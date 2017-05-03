<?php
/**
 * Created by PhpStorm.
 * User: alekseyyp
 * Date: 06.07.15
 * Time: 15:35
 */

namespace backend\modules\api\controllers;

use backend\models\Country;
use backend\models\CountryExt;
use backend\models\Language;
use backend\models\Story;
use backend\components\Helpers;
use yii\web\Controller;
use Yii;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use backend\modules\api\models\Api;
use backend\modules\api\models\StoryApi;
use yii\filters\VerbFilter;
use yii\caching\DbDependency;

class LanguageController  extends Controller
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
                        'actions'   => ['find', 'find-by-country'],
                        'allow'     => true,
                        'roles'     => [],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'find'              => ['get'],
                    'find-by-country'   => ['get']

                ],
            ],
        ];
    }


    /*
    * find all languages then loop through languages and pull only countries where specific language is spoken
    * because when you chooe Croatian as language, you can only choose specific countries as Edition like Croatia, Serbia, Bosnia.

    * in app used to fill right menu "languages" and "edition"
    */
    public function actionFind($languageId)
    {
       //in case $languageId  is empty (app cannot send any languageId)
        if(empty($languageId) || $languageId==-1)
            $languageId=7;

        $cache=Yii::$app->cache;

        $list = [];
        $tableCountryExt=CountryExt::tableName();
        $tableCountry=Country::tableName();
        $tableLanguage=Language::tableName();
        $tableStory=Story::tableName();
        $Country = new Country;

        //get lang code of current langauge: en, hr...
        $current_lang_code=Language::findById($languageId)->code;

        //Select all countries depending on language spoken in that country. e.g. "hr" is spoken in serbia, bosnia and croatia
        $cache_key=Api::LANG_CACHE_KEY.$languageId;
        //find in cache
        if($data=$cache->get($cache_key))
        {
            $languages=$data;
        }
        else
        {
            $dependency=new DbDependency;
            $dependency->sql="SELECT MAX(id) FROM $tableLanguage";

            $languages = Language::find()->with(['relationCountryLanguage.relationCountry'])->orderBy('name ASC')->all();
            //Dependency of the cached item. If the dependency changes, the corresponding value in the cache will be invalidated when it is fetched via get()
            $cache->set($cache_key, $languages, Yii::$app->params['7_day_cache'], $dependency);
        }

        //go through stories and get countries of each story so you can so you can list editions depending if there is story in that edition or not
        //find in cache
        $cache_key=Api::STORY_BY_COUNTRY_CACHE_KEY.$languageId;
        if($data=$cache->get($cache_key))
        {
            $stories=$data;
        }
        else
        {

            $dependency=new DbDependency;
            $dependency->sql="SELECT MAX(date_modified), MAX(id), COUNT(*) FROM $tableStory WHERE language_id=$languageId";

            $stories = Story::find()
            ->where(['status' => Story::STATUS_PUBLISHED, 'language_id' => $languageId])
            ->with(['relationCountries'])
            ->orderBy(['date_published' => SORT_DESC])
            ->limit(StoryApi::STORY_LIMIT)
            ->asArray()
            ->all();
            //Dependency of the cached item. If the dependency changes, the corresponding value in the cache will be invalidated when it is fetched via get()
            $cache->set($cache_key, $stories, Yii::$app->params['12_hours_cache'], $dependency);
        }

        $country_for_story=[];
        foreach($stories as $value)
            foreach($value["relationCountries"] as $countries)
                $country_for_story[] = $countries["id"];

        $include_countries_per_lang=Helpers::showCountriesPerLang();
        $hide_lang=[9,25];  //9 (swedish), 25(uzbek)

        foreach ( $languages as $language )
        {
            if(in_array($language->id, $hide_lang))
                continue;

            $countries = [];
            //return coutry name translated into chosen language
            Api::setLanguageByCode($current_lang_code);
            foreach ($language->relationCountryLanguage as $country)
            {
                //check if you should return some specific countries for that language
                if(isset($include_countries_per_lang[$language->id]))
                {
                    $countries_tmp=$include_countries_per_lang[$language->id];

                    //but only if there are stories in that countries (don't show countries if there aren't stories in it)
                    if(in_array($country->relationCountry->id,$countries_tmp) && in_array($country->relationCountry->id,$country_for_story))
                    {
                        $countries[] = [
                            'id'            => $country->relationCountry->id,
                            'name'          => Yii::t('app', $country->relationCountry->name),
                        ];
                    }
                }
                //only include countres if there are stories in it
                else if(in_array($country->relationCountry->id, $country_for_story))
                {
                     $countries[] = [
                        'id'            => $country->relationCountry->id,
                        'name'          => Yii::t('app', $country->relationCountry->name),
                    ];
                }
            }
            //International
            $countries[] =
            [
                'id'            => $Country->always_checked["International"],
               // 'name'          => Yii::t('app', "International"),
                'name'          => "International",
            ];

            //return language name from my english file where all langauges are in their native form
            Api::setLanguageByCode("en");
            $list[] = [
                'id'            => $language->id,
                'name'          =>  Yii::t('app', $language->name),
                'code'          => $language->code,
                'countries'     => $countries

            ];

        }

        return $list;
        /* return $this->render('@backend/views/site/index', [

        ]); */
    }

}