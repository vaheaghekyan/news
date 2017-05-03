<?php
namespace backend\components;

use Yii;

class MyMixpanel
{
    const TOKEN="52821a0b90594f32db2c1b525316303f";
    const PROPERTY_CMS_TYPE_NEW="Created";
    const PROPERTY_CMS_TYPE_PUBLISHED="Published";

    /*
    *  $model - newly created user (User model)
    */
    public static function addNewUser($model)
    {
        $mp = new \Mixpanel(self::TOKEN);
        $mp->people->set($model->id, array(
            '$first_name'       => $model->name,
            '$email'            => $model->email,
            'Date registered'   => $model->date,
        ));
    }

    /*
    * track new stories per user
    * $property -> property name
    */
    public static function TrackNewStories($property)
    {
        $mp = new \Mixpanel(self::TOKEN);
        // associate a user id to subsequent events
        $mp->identify(Yii::$app->user->getId());
        // track event
        $mp->track("New Stories", ["CMS Type"=>$property]);
    }

    /*
    * track stories per category
    * $categoryName - category level one name
    */
    public static function TrackNewStoriesCategory($categoryName)
    {
        $mp = new \Mixpanel(self::TOKEN);
        // associate a user id to subsequent events
        $mp->identify(Yii::$app->user->getId());
        // track event
        $mp->track("New Stories", array("CMS Category"=>$categoryName));
    }

    /*
    * track stories per country
    * $countryName - country name
    */
    public static function TrackNewStoriesCountry($countryName)
    {
        $mp = new \Mixpanel(self::TOKEN);
        // associate a user id to subsequent events
        $mp->identify(Yii::$app->user->getId());
        // track event
        $mp->track("New Stories", array("CMS Country"=>$countryName));
    }

    /*
    * track deleted stories
    */
    public static function TrackNewStoriesPublished()
    {
        $mp = new \Mixpanel(self::TOKEN);
        // associate a user id to subsequent events
        $mp->identify(Yii::$app->user->getId());
        // track event
        $mp->track("News Per Category", ["CMS tip"=>"Deleted"]);
    }

    /*
    * track edited stories per user
    */
    /*public static function TrackEditedStories()
    {
        $mp = new \Mixpanel(self::TOKEN);
        // associate a user id to subsequent events
        $mp->identify(Yii::$app->user->getId());
        // track event
        $mp->track("Edited Stories");
    } */


}
?>