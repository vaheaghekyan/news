<?php
namespace backend\controllers;

use yii\web\Controller;
use backend\models\TimezoneUser;
use yii\helpers\Url;
use backend\models\Language;
use backend\models\Story;
use Yii;

/*
* custom controller to easily handle beforeAction that needs to be the same for every controller
*/
class MyController extends Controller
{
    public function beforeAction($action)
    {
        //get controller and action
        $action_tmp=Yii::$app->controller->action->id;
        $controller_tmp=Yii::$app->controller->id;
        $url=$controller_tmp."/".$action_tmp;
        //because UserController uses MyController and add-timezone should be free to access
        if($action_tmp!="add-timezone")
           $this->checkTimeZone();

        //check if user has error in story (image/video are not well processed) and warn them, don't let them do anything else while error is there
        $allowedUrls=["story/update", "admin/index", "story/upload-image", "story/view", "story/delete", "story/delete-temp", "story/auto-save"];
        if(!in_array($url, $allowedUrls))
        {
            $storiesNoImgVid=Story::errorStories();

            if(count($storiesNoImgVid) > 0)
                \Yii::$app->response->redirect(Url::to(['/admin/index']), 301)->send();
        }

        return parent::beforeAction($action);
    }

    /*
    * set timezone and check if user added time zone
    */
    private function checkTimeZone()
    {
        $cookies = Yii::$app->request->cookies;
        $backend_timezone_cookie = $cookies->get(\Yii::$app->params['backend_timezone_cookie']);//get "timezone" cookie

        if($backend_timezone_cookie==NULL)
        {
            $query=TimezoneUser::find()->where(['user_id'=>Yii::$app->user->getId()])->joinWith(['relationTimezone'])->count();
            //check if user added imezone
            if($query == 0)
            {
                \Yii::$app->response->redirect(Url::to(['/user/add-timezone']), 301)->send();
                exit();
            }
        }
    }
}