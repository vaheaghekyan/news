<?php

namespace frontend\components;

use yii\base\Component;
use yii\helpers\Url;
use backend\models\Language;
use backend\models\Country;
use common\components\Helpers as CommonHelpers;
use Yii;

/*
* this component is being loaded before every other controller.
* This is used for example when language has to be loaded.
*/
class OnLoad extends Component
{

    public function init()
    {
        //set user-id cookie (google analytics)
        self::setUserIdCookie();
        return parent::init();
    }

    /*
    *  set user-id cookie for google analytics
    */
    private static function setUserIdCookie()
    {
        $cookie_name=\Yii::$app->params['frontend_user_id_cookie'];
        $cookie=CommonHelpers::getCookie($cookie_name);
        if($cookie==NULL)
        {
            $cookie_value=md5(uniqid(rand(), true));
            CommonHelpers::createCookie($cookie_name, $cookie_value, $expire=NULL);
        }
    }



   
}