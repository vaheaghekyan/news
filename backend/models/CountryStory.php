<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "country_stories".
 *
 * @property integer $story_id
 * @property integer $country_id
 *
 * @property Countries $country
 * @property Stories $story
 */
class CountryStory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country_stories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'country_id'], 'required'],
            [['story_id', 'country_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'=>'id',
            'story_id' => 'Story ID',
            'country_id' => 'Country ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationStory()
    {
        return $this->hasOne(Story::className(), ['id' => 'story_id']);
    }


    /*
    *  check if specific country is inserted into CountryStory so you can set "checked" in checkbox
    *   $countryId - id in countries
    *   $storyId - id in stories
    */
    public static function hasCategory( $countryId, $storyId )
    {
        return (CountryStory::find()->where(array("country_id" => $countryId, 'story_id'=>$storyId))->count() > 0 ? true : false);
    }
}
