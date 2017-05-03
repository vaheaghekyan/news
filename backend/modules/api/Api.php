<?php

namespace backend\modules\api;

class Api extends \yii\base\Module
{
    public $controllerNamespace     = 'backend\modules\api\controllers';

    public $enableCsrfValidation    = false;

    public $defaultRoute            = 'index';


    public $layout                  = false;

    public function init()
    {
        parent::init();

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //this is for scheduled stories, they have to be calculated by UTC
        date_default_timezone_set("UTC");
    }
}
