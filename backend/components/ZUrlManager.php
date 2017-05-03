<?php
/**
 * Created by PhpStorm.
 * User: Oleksii
 * Date: 08.06.2015
 * Time: 13:55
 */

namespace backend\components;
use yii\web\UrlManager;
use backend\models\User;
use Yii;
use backend\models\Language;
use backend\components\Helpers;


class ZUrlManager extends UrlManager
{
    public function createUrl($params)
    {
        if (!isset($params['language']))
        {

            /*if (Yii::$app->session->has(Language::COOKIE_KEY))
                Yii::$app->language = Yii::$app->session->get(Language::COOKIE_KEY);

            else if(isset(Yii::$app->request->cookies[Language::COOKIE_KEY]))
                Yii::$app->language = Yii::$app->request->cookies[Language::COOKIE_KEY]->value;*/

            //for admins and superadmins always put english lang
            Helpers::setLanguage();

            $params['language'] = Yii::$app->language;

        }
        return parent::createUrl($params);
    }
}