<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "story_3rd_party_video".
 *
 * @property integer $id
 * @property integer $story_id
 * @property string $video_code
 *
 * @property Stories $story
 */
class Story3rdPartyVideo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story_3rd_party_video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'video_code'], 'required'],
            [['story_id'], 'integer'],
            [['video_code'], 'string']
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
            'video_code' => Yii::t('app', 'Video Code'),
        ];
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
     * @return \backend\models\activequery\Story3rdPartyVideoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\Story3rdPartyVideoQuery(get_called_class());
    }
}
