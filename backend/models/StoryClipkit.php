<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "story_clipkit".
 *
 * @property integer $id
 * @property integer $story_id
 * @property string $clipkit_code
 *
 * @property Stories $story
 */
class StoryClipkit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story_clipkit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'clipkit_code'], 'required'],
            [['story_id'], 'integer'],
            [['clipkit_code'], 'string']
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
            'clipkit_code' => Yii::t('app', 'Clipkit Code'),
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
     * @return \backend\models\activequery\StoryClipkitQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\StoryClipkitQuery(get_called_class());
    }
}
