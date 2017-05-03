<?php

namespace backend\modules\settings\controllers;

use Yii;
use backend\modules\settings\models\SettingsSocialNetworks;
use backend\modules\settings\models\search\SettingsSocialNetworksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\AccessRule;
use yii\filters\AccessControl;
use backend\models\User;

/**
 * SettingsSocialNetworksController implements the CRUD actions for SettingsSocialNetworks model.
 */
class SettingsSocialNetworksController extends Controller
{
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
                        'actions' => ['index', 'update', 'create'],
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN, User::ROLE_SUPERADMIN],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all SettingsSocialNetworks models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SettingsSocialNetworksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SettingsSocialNetworks model.
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
     * Creates a new SettingsSocialNetworks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SettingsSocialNetworks();

        if ($model->load(Yii::$app->request->post()) )
        {
            $country=$model->country_id;
            foreach($_POST["SettingsSocialNetworks"]["social_network"] as $value)
            {
                $model = new SettingsSocialNetworks();
                $model->social_network=$value;
                $model->country_id=$country;
                $model->save();
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
     * Updates an existing SettingsSocialNetworks model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        //return all social_network and put it in "social_network" attribute so checkbox list can be checked
        $model2 = SettingsSocialNetworks::find()->select('social_network')->where(['country_id'=>$model->country_id])->all();
        foreach($model2 as $value)
            $selected[]=$value->social_network;

        $model->social_network=$selected;

        if ($model->load(Yii::$app->request->post()))
        {
             $country=$model->country_id;

            //remove everything related to specific country so you can add new entries
            SettingsSocialNetworks::deleteAll(['country_id' => $country]);

            foreach($_POST["SettingsSocialNetworks"]["social_network"] as $value)
            {
                $model = new SettingsSocialNetworks();
                $model->social_network=$value;
                $model->country_id=$country;
                $model->save();
            }
            return $this->redirect(['index']);
        }
        else
        {
           return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SettingsSocialNetworks model.
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
     * Finds the SettingsSocialNetworks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SettingsSocialNetworks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SettingsSocialNetworks::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
