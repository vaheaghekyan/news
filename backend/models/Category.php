<?php

namespace backend\models;

use Yii;
use backend\models\Language;
use backend\models\CategoryStory;
use backend\models\CategoryLevelOne;
use yii\helpers\ArrayHelper;
use backend\models\CategoriesLevelOne;
use yii\caching\DbDependency;

/**
 * This is the model class for table "categories".
 *
 * @property integer $id
 * @property string $name
 * @property integer $order_by
 * @property string $date


 * @property integer $language_id
 *
 * @property CategoryStories[] $categoryStories
 * @property Stories[] $stories
 */
class Category extends \yii\db\ActiveRecord
{
    //QUERY VARIABLES
    public $numberOfStories; // for query in category/index

    //alywas checked categories, because some categories always has to be checked: "Trending"
    public $always_checked=["Trending"=>1];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'order_by', 'language_id'], 'required'],
            [['order_by', 'language_id'], 'integer'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'order_by' => 'Order By',
            'date' => 'Date',
            'language_id' => 'Language ID',
        ];
    }




    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCategoriesLevelOne()
    {
        return $this->hasMany(CategoriesLevelOne::className(), ['parent_category' => 'id']);
    }


    /**
     * get parent categories, level 0
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getParents()
    {
        $tableCategoriesLevelOne=CategoriesLevelOne::tableName();
        $dependency = new DbDependency;
        $dependency->sql="SELECT MAX(id) FROM $tableCategoriesLevelOne";
        $query=Category::getDb()->cache(function($db)
        {
            return Category::find()->orderBy("order_by ASC")->with(['relationCategoriesLevelOne'])->all();
        }, Yii::$app->params['7_day_cache'], $dependency);

        return $query;
    }


    /*
    * Get list of all categories that are linked to a specific story
    * $data - model from Story
    */
    public static function listCategories($data)
    {
        $categories_array=[];
        foreach ($data->relationCategoryStories as $key=>$value)
        {
            $categories_array[] = Yii::t('app', $value->relationCategory->relationParentCategory->name)." > ".Yii::t('app',$value->relationCategory->name);
        }

        return implode("<br>", $categories_array);

    }

    /*
    *  Get categories and subcategories for dropdown list with optgroup
    */
    public static function dropDownListCategories()
    {
        $array=CategoriesLevelOne::find()->with('relationParentCategory')->all();
        foreach($array as $key=>$value)
        {
            $array_temp[$key]["id"]=$value->id;
            $array_temp[$key]["name"]=Yii::t('app', $value->name);
            $array_temp[$key]["class"]=Yii::t('app', $value->relationParentCategory->name);
        }
        return ArrayHelper::map($array_temp, 'id', 'name', 'class');
    }

}
