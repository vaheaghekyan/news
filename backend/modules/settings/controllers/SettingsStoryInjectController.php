<?php

namespace backend\modules\settings\controllers;

use Yii;
use backend\modules\settings\models\SettingsStoryInject;
use backend\modules\settings\models\search\SettingsStoryInjectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\AccessRule;
use yii\filters\AccessControl;
use backend\models\User;

/**
 * SettingsStoryInjectController implements the CRUD actions for SettingsStoryInject model.
 */
class SettingsStoryInjectController extends Controller
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
                        'actions' => ['index', 'update', 'create', 'delete'],
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN, User::ROLE_MARKETER],
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
     * Lists all SettingsStoryInject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SettingsStoryInjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SettingsStoryInject model.
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
     * Creates a new SettingsStoryInject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SettingsStoryInject();

        if ($model->load(Yii::$app->request->post()))
        {
            //get the last story inject for this edition and lang and check min divergence between frequency
            $last=SettingsStoryInject::find()
            ->where(['language_id'=>$model->language_id, 'country_id'=>$model->country_id])
            ->orderBy('id DESC')
            ->limit(1)
            ->one();

            if(!empty($last))
            {
                //check if this type already exists, if user is trying to add duplicate
                if($last->type==$model->type)
                {
                    $msg=Yii::t('app', 'This type of story inject already exists');
                    Yii::$app->session->setFlash('warning', $msg);
                    return $this->redirect(['create']);
                }

                //check for frequency divergence
                $freq=[$last->frequency, $model->frequency];
                if((max($freq) - min($freq)) < SettingsStoryInject::FREQUENCY_DIVERGENCE)
                {
                    $msg=Yii::t('app', 'Min divergence between stories should be')." ".SettingsStoryInject::FREQUENCY_DIVERGENCE;
                    Yii::$app->session->setFlash('warning', $msg);
                    return $this->redirect(['create']);
                }
            }
            $model->save();
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
     * Updates an existing SettingsStoryInject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()))
        {
            //get the last story inject for this edition and lang and check min space between frequency
            $last=SettingsStoryInject::find()->where(['language_id'=>$model->language_id, 'country_id'=>$model->country_id])->one();
            if(!empty($last))
            {
                 //check for frequency divergence
                $freq=[$last->frequency, $model->frequency];
                if((max($freq) - min($freq)) < SettingsStoryInject::FREQUENCY_DIVERGENCE)
                {
                    $msg=Yii::t('app', 'Min divergence between stories should be')." ".SettingsStoryInject::FREQUENCY_DIVERGENCE;
                    Yii::$app->session->setFlash('warning', $msg);
                    return $this->redirect(['create']);
                }
            }
            $model->save();
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
     * Deletes an existing SettingsStoryInject model.
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
     * Finds the SettingsStoryInject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SettingsStoryInject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SettingsStoryInject::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
