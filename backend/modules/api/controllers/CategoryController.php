<?php
/**
 * Created by PhpStorm.
 * User: alekseyyp
 * Date: 06.07.15
 * Time: 15:42
 */

namespace backend\modules\api\controllers;

use backend\models\Category;
use backend\models\Language;
use yii\web\Controller;
use Yii;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use yii\filters\VerbFilter;
use backend\modules\api\models\Api;

class CategoryController  extends Controller
{

    public $defaultAction           = "find";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'actions' => ['find'],
                        'allow' => true,
                        'roles' => [],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'find' => ['get'],

                ],
            ],
        ];
    }

    /*
    * return ALL categories so app can list it in sidebar
    * using list of categories $categoryIds in /story/find, app is creating menu on left side so that categories where there is no stories are hidden
    */
   public function actionFind($languageId)
    {
        //in case $languageId  is empty (app cannot send any languageId)
        if(empty($languageId) || $languageId==-1)
            $languageId=7;

        //set language
        Api::setLanguage($languageId);

        $list       = [];
        $categories  = Category::getParents();

        foreach ( $categories as $category )
        {

            //since order_by has to be unique for every parent category, multiply with 1000 to get high range so you can add as many subcategories to it by summing with subcategory_id since it is unique
            //In that way you will get unique IDs for each parent and subcategory. Because app needs unique IDs to recognize parent category and its childer but also to filter news
            foreach($category->relationCategoriesLevelOne as $subcategory)
            {
                $subcategories[] = [
                    'id'            => $category->order_by*Api::ORDER_BY_MULTIPLY+$subcategory->order_by,
                    'name'          => Yii::t('app', $subcategory->name),
                    //'name'          => $subcategory->name,
                ];
            }


            $list[] = [
                'id'            => $category->order_by*Api::ORDER_BY_MULTIPLY,
                'name'          => strtoupper(Yii::t('app', $category->name)),
                //'name'          => $category->name,
                'subcategories' => $subcategories
            ];

             //empty array
            $subcategories=[];

        }

        return $list;
        /* return $this->render('@backend/views/site/index');   */

    }


}