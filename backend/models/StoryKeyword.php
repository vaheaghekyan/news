<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%story_keyword}}".
 *
 * @property integer $id
 * @property integer $story_id
 * @property string $keywords
 *
 * @property Stories $story
 */
class StoryKeyword extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story_keyword';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'keywords'], 'required'],
            [['story_id'], 'integer'],
            [['keywords'], 'string']
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
            'keywords' => Yii::t('app', 'Keywords'),
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
     * @return \backend\models\activequery\StoryKeywordQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\StoryKeywordQuery(get_called_class());
    }
}
