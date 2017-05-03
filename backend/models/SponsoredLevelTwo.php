<?php

namespace backend\models;

use Yii;
use backend\models\SponsoredStory;
/**
 * This is the model class for table "sponsored_level_two".
 *
 * @property integer $id
 * @property integer $story_id
 * @property string $text
 * @property string $company_name
 * @property string $title
 * @property string $logo
 * @property string $stock_quote
 * @property string $image_file
 * @property string $caption
 * @property string $paragraph_one
 * @property string $paragraph_two
 * @property string $paragraph_three
 * @property integer $image_position
 * @property string $date_created
 * @property integer $type
 * @property string $wufoo_code
 *
 * @property Stories $story
 */
class SponsoredLevelTwo extends \yii\db\ActiveRecord
{

    const IMAGE_POS_1=0;
    const IMAGE_POS_2=1;
    const IMAGE_POS_3=2;

    //$insert - Whether this method called while inserting a record. If false, it means the method is called while updating a record.
    public function beforeSave($insert)
    {
        //saving
        if (parent::beforeSave($insert))
        {
            //if this is true it means user is creating model, if false user is updating
            if($insert==true)
            {
                $this->date_created=date("Y-m-d");
            }
            return true;
        }
        //updating
        else
        {
            return false;
        }
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sponsored_level_two';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sponsored_story_id', 'type'], 'required'],
            [['sponsored_story_id', 'image_position', 'type'], 'integer'],
            [['text', 'paragraph_one', 'paragraph_two', 'paragraph_three', 'wufoo_code'], 'string'],
            [['date_created'], 'safe'],
            [['company_name', 'logo', 'image_file', 'caption'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 50],
            [['stock_quote'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sponsored_story_id' => Yii::t('app', 'Story ID'),
            'text' => Yii::t('app', 'Text'),
            'company_name' => Yii::t('app', 'Company Name'),
            'title' => Yii::t('app', 'Title'),
            'logo' => Yii::t('app', 'Logo'),
            'stock_quote' => Yii::t('app', 'Stock Quote'),
            'image_file' => Yii::t('app', 'Image File'),
            'caption' => Yii::t('app', 'Caption'),
            'paragraph_one' => Yii::t('app', 'Paragraph One'),
            'paragraph_two' => Yii::t('app', 'Paragraph Two'),
            'paragraph_three' => Yii::t('app', 'Paragraph Three'),
            'image_position' => Yii::t('app', 'Image Position'),
            'date_created' => Yii::t('app', 'Date Created'),
            'type' => Yii::t('app', 'Type'),     //Not all sponsored stories will have a level 2 and level 3. Only the ones we tag as such. For now, let's tag these stories "IA" (for investor acquisition) in the CMS. In other words, when a sponsored story is tagged as "IA", the system should know  that 2 more levels are part of the campaign. If 'IA" is not checked, it will remain a regular sponsored story.
            'wufoo_code' => Yii::t('app', 'Wufoo Code'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSponsoredStory()
    {
        return $this->hasOne(SponsoredStory::className(), ['id' => 'sponsored_story_id']);
    }

    /**
     * @inheritdoc
     * @return \backend\models\activequery\SponsoredLevelTwoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\SponsoredLevelTwoQuery(get_called_class());
    }

    /*
    *  sponsored story type
    */
    public static function sponsoredStoryType()
    {
        return
        [
            SponsoredStory::SPONSORED_TYPE_REGULAR=>Yii::t("app", "Regular"),
            SponsoredStory::SPONSORED_TYPE_IA=>Yii::t("app", "Investor acquisition"),
        ];
    }
}
