<?php

namespace frontend\components;

use Yii;
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\Helpers as CommonHelpers;
use frontend\components\Helpers;

/*
* Helper class for some extra functions I need all across projet
*/
class LinkGenerator
{
    /*
    * Link to uploaded image of story
    * $storyId - id in Story
    * $urlTo - do you want to show only url ("short"), url+html ("html") or full url ("full")
    * $url_params:
        seo_url/type/name/id/page/categoryid
    */
    /* $categoryid - id of specific subcategory /CategoriresLevelOne
    * $name="trending", name of subcategory or category
    * $type - category/subcategory
    *id - id in stories table
    *seo_url - from table stories
    *$page - 0 or generated in view
    */
    public static function linkStoryView($title, $url_params,$urlTo)
    {
        $params[]="/story/view";
        foreach($url_params as $k=>$v)
            $params[$k]=$v;

        $link=Url::to($params);
        if($urlTo=="short")
            return $link;
        else if($urlTo=="full")
            return Helpers::frontendDomain().$link;
        else //html
            return Html::a($title, $link, ['target'=>'_blank']);
    }

    /*
    *  link to index/story page with all parameters
    * $urlTo - do you want to show only url ("short"), url+html ("html") or full url ("full")
    * $url_params:
        categoryid/page/type/name
        categoryid/type/name
    */
    public static function linkStoryIndex($title, $url_params, $urlTo)
    {
        $params[]="/story/index";
        foreach($url_params as $k=>$v)
            $params[$k]=$v;

        $link=Url::to($params);
        if($urlTo=="short")
            return $link;
        else if($urlTo=="full")
            return Helpers::frontendDomain().$link;
        else //html
            return Html::a($title, $link);
    }
}