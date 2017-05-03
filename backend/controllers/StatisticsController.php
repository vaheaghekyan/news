<?php

namespace backend\controllers;

use Yii;
use backend\components\Helpers;
use backend\models\Story;
use backend\models\Country;
use backend\models\CategoriesLevelOne;
use backend\models\CategoryStory;
use backend\models\Language;
use backend\models\LanguagesAll;
use backend\models\search\LanguageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use backend\models\User;
use yii\db\Exception;
use yii\db\Query;

/**
 * LanguageController implements the CRUD actions for Language model.
 */
class StatisticsController extends Controller
{
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
                        'actions' => ['index', 'daily-report', 'stories-per-category'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['post'],
                ],
            ],
        ];
    }

    /*
    *  calulcate how many stories user published per category for chosen day
    */
    public function actionStoriesPerCategory()
    {
        $CategoriesLevelOne=CategoriesLevelOne::tableName();
        $CategoryStory=CategoryStory::tableName();
        $Story=Story::tableName();

        if(isset($_GET["date"]) && !empty($_GET["date"]))
            $date=$_GET["date"];
        else
            $date=date("Y-m-d");

        if(isset($_GET["user"]) && !empty($_GET["user"]))
            $IDuser=$_GET["user"];
        else
            $IDuser=Yii::$app->user->getId();


        $begin=Helpers::createDateTimeBetween("begin", $date);
        $end=Helpers::createDateTimeBetween("end", $date);

        /*
        SELECT COUNT(*), categories_level_one.name FROM `category_stories`
         LEFT JOIN  categories_level_one ON (categories_level_one.id=category_stories.category_id)
        LEFT JOIN stories ON (stories.id=category_stories.story_id)
        WHERE stories.user_id=5 AND date_published BETWEEN "2015-09-08 00:00:00" AND "2015-12-08 23:59:59"
        GROUP BY category_id
        */
        $stories = (new Query())
        ->select(["COUNT(*) AS count, $CategoriesLevelOne.name AS category_name"])
        ->from("$CategoryStory")
        ->leftJoin($CategoriesLevelOne, "$CategoriesLevelOne.id=$CategoryStory.category_id")
        ->leftJoin($Story, "$Story.id=$CategoryStory.story_id")
        ->where(["$Story.user_id" => $IDuser])
        ->andWhere(["BETWEEN", "date_published", $begin, $end])
        ->groupBy("category_id")
        ->all();

        $total=0;
        foreach($stories as $story)
        {
            $total+=$story["count"];
        }

        //get user info
        $user=User::findOne($IDuser);

        return $this->render('stories-per-category',
        [
            'stories'=>$stories,
            'date'=>$date,
            "total"=>$total,
            "user"=>$user
        ]);
    }

    /*
    *  list daily report for users
    */
    public function actionDailyReport()
    {
        if(isset($_GET["date"]) && !empty($_GET["date"]))
            $date=$_GET["date"];
        else
            $date=date("Y-m-d");

        if(isset($_GET["user"]) && !empty($_GET["user"]))
            $IDuser=$_GET["user"];
        else
            $IDuser=Yii::$app->user->getId();

        $begin=Helpers::createDateTimeBetween("begin", $date);
        $end=Helpers::createDateTimeBetween("end", $date);
        $stories=Story::find()
        ->where(["user_id"=>$IDuser])
        ->andWhere(["BETWEEN", "date_created", $begin, $end])
        ->andWhere(["status"=>Story::STATUS_PUBLISHED])
        ->orderBy("date_created ASC")
        ->all();

        return $this->render('daily-report',
        [
            'stories'=>$stories,
            'date'=>$date,
        ]);
    }

    /**
     * Lists all Language models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(isset($_POST["submit_statistics"]))
        {
            $tableStory=Story::tableName();
            $tableCountry=Country::tableName();

            $story=Story::find();
            $per_country_query=Country::find()
                ->addSelect("COUNT(*) AS countNumberOfStories, $tableCountry.*")
                ->joinWith(['relationCountryStories.relationStory'])
                ->groupBy("countries.id");

            //date created
                  $start_date=$_POST["start_date"];
                $end_date=$_POST["end_date"];
                $per_country_query->andWhere(['between', 'date_created', $start_date, $end_date]);

                $per_country_query->andWhere(["user_id"=>(int)$_POST["user"]]);

                $per_country_query->andWhere(["$tableStory.language_id"=>(int)$_POST["language"]]);



            return $this->render('index',
            [
                'per_country'=>$per_country_query->all()
            ]);

        }


        return $this->render('index');
    }
}
