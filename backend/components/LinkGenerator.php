<?php

namespace backend\components;

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Story;

/*
* used to generate different type of links
*/
class LinkGenerator
{
    //----------------------STORIES -----------------------------------------
    /*
    * Link to uploaded image of story
    * $storyId - id in Story
    */
    public static function linkImage($storyId)
    {
        $link=Url::toRoute(['site/downloadimage', 'storyId' => $storyId]);
        return Html::a('<i class="fa fa-image"></i>', $link, ['target'=>'_blank']);
    }

    /*
    * Link to uploaded video  of story
    * $storyId - id in Story
    */
    public static function linkVideo($storyId)
    {
        $link=Url::toRoute(['site/downloadvideo', 'storyId' => $storyId]);
        return Html::a('<i class="fa fa-video-camera"></i>', $link, ['target'=>'_blank']);
    }

    /*
    * Link to create story
    * $text - link's text
    */
    public static function linkStoryCreate($text, $options=[])
    {
        $link=Url::toRoute(['/story/create']);
        return Html::a($text, $link, $options);
    }

    /*
    * Link to unpuvlished story
    * $text - link's text
    */
    public static function linkStoryUnpublished($text, $options=[])
    {
        $link=Url::toRoute(['/story/unpublished']);
        return Html::a($text, $link, $options);
    }

    /*
    * Link to published story
    * $text - link's text
    */
    public static function linkStoryPublished($text, $options=[])
    {
        $link=Url::toRoute(['/story/published']);
        return Html::a($text, $link, $options);
    }

    /*
    * Link to pending story
    * $text - link's text
    */
    public static function linkStoryPending($text, $options=[])
    {
        $link=Url::toRoute(['/story/pending']);
        return Html::a($text, $link, $options);
    }
}
