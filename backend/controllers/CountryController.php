<?php
/**
 * Created by PhpStorm.
 * User: Oleksii
 * Date: 18.06.2015
 * Time: 11:25
 */

namespace backend\controllers;

use backend\controllers\MyController;  
use backend\models\Category;
use backend\models\Continent;
use backend\models\Country;
use backend\models\CountryStory;
use backend\models\CountryExt;
use backend\models\Story;
use backend\models\Language;
use backend\models\CountryLanguage;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\components\AccessRule;
use backend\models\User;
use yii\db\Query;
use yii\caching\DbDependency;
use backend\components\Helpers;

class CountryController extends MyController  {

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
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN, User::ROLE_MARKETER],
                    ],
                    [
                        'actions' => ['add', 'order', 'delete', 'order-continent', 'countries'],
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN, User::ROLE_MARKETER],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index'             => ['get'],
                    'countries'         => ['get'],
                    'delete'            => ['delete'],
                    'order'             => ['put'],
                    'add'               => ['post'],
                    'order'             => ['post'],
                    'order-continent'   => ['post']

                ],
            ],
        ];
    }

    /*
    * list of all countries
    */
    public function actionIndex()
    {
        $db=Helpers::databaseConnection();

        $tableCountryStories=CountryStory::tableName();
        $tableStories=Story::tableName();
        $tableCountries=Country::tableName();
        $tableCountriesExt=CountryExt::tableName();

        $dependency = new DbDependency;
        $dependency->sql="SELECT MAX(id), MAX(date_modified) FROM $tableStories
                          UNION
                          SELECT MAX(id), MAX(order_index) FROM $tableCountries"; //order_index is not required, it's here because number of columns have to be the same
        //get all countries and count how many stories is in one country
        $countries = $db->cache(function($db) use ($tableStories, $tableCountryStories, $tableCountries)
        {
            /*SELECT `country_stories`.*, countries.name, COUNT(story_id)  AS count_story FROM `country_stories`
            RIGHT JOIN `stories` ON `stories`.`id` = `country_stories`.`story_id` AND stories.status='PUBLISHED'
            RIGHT JOIN `countries` ON `countries`.`id` = `country_stories`.`country_id`
            GROUP BY countries.name ORDER BY `name`   */
            return $db->createCommand("
            SELECT $tableCountryStories.*, $tableCountries.name, COUNT(story_id) AS count_story, $tableCountries.id
            FROM $tableCountryStories
            RIGHT JOIN $tableStories ON ($tableStories.id=$tableCountryStories.story_id AND $tableStories.status='".Story::STATUS_PUBLISHED."')
            RIGHT JOIN $tableCountries ON ($tableCountries.id=$tableCountryStories.country_id)
            GROUP BY $tableCountries.name
            ORDER BY $tableCountries.name
            ")->queryAll();
        }, Yii::$app->params['1_day_cache'], $dependency);


        //find all countries from "countries_ext" that doesn't exist in "countries"
        $dependency = new DbDependency;
        $dependency->sql="SELECT MAX(id) FROM $tableCountries";
        $addnewcountry = $db->cache(function($db) use ($tableCountries, $tableCountriesExt)
        {
            /*SELECT countries_ext.name FROM countries_ext
            JOIN LEFT countries ON (countries_ext.name=countries.name)
            WHERE countries.name IS NULL
            */
            return $db->createCommand("
            SELECT $tableCountriesExt.name
            FROM $tableCountriesExt
            LEFT JOIN $tableCountries ON ($tableCountriesExt.name=$tableCountries.name)
            WHERE $tableCountries.name IS NULL
            ")->queryAll();

        }, Yii::$app->params['1_day_cache'], $dependency);

        return $this->render("index", array(
            'countries' => $countries,
            'addnewcountry'=>$addnewcountry
        ));

    }

    /*
    * Add new country to database "countries"
    */
    public function actionAdd()
    {
        if ($country = Yii::$app->request->post("country_name") )     //country_name e.g. Croatia, Serbia...
        {

            foreach ($country as $countryName)
            {
                //find continent for that country
                $continent_id=Country::findContinentForCountry($countryName);
                //var_dump($continent_id);
                $Country=new Country;
                $Country->order_index=2; //orderin countries because Interlational is always #1, other are by name
                $Country->continent_id=$continent_id;
                $Country->language_id=7; //stupid because I can add only one language but one country can be multilingual
                $Country->name=$countryName;
                if($Country->save())
                {
                    //find languages spoken in this country
                    $countryExt=CountryExt::find()->where(['name'=>$countryName])->one();
                    $langs=explode(",", $countryExt->languages); //hr,en,es
                    foreach($langs as $lang)
                    {
                        //find that language in "languages" table so you can take id and add only language (assign to new country) that exist in CMS
                        $lang_table=Language::find()->where(['code'=>$lang])->one();
                        if(!empty($lang_table))  //if there is any lang in database that match language from that country
                        {
                            $CountryLanguage = new CountryLanguage;
                            $CountryLanguage->language_id=$lang_table->id;
                            $CountryLanguage->country_id=$Country->id;
                            $CountryLanguage->save();
                        }
                    }
                    $success=true;
                }
                else
                    $success=false;

            }
            if($success==true)
                Yii::$app->session->setFlash('success', Yii::t('app','Everything went fine'));
            else
                Yii::$app->session->setFlash('danger', Yii::t('app','Something was wrong'));

            return $this->redirect(['index']);

        }

    }

    public function actionDelete()
    {
        if ( ( $id = Yii::$app->request->post("id") )  &&
            ( $model = Country::findOne($id) ) &&
                $model->numberOfStories() == 0 ) {

            $model->delete();

        }
        Yii::$app->end(200);

    }

    public function actionOrder()
    {

        $ids  = Yii::$app->request->post("ids");
        if ( $ids ) {

            Country::changePlaces( $ids );

        }
        Yii::$app->end(200);

    }

    public function actionOrderContinent()
    {

        $ids  = Yii::$app->request->post("ids");
        if ( $ids ) {

            Continent::changePlaces( $ids );

        }
        Yii::$app->end(200);

    }

    /*
    *  get all countries for specific
    */
    public function actionCountries()
    {
        $continentId = Yii::$app->request->get("continentId");
        $countries = Country::getAvailable( $continentId );
        echo json_encode( $countries );
        Yii::$app->end(200);
    }


}