<?php

namespace backend\models;

use Yii;
use backend\models\CategoryStory;
use backend\models\Category;

/**
 * This is the model class for table "{{%categories_level_one}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_category
 *
 * @property Categories $parentCategory
 * @property CategoryStories[] $categoryStories
 */
class CategoriesLevelOne extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'categories_level_one';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'parent_category', 'order_by'], 'required'],
            [['parent_category', 'order_by'], 'integer'],
            [['name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'parent_category' => Yii::t('app', 'Parent Category'),
            'order_by' => 'Order By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationParentCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_category']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCategoryStories()
    {
        return $this->hasMany(CategoryStory::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationStories()
    {
        $tableCategoriesStories=CategoryStory::tableName();//category_stories
        return $this->hasMany(Story::className(), ['id' => 'story_id'])->viaTable($tableCategoriesStories, ['category_id' => 'id']);
    }
        /**
     * @inheritdoc
     * @return \backend\models\activequery\CategoriesLevelOneQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\CategoriesLevelOneQuery(get_called_class());
    }

    /**
     * This returns the number of stories in the current category
     * @return int|string
     */
    public function numberOfStories()
    {
        $tableStory=Story::tableName();
        $tableCategoryStory=CategoryStory::tableName();

        return CategoryStory::find()
                ->leftJoin($tableStory, $tableCategoryStory.".story_id=".$tableStory.".id")
                ->where([$tableCategoryStory.".category_id" => $this->id, $tableStory.".status" => Story::STATUS_PUBLISHED])
                ->count();

    }

    /*
    *  find all level one categories depending on parent category, return as array
    * can be used for query like: IN(2,3,15...)
    * used ing frontend/StorySearch.php
    * $parent_id - id in "Category"
    */
    public static function getChildrenByParent($parent_id)
    {
        $query = CategoriesLevelOne::find()->select('id')->where(["parent_category"=>$parent_id])->all();
        foreach($query as $value)
        {
            $array[]=$value->id;
        }

        return $array;
    }


    /*
    * get SPONSORED category
    */
    public static function getSponsoredCategory()
    {
        $tableCategory=Category::tableName();
        return CategoriesLevelOne::find()->where(["$tableCategory.name"=>'SPONSORED'])->joinWith(['relationParentCategory'])->one();
    }
}
