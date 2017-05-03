<?php

namespace backend\modules\api\models;

use Yii;
use backend\components\Helpers as BackendHelpers;
use backend\models\Story;
use frontend\components\LinkGenerator;
use frontend\components\Helpers as FrontendHelpers;

class StoryApi
{
    const STORY_LIMIT=400;

    /*
    * generate url to video so you can create json for video
    */
    public static function urlToVideo($story)
    {
        if($story->video!=NULL)
        {
            return BackendHelpers::backendDomain()."/api/story/video?storyId=".$story->id;
        }
        else
            return NULL;
    }

    /*
    * format video in json format
    * $story - loaded Story model
    */
    public static function formatVideo($story)
    {
        $video=$story->getVideo();
        if($video!=NULL)
        {
            $return=
            [
                'content'=>
                [
                   [
                        'url'=>$video,
                        'autoplay'=>true,
                        "controls"=>
                        [
                            "playedTrackColor"=>"6F9D30"
                        ]
                   ]
                ]
            ];
            return $return;
        }
        return NULL;
    }

    /*
    * return array
    * return story content
    * $story - Story model
    * $i - index for story, replaces story->id
    * $deeplinkCategory - subcategory id and subcategory name for deepling
    */
    public static function storyContent($story, $countryIds, $categoryIds, $i, $deeplinkCategory)
    {
        $width      = Story::THUMB_WIDTH;
        $height     = Story::THUMB_HEIGHT;
        $video=StoryApi::urlToVideo($story);

        //url params
        $url_params=[
        "id"=>$story->id,
        "seo_url"=>$story->seo_url,
        "categoryid"=>$deeplinkCategory['subcategory_id'],
        "page"=>0,
        "type"=>"subcategory",
        "name"=>FrontendHelpers::generateSubcategoryName($deeplinkCategory["subcategory_name"])
        ];

        return
        [
            'id'                => $i,
            'title'             => trim($story->title),
            'date_created'      => $story->date_published, //this time is used to sort news
            'date_modified'     => $story->date_modified,
            'date_published'    => $story->date_published,
            'site_title'        => $story->seo_title,
            'description'       => $story->description,
            'link'              => trim($story->link),
            'image'             => $story->getImage($width, $height, $story),
            'video'             => $video,
            'countries'         => $countryIds,
            'categories'        => $categoryIds,
            'author'            => $story->relationUser->name,
            'deeplink'          => LinkGenerator::linkStoryView(NULL, $url_params, "full"),
            'sponsored'         => $story->sponsored_story

        ];
    }

    /*
    *  return query for story
    */
   /* public static function storiesQuery($languageId, $categoryId)
    {
        $tableStory=Story::tableName();
        $tableCategoryStory=CategoryStory::tableName();

        return
        Story::find()
        ->where(["$tableStory.status" => Story::STATUS_PUBLISHED, "$tableStory.language_id" => $languageId])
        ->andWhere(["$tableCategoryStory.category_id"=>$categoryId])
        ->andWhere("type=:image OR type=:video", [':image'=>Story::TYPE_IMAGE, ':video'=>Story::TYPE_VIDEO])
        ->orderBy(['date_published' => SORT_DESC])
        ->joinWith(['relationCountries','relationSubCategories.relationParentCategory', 'relationUser'])
        ->limit(StoryApi::STORY_LIMIT)
        ->all();
    } */
}

