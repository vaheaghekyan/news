<?php
/**
 * Created by PhpStorm.
 * User: Oleksii
 * Date: 12.06.2015
 * Time: 10:27
 */

namespace backend\controllers;

use backend\controllers\MyController;  
use backend\models\Story;
use backend\models\Category;
use backend\models\CategoryStory;
use backend\models\CategoriesLevelOne;
use backend\models\Language;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\components\AccessRule;
use backend\models\User;
use yii\db\Query;
use backend\components\Helpers;
use yii\caching\DbDependency;

class CategoryController extends MyController  {

    public $layout = "admin";

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
                        'actions' => [ 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete',  'delete', 'create-sub-category', 'delete-subcategory', 'change-order'],
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index'      => ['get'],
                    'delete'     => ['delete'],
                    'order'      => ['put'],
                    'update'     => ['post'],
                    'create'     => ['post'],
                    'order'      => ['post'],
                    'create-sub-category'   => ['post'],
                    'delete-subcategory'    => ['delete']

                ],
            ],
        ];
    }

    /*
    * change order of all parent and level one categories
    */
    public function actionChangeOrder()
    {
        if(isset($_POST["submit_order"]))
        {
            //-----------------------------PARENT---------------------------
            /*
            $_POST["parent_order"]
            array(6) {
              [5]=>             THIS IS id in Category
              string(1) "2"     THIS IS order_by in Category
              [3]=>
              string(1) "3"
              ...
            }
            */
            $parent_order_by=$_POST["parent_order_by"];
            //first check if there is douple values for parent categories
            if((array_unique($parent_order_by) == $parent_order_by)==false)  //it has double values
            {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'Parent category double order')  );
                 return $this->redirect('index');

            }
            //save to database
            foreach($parent_order_by as $key=>$value)
            {
                //find in database
                $query=Category::findOne($key);
                $query->order_by=$value;
                $query->save();
            }

            //-----------------------------LEVEL ONE---------------------------
            /*
            array(6)
            {
              [1]=>                         [1] THIS IS id in Category
              array(1)
              {
                [1]=> string(1) "2"        [1], [5]... THIS IS order_by in CategoriesLevelOne
              }                            string(1) "2" -> order_by in  CategoriesLevelOne
              [3]=>
              array(4)
              {
                [5]=> string(1) "2"
                [6]=> string(1) "2"
                [7]=> string(1) "2"
                [2]=> string(1) "2"
              }
              ...
            }
            */
            $level_one_order=$_POST["level_one_order_by"];
            //check for duplicates
            foreach($level_one_order as $subcategory_order_index)
            {
                /*
                array(1)
                {
                  [1]=> string(1) "2"      [1], [5]... THIS IS order_by in CategoriesLevelOne
                }                          string(1) "2" -> order_by in  CategoriesLevelOne
                array(4)
                {
                  [5]=> string(1) "2"
                  [6]=> string(1) "2"
                  [7]=> string(1) "2"
                  [2]=> string(1) "2"
                }
                ...
                */
                if((array_unique($subcategory_order_index) == $subcategory_order_index)==false)  //it has double values
                {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'Child category double order')  );
                    return $this->redirect('index');
                }
            }
            //save to database
            foreach($level_one_order as $value)
            {
                foreach($value as $category_level_one_id=>$category_level_one_order_by)
                {
                    //find in database
                    $query=CategoriesLevelOne::findOne($category_level_one_id);
                    $query->order_by=$category_level_one_order_by;
                    $query->save();
                }


            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
            //clear cache so categories ID for API are updated, since I use order_by field to generate categories id
            Yii::$app->cache->flush();
            return $this->redirect('index');
        }
    }

    /*
    * list all categories with number of stories per category
    */
    public function actionIndex()
    {
        $db=Helpers::databaseConnection();

        $tableStory=Story::tableName();
        $tableCategoriesLevelOne=CategoriesLevelOne::tableName();
        $tableCategoriesStories=CategoryStory::tableName();
        $tableCategory=Category::tableName();
        $language_id=Language::getCurrentId();

        $dependency = new DbDependency;
        $dependency->sql="SELECT MAX(id) FROM $tableStory WHERE language_id=$language_id";
        $query = $db->cache(function($db) use ($tableCategoriesLevelOne, $tableCategoriesStories, $tableCategory, $tableStory, $language_id)
        {
            /* count number of stories per category
            SELECT COUNT( * ) , categories_level_one.name
            FROM stories
            LEFT JOIN category_stories ON category_stories.story_id = stories.id
            LEFT JOIN categories_level_one ON category_stories.category_id = categories_level_one.id
            WHERE stories.status =  "PUBLISHED" AND language_id =7
            GROUP BY categories_level_one.id
            */
            return
                (new Query())
                ->select("COUNT(*) as numberOfStories, $tableCategoriesLevelOne.id AS CLO_ID")
                ->from("$tableStory")
                ->leftJoin($tableCategoriesStories, "$tableCategoriesStories.story_id=$tableStory.id")
                ->leftJoin($tableCategoriesLevelOne, "$tableCategoriesStories.category_id=$tableCategoriesLevelOne.id")
                ->where(["$tableStory.status"=>Story::STATUS_PUBLISHED, "$tableStory.language_id"=>$language_id])
                ->groupBy("$tableCategoriesLevelOne.id")
                ->all();
        }, Yii::$app->params['12_hours_cache'], $dependency);

        $numberOfStories=[];
        foreach($query as $key=>$value)
        {
            $categoryLevelOneId=$value["CLO_ID"];
            $numberOfStories[$categoryLevelOneId]=$value["numberOfStories"];
        }

        $categories=Category::find()->with(['relationCategoriesLevelOne'])->orderBy("order_by ASC")->all();

        $Category = new Category;
        $CategoriesLevelOne = new CategoriesLevelOne;

        return $this->render("index", array(
            'categories' => $categories,
            'numberOfStories'=>$numberOfStories,
            'Category'=>$Category,
            'CategoriesLevelOne'=>$CategoriesLevelOne,
        ));
    }

    /*
    *  in /category/index when admin clicks on button "Create new categirues" under list of all categories
    */
    public function actionCreate()
    {

        $category               = new Category();
        $category->language_id  = Language::getCurrentId();

        $subCategory            = new CategoriesLevelOne();

        if ($category->load(Yii::$app->request->post()) && $subCategory->load(Yii::$app->request->post()))
        {
            //get last order_by and increment it by 1
            $category_tmp=Category::find()->orderBy('order_by DESC')->limit(1)->one();
            $category->order_by     = $category_tmp->order_by+1;

            $category->name=strtoupper($category->name); //make it all caps
            if($category->save())
            {
                //get las order_by for level one categories and increment it by 1
                $subCategory_tmp=CategoriesLevelOne::find()->where(['parent_category'=>$category->id])->orderBy('order_by DESC')->limit(1)->one();
                $subCategory->parent_category = $category->id;
                $subCategory->order_by = 1;
                $subCategory->save();
                return $this->redirect(['index']);
            }
        }
        else
        {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Cannot add category'));
             return $this->redirect(['index']);
        }

    }

    /*
    *  in /category/index when admin clicks on button "Create new subcategory" under list of all categories
    */
    public function actionCreateSubCategory()
    {
        if ( ($id = Yii::$app->request->post("id"))  && ($model = Category::findOne($id)) && ($name = Yii::$app->request->post("name")) )
        {
            //get last order_by for level one categories and increment it by 1
            $subCategory_tmp=CategoriesLevelOne::find()->where(['parent_category'=>$model->id])->orderBy('order_by DESC')->limit(1)->one();
            $subcategory = new CategoriesLevelOne();
            $subcategory->parent_category = $id;
            $subcategory->name = $name;
            $subcategory->order_by = $subCategory_tmp->order_by+1;
            if($subcategory->save())
                return json_encode(array("success" => true, "id" => $subcategory->id));

        }
        Yii::$app->end(200);
    }

    /*
    *  updating only subcategories
    */
    public function actionUpdate()
    {
        if ( ($id = Yii::$app->request->post("id"))  && ($model = CategoriesLevelOne::findOne($id)) && ($name = Yii::$app->request->post("name")) ) {

            $model->name = $name;
            $model->save( false, array("name") );

        }
        Yii::$app->end(200);
    }

    /*public function actionDelete()
    {
        if ( ( $id = Yii::$app->request->post("id") )  &&
            ( $model = Category::findOne($id) ) &&
                $model->numberOfStoriesInParent() == 0 ) {


            $model->deleteParentStuff();
            Yii::$app->getSession()->setFlash('success', Yii::t("app", "Category has been successfully deleted"));

        }
        Yii::$app->end(200);
    }  */

    /*
    *  delete subcategory but only if it's empty. When user wants to delete subcategory
    */
    public function actionDeleteSubcategory()
    {
        if ( ($id = Yii::$app->request->post("id"))  &&  ($model = CategoriesLevelOne::findOne($id)) && $model->numberOfStories() == 0 )
        {

            $model->delete();
            //if there is no more categories for parent category, delete parent category
            $check_child=CategoriesLevelOne::find()->where(['parent_category'=>$model->parent_category])->count();
            if($check_child==0)
            {
                $parent=Category::findOne($model->parent_category)->delete();
            }
            Yii::$app->getSession()->setFlash('success', Yii::t("app", "Category has been successfully deleted"));

        }
        Yii::$app->end(200);
    }

    public function actionOrder()
    {

        $ids  = Yii::$app->request->post("ids");
        if ( $ids ) {

            Category::changePlaces( $ids );

        }
        Yii::$app->end(200);

    }


}