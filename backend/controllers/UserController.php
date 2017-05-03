<?php
/**
 * Created by PhpStorm.
 * User: alekseyyp
 * Date: 19.06.15
 * Time: 11:40
 */

namespace backend\controllers;

use backend\controllers\MyController;
use backend\models\UserForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\components\DataTable;
use backend\models\Story;
use backend\models\Language;
use backend\models\CategoryStory;
use backend\models\CountryStory;
use backend\models\Category;
use backend\models\Country;
use backend\models\UserLanguage;
use backend\models\User;
use backend\models\search\UserSearch;
use backend\components\AccessRule;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use backend\models\TimezoneUser;
use backend\components\MyMixpanel;
use yii\base\Security;

class UserController extends MyController
{

    public $layout = "admin";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'actions' => ['find', 'index', 'add-timezone','dependent-dropdown'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete', 'create', 'update', 'get', 'activate-user', 'disactivate-user'],
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN, User::ROLE_ADMIN],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index'     => ['get'],
                    'find'      => ['get'],
                    'delete'    => ['delete'],
                    'get'       => ['get'],
                    'update'    => ['get', 'post'],
                    'create'    => ['post'],
                    'activate-user'  => ['post'],
                    'disactivate-user'  => ['post']
                ],
            ],
        ];
    }


    /*
    * used in creating story where admin and superadmin can list users by language
    */
    /*public function actionDependentDropdown()
    {
        if(isset($_POST["language_id"]))
        {
            $language_id=(int)$_POST["language_id"];
            //select all users who have this specific language
            $query=UserLanguage::find()->where(['language_id'=>$language_id])->with(['relationUser'])->all();
            $i=0;
            foreach($query as $value)
            {
                $array[$i]['id']=$value->relationUser->id;
                $array[$i]['name']=$value->relationUser->name;
                $i++;
            }

            return json_encode(['result'=>$array]);
        }
    }*/


    /*
    *  activate user
    * $id - id in users
    */
    public function actionActivateUser($id)
    {
        $query=User::findOne($id);
        $query->status=1;
        if($query->save())
            Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
        else
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Something was wrong'));

        $this->redirect("/user/index");
    }

    /*
    *  idsactivate user
    * $id - id in users
    */
    public function actionDisactivateUser($id)
    {
        $Security = new Security();

        $query=User::findOne($id);
        $query->status=0;
        $query->auth_key=$Security->generateRandomString();
        if($query->save())
            Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
        else
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Something was wrong'));

        $this->redirect("/user/index");
    }

    /*
    * make user add his/her timezone. If he don't add timezone, he cannot do anything else
    */
    public function actionAddTimezone()
    {
        //if imezone exissts for this user rediret him
        $query=TimezoneUser::find()->where(['user_id'=>Yii::$app->user->getId()])->count();
        if($query>=1)
              return $this->redirect('/admin/index');
              
        $model = new TimezoneUser();

        if ($model->load(Yii::$app->request->post()))
        {
            $model->user_id=Yii::$app->user->getId();
            if($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
                return $this->redirect(['admin/index']);
            }
            else
            {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'Something was wrong'));
                return $this->redirect(['create']);
            }
        }
        else
        {
            return $this->render('timezone/create', [
                'model' => $model,
            ]);
        }
    }

    /*
    *  List all users
    */
    public function actionIndex()
    {
        //when adding user list all languages that are available so you can assign them to user
        $languages  = Language::find()->orderBy('name ASC')->all();
        $list       = [];
        foreach ( $languages as $language )
        {

            $list[] = [
                'id'    => $language->id,
                'name'  => $language->name
            ];

        }

        //search all users
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render("index", [
            'languages'     => $list,
             'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGet()
    {
        /* @var $user \backend\models\User */
        if ( ( $userId = Yii::$app->request->get("id") ) &&
            ( $user = User::findOne( $userId ) )  ) {

            $languages  = $user->getLanguages()->all();
            $list       = [];
            foreach ( $languages as $language ) {

                $list[] = $language->id;

            }
            $data = [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'image'     => $user->getImage(),
                'languages' => $list,
                'oldEmail'  => $user->email
            ];
            echo json_encode( $data );
            Yii::$app->end();
        }

    }

    public function actionCreate()
    {
        $userForm = new UserForm();
        $userForm->scenario = "create";
        $data = [];
        if ( $userForm->load(Yii::$app->request->post()) )
        {

            $imageFile = UploadedFile::getInstanceByName("image");
            if ($imageFile)
            {

                if ($imageFile->error == UPLOAD_ERR_FORM_SIZE || $imageFile->error == UPLOAD_ERR_INI_SIZE) {

                    $userForm->addError("upload_image_field", "Image is too big, max file size is " . ini_get("upload_max_filesize") . "b");

                } elseif (!in_array($imageFile->getExtension(), ['jpg', 'jpeg', 'png', 'gif']) /*||
                    !in_array(FileHelper::getMimeType($imageFile->tempName), ['image/gif', 'image/jpeg', 'image/png'])  */ ) {

                    $userForm->addError("upload_image_field", "Image file has a wrong format. Allowed formats are: jpg, jpeg, png, gif");

                }

            }
            if (!$userForm->hasErrors() && $userForm->validate())
            {
                $user = $userForm->create();

                //***********MIXPANEL*************
                if(!empty($user))
                {
                    MyMixpanel::addNewUser($user);
                }

                if ($imageFile)
                {
                    //$ext = pathinfo($imageFile->name, PATHINFO_EXTENSION);
                    $imagePath = Yii::getAlias('@webroot').User::PATH_IMAGE . $user->id . ".jpg";
                    $imageFile->saveAs($imagePath);
                }
                $data['success'] = true;
            }
            else
            {

                $data['errors'] = [];
                $errors = $userForm->getErrors();
                foreach ( $errors as $error ) {

                    $data['errors'][] = $error[0];

                }
            }

        }
        else
        {

            ob_clean();
            $userForm->addError("upload_image_field", "Image is too big, max file size is " . ini_get("upload_max_filesize") . "b");
            $errors = $userForm->getErrors();
            foreach ( $errors as $error ) {

                $data['errors'][] = $error[0];

            }

        }
        echo json_encode( $data );
        Yii::$app->end();

    }

    public function actionDelete()
    {
        if (($userId = Yii::$app->request->post("userId"))) {

            $user = User::findOne($userId);
            $user->delete();
            return json_encode(array("success" => true));

        }

    }

    public function actionUpdate()
    {

        set_time_limit(3000);
        $userForm = new UserForm();
        $data = [];
        if ( $userForm->load(Yii::$app->request->post()) )
        {

            $imageFile = UploadedFile::getInstanceByName("image");
            if ( $imageFile)
            {

                if ($imageFile->error == UPLOAD_ERR_FORM_SIZE || $imageFile->error == UPLOAD_ERR_INI_SIZE) {

                    $userForm->addError("upload_image_field", "Image is too big, max file size is " . ini_get("upload_max_filesize") . "b");

                }
                elseif (!in_array($imageFile->getExtension(), ['jpg', 'jpeg', 'png', 'gif']) /*||
                    !in_array(FileHelper::getMimeType($imageFile->tempName), ['image/gif', 'image/jpeg', 'image/png']) */
                )
                {

                    $userForm->addError("upload_image_field", "Image file has a wrong format. Allowed formats are: jpg, jpeg, png, gif");

                }

            }
            if ( !$userForm->hasErrors() && $userForm->validate()  )
            {

                $user = $userForm->update();
                if ($imageFile) {

                    //$ext = pathinfo($imageFile->name, PATHINFO_EXTENSION);
                    $imagePath = Yii::getAlias('@webroot'). User::PATH_IMAGE . $user->id . ".jpg";
                    $imageFile->saveAs($imagePath);

                }
                $data['success'] = true;

            }
            else
            {

                $data['errors'] = [];
                $errors = $userForm->getErrors();
                foreach ( $errors as $error ) {

                    $data['errors'][] = $error[0];

                }

            }


        }
        else
        {

            ob_clean();
            $userForm->addError("upload_image_field", "Image is too big, max file size is " . ini_get("upload_max_filesize") . "b");
            $errors = $userForm->getErrors();
            foreach ( $errors as $error ) {

                $data['errors'][] = $error[0];

            }

        }
        echo json_encode( $data );
        Yii::$app->end();

    }


}