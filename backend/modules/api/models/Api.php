<?php

namespace backend\modules\api\models;

use Yii;
use backend\models\Language;

class API
{
    //in categoryController and StoryController multiply order_by of Category with this number
    const ORDER_BY_MULTIPLY=1000;

    const MAX_PER_TRENDING=100;
    const MAX_PER_CATEGORY=30;
    const TRENDING_SUBCATEGORY=1;

    const STORY_CACHE_KEY="api_story_";//languageId_categoryId
    const STORY_SPONSORED_CACHE_KEY="api_sponsored_story_";//languageId
    const STORY_BY_COUNTRY_CACHE_KEY="api_story_by_country_";//languageId
    const LANG_CACHE_KEY="api_lang_";//languageId

    /*
    *  app is sending me language_id, for example: 3,7... find language code and set it
    */
    public static function setLanguage($language_id)
    {
        $lang=Language::findOne($language_id);
        if(empty($lang))
            $code="en";
        else
            $code=$lang->code;

        Yii::$app->language=$code;
    }

    /*
    * set language by code
    */
    public static function setLanguageByCode($code)
    {
        Yii::$app->language=$code;
    }
}
