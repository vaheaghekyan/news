<?php

namespace backend\controllers;

use Yii;
use backend\components\Helpers;
use backend\models\CountryExt;
use backend\models\CountryLanguage;
use backend\models\Language;
use backend\models\LanguagesAll;
use backend\models\search\LanguageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use backend\models\User;
use yii\db\Exception;

/**
 * LanguageController implements the CRUD actions for Language model.
 */
class LanguageController extends Controller
{
    public function behaviors()
    {
        return [
            'access' =>
            [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'actions' => ['create', 'delete', 'index'],
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Language models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LanguageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Language model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Language model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Language();

        if ($model->load(Yii::$app->request->post()))
        {
            //find language name
            $query=LanguagesAll::find()
            ->where(['code'=>$model->code])
            ->one();

            $model->name=$query->name;
            if($model->save())
            {
                //Pick all countries for this new language. Because: if I added Belgium as country and I only had German language added to CMS, only german will be added for Belgium. If I add french and dutch later they have to be assigned to Belgium
                $countryExt=CountryExt::find()
                    ->where(['LIKE', 'languages', $model->code])
                    ->joinWith('relationCountry', true, "RIGHT JOIN")
                    ->all();
                foreach($countryExt as $value)
                {
                    //just add new language for specific country, because this language wasn't added for country because it wasn't in database
                    $CountryLanguage = new CountryLanguage;
                    $CountryLanguage->country_id=$value->relationCountry->id;
                    $CountryLanguage->language_id=$model->id;
                    $CountryLanguage->save();
                }

                //CMS lang
                $messages_dir_en=Language::messageDir("en");
                $messages_dir=Language::messageDir($model->code);

                //Web version lang
                $messages_web_dir_en=Language::frontendMessageDir("en");
                $messages_web_dir=Language::frontendMessageDir($model->code);

                //android lang
                $messages_android_dir_en=Language::messageDir("en/android");
                $messages_android_dir=Language::messageDir("$model->code/android");

                //ios lang
                $messages_ios_dir_en=Language::messageDir("en/ios");
                $messages_ios_dir=Language::messageDir("$model->code/ios");


                //-----------CMS LANG FILES----------------
                $this->copyFiles($messages_dir_en, $messages_dir);

                //-----------WebVersion LANG FILES----------------
                $this->copyFiles($messages_web_dir_en, $messages_web_dir);

                //-----------ANDROID LANG FILES---------------
                $this->copyFiles($messages_android_dir_en, $messages_android_dir);

                 //-----------iOS LANG FILES---------------
                $this->copyFiles($messages_ios_dir_en, $messages_ios_dir);

            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
            return $this->render('create', [
                'model' => new Language(),
            ]); 
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /*
    * copy and create files in a directory of a newly created language
    */
    private function copyFiles($messages_dir_en, $messages_dir)
    {
        //create new folder for that language and copy all files
        mkdir($messages_dir,0755,true);
        //Open en directory to copy files from
       $directory = opendir($messages_dir_en);
       //Scan through the folder one file at a time

       while(($file = readdir($directory)) != false)
       {
            //Copy only app.php
            if($file=="app.php")
                copy($messages_dir_en.$file, $messages_dir.$file);
            //create new files
            else if(!is_dir($messages_dir_en.$file))
            {
                $txt = "<?php return []; ?>";
                file_put_contents($messages_dir.$file , $txt);
            }
       }
    }

    /**
     * Updates an existing Language model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Language model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try
        {
            $model=$this->findModel($id);
            if($model->delete())
            {
                //delete android lang files
                $messages_android_dir=Language::messageDir("$model->code/android");
                Helpers::deleteFilesAndFolder($messages_android_dir);

                //delete iOS lang files
                $messages_ios_dir=Language::messageDir("$model->code/ios");
                Helpers::deleteFilesAndFolder($messages_ios_dir);

                //delete cms lang files
                $messages_dir=Language::messageDir($model->code);
                Helpers::deleteFilesAndFolder($messages_dir);

                //delete web version lang files
                $messages_dir=Language::frontendMessageDir($model->code);
                Helpers::deleteFilesAndFolder($messages_dir);
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
        }
        catch (Exception $e)
        {
            throw new \yii\web\HttpException(403, Yii::t('app', 'You cannot delete language'));
        }


        return $this->redirect(['index']);
    }



    /**
     * Finds the Language model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Language the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Language::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
