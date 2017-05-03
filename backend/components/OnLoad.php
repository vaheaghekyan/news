<?php

namespace backend\components;

use yii\base\Component;
use yii\helpers\Url;
use backend\models\Language;
use backend\models\TimezoneUser;
use backend\models\User;
use common\components\Helpers as CommonHelpers;
use backend\components\Helpers;
use Yii;

/*
* this component is being loaded before every other controller.
* This is used for example when language has to be loaded.
*/
class OnLoad extends Component
{

    public function init()
    {

        //always set default timezone if possible
        //$this->setDefaultTimezone();
        date_default_timezone_set("UTC");

        //set language, you have to set it here so everything is translated on page
        Helpers::setLanguage();

        return parent::init();
    }


    //always set default timezone if possible
    private function setDefaultTimezone()
    {
        //get "timezone" cookie
        $backend_timezone_cookie = CommonHelpers::getCookie(\Yii::$app->params['backend_timezone_cookie']);

        //if it doesn't exist use query to set timezone and cookie
        if($backend_timezone_cookie==NULL)
        {
            $query=TimezoneUser::find()->where(['user_id'=>Yii::$app->user->getId()])->with(['relationTimezone'])->one();
            if($query)
            {
                //set cookie with that timezone so you don't have to do this query all overa again
                Yii::$app->response->cookies->add(new \yii\web\Cookie([
                    'name' => \Yii::$app->params['backend_timezone_cookie'],
                    'value' => $query->relationTimezone->timezone,
                    'expire' => time() + (60*60*24*365*10) //current time + 10 years
                ]));

                date_default_timezone_set($query->relationTimezone->timezone);
            }
            else
                date_default_timezone_set("UTC");
        }
        else
        {
            date_default_timezone_set($backend_timezone_cookie);
        }
    }


}