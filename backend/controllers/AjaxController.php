<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use backend\models\Language;
use backend\models\CategoriesLevelOne;
use backend\models\Category;
use backend\models\Country;
use backend\models\CountryStory;
use backend\models\Story;

use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\components\Helpers as CommonHelpers;
use frontend\components\LinkGenerator;
use frontend\components\Helpers;
use backend\components\Helpers as BackendHelpers;

/**
 * Ajax controller
 * used to  call actions from ajax function
 */
class AjaxController extends Controller
{

    public function beforeAction($action)
    {
        $action_tmp=Yii::$app->controller->action->id;
        //disable CSRF
        $this->enableCsrfValidation=false;
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['report-story'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                ],
            ],
        ];
    }


    /*
    * get latest story per country and language  for hash world map on landing page
    * you have to disable CSRF here because of POST
    */
    public function actionReportStory()
    {
        if(isset($_POST["storyid"]) && isset($_POST["message"]))
        {
            //find this sitry
            $story=Story::find()->where(['id'=>(int)$_POST["storyid"]])->with(['relationUser'])->one();
            $message=$_POST["message"];
            $message.='<br><b>Story:</b> '.$story->title;
            $user=Yii::$app->user->getIdentity();
            CommonHelpers::sendEmailToAnyone
            (
             "[MISTAKE] Story mistake",
             $message,
             $story->relationUser->email,
             $story->relationUser->name,
             $user
            );
            echo json_encode(["message"=>"true"]);
        }
        else
             echo json_encode(["message"=>"false"]);
    }


}
