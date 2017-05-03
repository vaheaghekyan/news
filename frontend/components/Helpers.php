<?php

namespace frontend\components;

use Yii;
use yii\web\View;
use yii\helpers\Url;
use common\components\Helpers as CommonHelpers;
use backend\models\Country;
use backend\models\Language;
/*
* Helper class for some extra functions I need all across projet
*/
class Helpers
{

    /*
    *  get full backend domain
    */
    public static function frontendDomain()
    {
        if(in_array($_SERVER['REMOTE_ADDR'], \Yii::$app->params['local_ip']))
        {
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://localhost:8001"; //so you can fetch image from frontend
        }
        else if(strpos($_SERVER['HTTP_HOST'], "estfrontend") || strpos($_SERVER['HTTP_HOST'], "estbackend"))
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://testfrontend.born2invest.com";
        else
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://news.born2invest.com";
    }

    public static function frontendCDN()
    {
        if(in_array($_SERVER['REMOTE_ADDR'], \Yii::$app->params['local_ip']))
        {
//            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://localhost:8001"; //so you can fetch image from frontend
            return '';
        }
        else if(strpos($_SERVER['HTTP_HOST'], "estfrontend") || strpos($_SERVER['HTTP_HOST'], "estbackend"))
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://testfrontend.born2invest.com";
        else
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://cdn.news.born2invest.com"; //so you can fetch image from
    }

    //create meta tags for facebook
    public static function facebookMetaTags($title, $image, $description)
    {
        $view = \Yii::$app->view;
        $view->registerMetaTag(['name' => 'og:title', 'content' => $title]);
        $view->registerMetaTag(['name' => 'og:image', 'content' => $image]);
        $view->registerMetaTag(['name' => 'og:description', 'content' => $description]);
        $view->registerMetaTag(['name' => 'og:site_name', 'content' => 'Born2Invest']);

        $view->registerMetaTag(['name' => 'og:type', 'content' => 'website']);
        $view->registerMetaTag(['name' => 'al:ios:url', 'content' => 'born2Invest://']);
        $view->registerMetaTag(['name' => 'al:ios:app_name', 'content' => 'Born2Invest']);
        $view->registerMetaTag(['name' => 'al:iphone:url', 'content' => 'born2Invest://']);
        $view->registerMetaTag(['name' => 'al:iphone:app_name', 'content' => 'Born2Invest']);
    }

    //create meta tags for twitter
    public static function twitterMetaTags($title, $image, $description)
    {
        $view = \Yii::$app->view;
        $view->registerMetaTag(['name' => 'twitter:site', 'content' => "@born2invest"]);
        $view->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
        $view->registerMetaTag(['name' => 'twitter:description', 'content' => $description]);
        $view->registerMetaTag(['name' => 'twitter:image', 'content' => $image]);
        $view->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary_large_image']);

    }


    //create meta tags for site
    public static function registerMetaTag($description, $keywords)
    {
        $view = \Yii::$app->view;
        $view->registerMetaTag(['name' => 'description', 'content' => $description]);
        $view->registerMetaTag(['name' => 'keywords', 'content' => $keywords]);

    }

     //set country a.k.a. edition
    public static function setCountry()
    {

        $frontend_edition_id_cookie = CommonHelpers::getCookie(\Yii::$app->params['frontend_edition_country_id_cookie']);

        if($frontend_edition_id_cookie==NULL)
        {
            $geocoder = new \Geocoder\ProviderAggregator();
            $adapter  = new \Ivory\HttpAdapter\CurlHttpAdapter();

            $chain = new \Geocoder\Provider\Chain([
                new \Geocoder\Provider\FreeGeoIp($adapter),
                new \Geocoder\Provider\HostIp($adapter),
                new \Geocoder\Provider\GeoPlugin($adapter),
                //new \Geocoder\Provider\GoogleMaps($adapter),
                //new \Geocoder\Provider\BingMaps($adapter, '<API_KEY>'),
            ]);

            $geocoder->registerProvider($chain);

            try
            {
                $Country=new Country;
                //geocode IP, find country and set cookie
                $geocode = $geocoder->geocode($_SERVER['REMOTE_ADDR']);
                //if ip address cannot be geocoded
                if(!empty($geocode))
                    $country="International";
                else
                    $country=$geocode->first()->getCountry()->getName();

                $query=Country::find()->where(['name'=>$country])->one();

                if(empty($query))
                {
                    $country_id=$Country->always_checked["International"];//if you cannot find anything in database put "international" as category
                }
                else
                {
                    $country_id=$query->id;
                }

                CommonHelpers::createCookie(\Yii::$app->params['frontend_edition_country_id_cookie'], $country_id, NULL);


            }
            catch (Exception $e)
            {
                echo $e->getMessage();
            }
        }
    }

    //detetct language and return language code and language id
    public static function detectLanguage()
    {
        //get language id and language code cookie
        $frontend_language_id_cookie = CommonHelpers::getCookie(\Yii::$app->params['frontend_language_id_cookie']);
        $frontend_language_code_cookie = CommonHelpers::getCookie(\Yii::$app->params['frontend_language_code_cookie']);

        $language_id=$frontend_language_id_cookie;
        $language_code=$frontend_language_code_cookie;

        //if cookie doesnt exists
        if($frontend_language_id_cookie==NULL && $frontend_language_code_cookie==NULL)
        {
            //get user language from browser
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); //'en-US,en;q=0.8' becomes 'en'
            $lang_query=Language::findByCode($lang); //find indatabase
            //if you don't find anything
            if(empty($lang_query))
            {
                $language_id=7; //english as default language, id=7 in languages table
                $language_code="en";
            }
            else
            {
                $language_id=$lang_query->id;
                $language_code=$lang_query->code;
            }

            // add a new cookie
            CommonHelpers::createCookie(\Yii::$app->params['frontend_language_id_cookie'], $language_id, NULL);
            CommonHelpers::createCookie(\Yii::$app->params['frontend_language_code_cookie'], $language_code, NULL);
        }

        $language['code'] = $language_code;
        $language['id'] = $language_id;
        return $language;
    }

    public static function setLanguage()
    {
        $language = Helpers::detectLanguage();
        Yii::$app->language=$language['code'];
    }

    /*
    *  set timezone from ookie
    */
    public static function setTimezone()
    {
        $timezone=CommonHelpers::getCookie(\Yii::$app->params['frontend_timezone_cookie']);
        if($timezone==NULL)
            \Yii::$app->response->redirect(Url::to(['/site/detect-timezone']), 301)->send();
        else
            date_default_timezone_set($timezone);

    }

    /*
    * generate subcategory name, for example: Trending => trending, Top Stories => top-stories
    * $category -> category name
    */
    public static function generateSubcategoryName($category)
    {
        return strtolower(str_replace(" ", "-", $category));
    }

}