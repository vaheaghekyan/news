<?php
namespace frontend\controllers;

use Yii;
use backend\models\Language;
use backend\models\CategoriesLevelOne;
use backend\models\Category;
use backend\models\Country;
use backend\models\CountryExt;
use backend\models\CountryStory;
use backend\models\Story;

use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\components\Helpers as CommonHelpers;
use frontend\components\LinkGenerator;
use frontend\components\Helpers;
use backend\components\Helpers as BackendHelpers;

/**
 * Ajax controller
 * used to  call actions from ajax function
 */
class AjaxController extends Controller
{

    public function beforeAction($action)
    {
        $action_tmp=Yii::$app->controller->action->id;

        //disable CSRF
        if($action_tmp=="landing-last-story")
        {
            $this->enableCsrfValidation=false;
        }
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['landing-last-story'],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                ],
            ],
        ];
    }


    /*
    * get latest story per country and language  for hash world map on landing page
    * you have to disable CSRF here because of POST
    */
    public function actionLandingLastStory()
    {
        header('Access-Control-Allow-Origin: *');

        if(isset($_POST["language"]) && isset($_POST["country"]))
        {
            $tableCountryStory=CountryStory::tableName();
            $tableStory=Story::tableName();
            $language=explode(",", $_POST["language"]); //$_POST["language"] = 1,25 id of language in languages table
            $country=explode(",", $_POST["country"]); //$_POST["country"] = 3,6 id of country in countries table

            //if country has multiple languages, change native language to the one of a browser, if it is one of those
            //switzerland
            if(in_array(22, $country)) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            switch ($lang){
                    case "fr":
                        $language = '14';
                        break;
                    /*case "it":
                        $language = '';
                        break;*/
                    default:
                        //german
                        $language = '1';
                        break;
                }
            }

            //belgium
            if(in_array(19, $country)) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            switch ($lang){
                    case "de":
                        $language = '1';
                        break;
                    /*case "nl":
                        $language = '';
                        break;*/
                    default:
                        //french
                        $language = '14';
                        break;
                }
            }

            //singapore

            //cameroon

            //spain

            //ireland

            //hong kong

            //zimbabwe

            //republic of the congo

            //democratic republic of the congo

            //get first story in selected country in native language
            $query=Story::find()
            ->where(['IN', "$tableStory.language_id", $language])
            //->andWhere(["IN", "$tableCountryStory.country_id",$country])
            ->andWhere(["$tableCountryStory.country_id"=>$country[0]])
            ->joinWith(['relationCountryStories.relationCountry', 'relationLanguage', 'relationSubCategories'])
            //->joinWith(['relationLanguage', 'relationSubCategories'])
            ->orderBy("date_published DESC")
            ->limit(1)
            ->one();

            //if there are no stories for the current country in native language, get international story in that language
            if($query == NULL) {
                $query=Story::find()
                ->where(['IN', "$tableStory.language_id", $language])
                //->andWhere(["IN", "$tableCountryStory.country_id",$country])
                ->andWhere(["$tableCountryStory.country_id"=>53])
                ->joinWith(['relationCountryStories.relationCountry', 'relationLanguage', 'relationSubCategories'])
                //->joinWith(['relationLanguage', 'relationSubCategories'])
                ->orderBy("date_published DESC")
                ->limit(1)
                ->one();
            }

            //get country name of currently selected country
            $overcountry=Country::find()
            ->where(['id'=>$country[0]])
            ->limit(1)
            ->one();

            /*$nativecountry=CountryExt::find()
            ->where(['name'=>$overcountry->name])
            ->limit(1)
            ->one();

            if($nativecountry == NULL)
                $showcountry = $overcountry->name;
            else
                $showcountry = $nativecountry->native;*/

             //seo_url/type/name/id/page/categoryid
             $url_params=[
             'seo_url'=>$query->seo_url,
             'categoryid'=>$query->relationSubCategories[0]->id,
             'id'=>$query->id,
             'type'=>'subcategory',
             'name'=>Helpers::generateSubcategoryName($query->relationSubCategories[0]->name),
             'page'=>0];

            /*$nativelanguage = Language::find()
            ->where(['id'=>$language[0]])
            ->limit(1)
            ->one();*/

            //set language of the selected country to display it in native language
            Yii::$app->language=$query->relationLanguage->code;//$nativelanguage->code;

            //if language is not arabic, all normal
            if(Yii::$app->language != 'ar') {
                $result=
            '<a href="'.LinkGenerator::linkStoryView(null, $url_params,"full").'" target="_blank">'.
            '<h1>'.Yii::t("app", $overcountry->name).'<br><span>'.Yii::t("app", $query->relationLanguage->name).'</span></h1>'.
                    '<img src="'.BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($query->date_created, Story::PATH_IMAGE, $query->image, false).'" alt="">'.
                    '<h3>'.$query->title.'</h3>'.
            '<h1 style="font-size: 14px; font-weight: bold; border: none; text-align: center;"><span style="color: #91bd09;">'.Yii::t("app", "Read More").'</span></h1></a>'
            ;
            }
            //if language is arabic: text direction rtl, text align right
            else {
                $result=
            '<a href="'.LinkGenerator::linkStoryView(null, $url_params,"full").'" target="_blank">'.
            '<h1 style="direction: rtl; text-align: right;"><bdo dir="rtl">'.Yii::t("app", $overcountry->name).'</bdo><br><span><bdo dir="rtl">'.Yii::t("app", $query->relationLanguage->name).'</span></bdo></h1>'.
                    '<img src="'.BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($query->date_created, Story::PATH_IMAGE, $query->image, false).'" alt="">'.
                    '<h3 style="direction: rtl; text-align: right;"><bdo dir="rtl">'.$query->title.'</bdo></h3>'.
            '<h1 style="font-size: 14px; font-weight: bold; border: none; text-align: center;"><span style="color: #91bd09;"><bdo dir="rtl">'.Yii::t("app", "Read More").'</bdo></span></h1></a>'
            ;
            }
            //set language back to english
            Yii::$app->language="en";
            echo json_encode(['result'=>$result]);
        }
    }


}