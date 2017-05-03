<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sponsored_story".
 *
 * @property integer $ID
 * @property integer $story_id
 * @property integer $sponsored_type
 *
 * @property SponsoredLevelTwo[] $sponsoredLevelTwos
 * @property Stories $story
 */
class SponsoredStory extends \yii\db\ActiveRecord
{

    const SPONSORED_TYPE_REGULAR=0;
    const SPONSORED_TYPE_IA=1; // investor acquisition

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sponsored_story';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'sponsored_type'], 'required'],
            [['story_id', 'sponsored_type'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'story_id' => Yii::t('app', 'Story ID'),
            'sponsored_type' => Yii::t('app', 'Sponsored Type'), //type of sponsored story: httpool, regular, investor acquisition...
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationSponsoredLevelTwo()
    {
        return $this->hasOne(SponsoredLevelTwo::className(), ['sponsored_story_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Stories::className(), ['id' => 'story_id']);
    }

    /**
     * @inheritdoc
     * @return \backend\models\activequery\SponsoredStoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\SponsoredStoryQuery(get_called_class());
    }
}
