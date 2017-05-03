<?php

namespace backend\modules\preroll\controllers;

use Yii;
use backend\modules\preroll\models\AdsGeolocationTags;
use backend\modules\preroll\models\search\AdsGeolocationTagsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use backend\components\AccessRule;
use yii\filters\AccessControl;
use backend\models\User;
use common\components\Helpers;
use backend\modules\preroll\Preroll;
use backend\modules\preroll\models\AdsGeolocationTagCountry;

/**
 * PrerollController implements the CRUD actions for AdsGeolocationTags model.
 */
class PrerollController extends Controller
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
                        'actions' => ['index', 'delete', 'update', 'create', 'get-countries'],
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN, User::ROLE_MARKETER],
                    ],
                    [
                        'actions' => ['video'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'get-countries' => ['get'],
                ],
            ],
        ];
    }


    /*
    * this is demo page for preroll video
    */
    public function actionVideo()
    {
        //get user ip
        $userIp = $_SERVER['REMOTE_ADDR'];

        $db=Helpers::dbConnection();

        //get country code for that specific IP
        $getCountryCode = "
        SELECT countryCode
        FROM ".Preroll::TABLE_IP_COUNTRIES."
        WHERE
        INET_ATON('$userIp') BETWEEN beginNum AND endNum
        LIMIT 1
        ";
        $getCountryCode = $db->createCommand($getCountryCode)->queryOne();

        //get random vast tag for that country
        $getTag =
        "
        SELECT t.tagUrl
        FROM ".Preroll::TABLE_TAGS." t, ".Preroll::TABLE_COUNTRIES." c, ".Preroll::TABLE_TAG_COUNTRY." tc
        WHERE t.tagId = tc.tagId
        AND c.idCountry = tc.countryId
        AND c.countryCode = '".$getCountryCode["countryCode"]."'
        ORDER BY RAND()
        LIMIT 1
        ";
        $getTag = $db->createCommand($getTag)->queryOne();


        if( empty($getTag) )
        {
            $getTag =
            "
            SELECT t.tagUrl FROM ".Preroll::TABLE_TAGS." t, ".Preroll::TABLE_TAG_COUNTRY." tc
            WHERE t.tagId = tc.tagId
            ORDER BY RAND()
            LIMIT 1
            ";

            $getTag = $db->createCommand($getTag)->queryOne();
        }

        return $this->renderPartial("video", ['getTag'=>$getTag]);
    }

    /*
    *  get countries for token input
    */
    public function actionGetCountries()
    {
        if(isset($_GET["q"]))
        {
            $db=Helpers::dbConnection();
            $countries = $db->createCommand('
            SELECT * FROM '.Preroll::TABLE_COUNTRIES.'
            WHERE countryName LIKE "%'.$_GET["q"].'%"')
            ->queryAll();

            $return=[];
            $i=0;
            foreach($countries as $country)
            {
                $return[$i]["id"]=$country["idCountry"];
                $return[$i]["name"]=$country["countryName"];
                $i++;
            }
            echo json_encode($return);
        }

    }
    /**
     * Lists all AdsGeolocationTags models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdsGeolocationTagsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AdsGeolocationTags model.
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
     * Creates a new AdsGeolocationTags model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdsGeolocationTags();
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            $countryIDs=explode(",", $_POST["countries"]);
            foreach($countryIDs as $countryID)
            {
                $model2 = new AdsGeolocationTagCountry;
                $model2->tagId=$model->tagId;
                $model2->countryId=$countryID;
                $model2->save();
            }
            return $this->redirect(['index']);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AdsGeolocationTags model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $db=Helpers::dbConnection();


        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            //remove all previous countries for that tagId
            $db->createCommand("DELETE FROM ".Preroll::TABLE_TAG_COUNTRY." WHERE tagId=$model->tagId")
   ->execute();

            $countryIDs=explode(",", $_POST["countries"]);
            foreach($countryIDs as $countryID)
            {
                $model2 = new AdsGeolocationTagCountry;
                $model2->tagId=$model->tagId;
                $model2->countryId=$countryID;
                $model2->save();
            }
            return $this->redirect(['update', 'id'=>$model->tagId]);
        }
        else
        {
            $countries = $db->createCommand('
            SELECT * FROM '.Preroll::TABLE_TAG_COUNTRY.'
            LEFT JOIN '.Preroll::TABLE_COUNTRIES.' ON ('.Preroll::TABLE_TAG_COUNTRY.'.countryId='.Preroll::TABLE_COUNTRIES.'.idCountry)
            WHERE tagId='.$model->tagId.'')
            ->queryAll();

            return $this->render('update', [
                'model' => $model,
                'countries' => $countries,
            ]);
        }
    }

    /**
     * Deletes an existing AdsGeolocationTags model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AdsGeolocationTags model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdsGeolocationTags the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdsGeolocationTags::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
