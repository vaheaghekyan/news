<?php
/**
 * Created by PhpStorm.
 * User: Oleksii
 * Date: 08.06.2015
 * Time: 13:32
 */

namespace backend\controllers;

use backend\controllers\MyController;
use backend\models\ChangePasswordForm;
use backend\models\Story;
use backend\models\Language;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use Yii;

class AdminController extends MyController
{

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action))
        {
            return false;
        }

        //logout user if his account is deactivated
        $user=Yii::$app->user->getIdentity();
         //if user status is 0, user's account is deactivated
        if($user->status==0)
        {
            Yii::$app->user->logout();
            $this->redirect(['/site/login']);
        }
        return true; // or false to not run the action
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'test', 'settings'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],

        ];
    }

    public $layout = "admin";

    public function actionIndex()
    {

        $tableStory=Story::getTableSchema();
        //$lang=Language::getCurrentId();
           
        //find total number of unpublished stories
        //find total number of published stories
        //find total number of pending stories
       /* $command = Yii::$app->db->createCommand("
        SELECT
            SUM(IF(status='".Story::STATUS_UNPUBLISHED."',1,0)) AS unpublished,
            SUM(IF(status='".Story::STATUS_PUBLISHED."',1,0)) AS published,
            SUM(IF(status='".Story::STATUS_PENDING."',1,0)) AS pending,
            COUNT(*) as total
        FROM ".$tableStory->name." WHERE language_id=$lang");
        $story_report = $command->queryOne();  */

        //check for stories without image
        $storiesNoImgVid=Story::errorStories();
        
        return $this->render("index",
        [
            'storiesNoImgVid' => $storiesNoImgVid,
            //'story_report'=>$story_report,
        ]);
    }

    public function actionSettings()
    {

        $model = new ChangePasswordForm();
        if ( $model->load(Yii::$app->request->post()) && $model->change() ) {

            Yii::$app->session->setFlash('success', Yii::t("app", "Your password has been successfully changed."));
            return $this->refresh();

        }

        return $this->render("settings", array(
            'model'     => $model
        ));
    }

    /*public function actionTest()
    {
        $languageId = 2; //Ukrainian Language

        $category               = new \backend\models\Category();
        $category->name         = "Main";
        $category->language_id  = $languageId;
        $category->order_by     = 1;
        $category->save(false);

        $subCategory            = new \backend\models\Category();
        $subCategory->name        = "Sub category";
        $subCategory->parent_id   = $category->id;
        $subCategory->language_id = $languageId;
        $subCategory->order_by    = 1;
        $subCategory->save(false);

        $subCategory2            = new \backend\models\Category();
        $subCategory2->name      = "Sub category 2";
        $subCategory2->parent_id = $category->id;
        $subCategory2->language_id = $languageId;
        $subCategory2->order_by    = 2;
        $subCategory2->save(false);

        $user = Yii::$app->user->getIdentity();

        $story1                 = new \backend\models\Story();
        $story1->title          = "Story 1";
        $story1->language_id    = $languageId;
        $story1->user_id        = $user->id;

        $story1->save(false);

        $categoryStory                  = new \backend\models\CategoryStory();
        $categoryStory->category_id     = $subCategory->id;
        $categoryStory->story_id        = $story1->id;
        $categoryStory->save(false);

        $categoryStory                  = new \backend\models\CategoryStory();
        $categoryStory->category_id     = $subCategory2->id;
        $categoryStory->story_id        = $story1->id;
        $categoryStory->save(false);

        $story1                 = new \backend\models\Story();
        $story1->title          = "Story 2";
        $story1->language_id    = $languageId;
        $story1->user_id        = $user->id;
        $story1->save(false);

        $categoryStory                  = new \backend\models\CategoryStory();
        $categoryStory->category_id     = $subCategory->id;
        $categoryStory->story_id        = $story1->id;
        $categoryStory->save(false);

        die("done");


    }*/

}