<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "category_stories".
 *
 * @property integer $category_id
 * @property integer $story_id
 *
 * @property Categories $category
 * @property Stories $story
 */
class CategoryStory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_stories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'story_id'], 'required'],
            [['category_id', 'story_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'category_id' => 'Category ID',
            'story_id' => 'Story ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCategory()
    {
        return $this->hasOne(CategoriesLevelOne::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationStory()
    {
        return $this->hasOne(Story::className(), ['id' => 'story_id']);
    }


}
