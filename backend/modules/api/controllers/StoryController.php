<?php
namespace backend\modules\api\controllers;

use backend\models\Language;
use backend\models\Country;
use backend\models\Category;
use backend\models\CategoryStory;
use common\components\Helpers as CommonHelpers;
use yii\web\Controller;
use Yii;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use yii\filters\VerbFilter;
use backend\models\Story;
use backend\modules\api\models\StoryApi;
use backend\modules\api\models\Api;
use yii\caching\DbDependency;

class StoryController  extends Controller
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
                        'actions' => ['find', 'test', 'video', 'single-story'],
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

    /*public function actionTest()
    {
        $array=
        [
            'content'=>
            [
                [
                    'url'=>'http://cms.born2invest.com/uploads/28.mp4',
                    'autoplay'=>false,
                    'controls'=>
                    [
                        'components'=>
                        [
                            "totalTime",
                          "slider",
                          "currentTime",
                          "fullscreen"
                        ]
                    ]
                ],
                ['url'=>'http://cms.born2invest.com/uploads/269.mp4',
                'autoplay'=>false,
                ],
            ]
        ];

        return ($array);
    } */
    /*
    *  return ALL stories from database and include countries and categories linked to every story
    * so app can list all stories
    * using list of categories $categoryIds, app is creating menu on left side so that categories where there is no stories are hidden
    *  NOT USED ANYMORE $currentTimeId - current time on mobile phone
    * $_GET["currentTimeId"] - Y-m-d_H:i:s
    */

    public function actionFind($languageId)
    {       
        //now time is UTC so we don't need anythig from phone
        $currentTime=strtotime(date("Y-m-d H:i:s"));
        //check for "_" becuase old version are sending time without "_" like Y-m-d H:i:s
        /*if(isset($_GET["currentTimeId"]) && strpos($_GET["currentTimeId"], "_"))
        {
            $time=explode("_", $_GET["currentTimeId"]);
            $currentTime=$time[0]." ".$time[1];
            $currentTime=strtotime($currentTime);
        }
        //else take that currenTimeId as it is, I assume Y-m-d H:i:s
        else if(isset($_GET["currentTimeId"]))
        {
            $currentTime=strtotime($_GET["currentTimeId"]);
        }  */

       //in case $languageId  is empty (app cannot send any languageId)
        if(empty($languageId) || $languageId==-1)
            $languageId=7;

        $cache=Yii::$app->cache; //$cache->flush();
        $list = [];
        $tableStory=Story::tableName();
        $tableCategoryStory=CategoryStory::tableName();
        $Country=new Country;
        $cache_key=Api::STORY_CACHE_KEY.$languageId;
        $status_published=Story::STATUS_PUBLISHED;

        //find in cache
        $stories = $cache->get($cache_key);

        if ($stories === false)
        {
            $dependency = new DbDependency;
            $dependency->sql="SELECT MAX(id), MAX(date_modified), COUNT(*)
                          FROM $tableStory
                          WHERE language_id=$languageId AND status='$status_published'";

            $stories = Story::find()
            ->where(["$tableStory.status" => Story::STATUS_PUBLISHED,
            "$tableStory.language_id" => $languageId])
            ->andWhere("type=:image OR type=:video",
            [':image'=>Story::TYPE_IMAGE, ':video'=>Story::TYPE_VIDEO])
            ->orderBy(['date_published' => SORT_DESC])
            ->with(['relationCountries','relationSubCategories.relationParentCategory', 'relationUser'])
            ->limit(StoryApi::STORY_LIMIT)
            ->all();
            //Dependency of the cached item. If the dependency changes, the corresponding value in the cache will be invalidated when it is fetched via get()
            $cache->set($cache_key, $stories, Yii::$app->params['8_hours_cache'], $dependency);
        }

        /*//SPONSORED STORIES
        $cache_key=Api::STORY_SPONSORED_CACHE_KEY.$languageId;
        $injectSponsoredStories = $cache->get($cache_key);
        if ($injectSponsoredStories === false)
        {
            $injectSponsoredStories =  Story::find()
                    ->where(['status' => Story::STATUS_PUBLISHED, 'language_id' => $languageId])
                    ->andWhere("type=:sponsored", [':sponsored'=>Story::TYPE_SPONSORED])
                    ->orderBy(['date_published' => SORT_DESC])
                    ->with(['relationCountries','relationSubCategories.relationParentCategory', 'relationUser'])
                    ->limit(50)
                    ->all();

            $cache->set($cache_key, $injectSponsoredStories, Yii::$app->params['5_day_cache'], $dependency);
        };

        $stories = CommonHelpers::findStories($stories, $injectSponsoredStories, ['language'=>$languageId, 'country'=>$Country->always_checked["International"]]); */

        //inject sponsored story
        $i=1;
        foreach ( $stories as $story )
        {
            //this is for schedule stories
            if($currentTime!=false)
                if(strtotime($story->date_published) > $currentTime)
                    continue;

            //if image doesn't exist and this is image story, don't load story
            if($story->type==Story::TYPE_IMAGE && (empty($story->image) || $story->image==NULL))
               continue;

            //save counries
            $countryIds = [];
            foreach ( $story->relationCountries as $country )
            {
                $countryIds[] = $country->id;
            }

            //save subcategories
            $categoryIds = [];
            $deeplinkCategory = [];
            foreach ($story->relationSubCategories as $subcategory)
            {
                //category for deeplink, get the last category (chances are that it won't be trending because trending is saved first and I'm overriding here)
                $deeplinkCategory['subcategory_id']=$subcategory->id;
                $deeplinkCategory['subcategory_name']=$subcategory->name;

                //since order_by has to be unique for every parent category and subcategory, multiply with 1000 to get high range so you can add as many subcategories to it by summing with subcategory_id since it is unique
                //In that way you will get unique IDs for each parent and subcategory. Because app needs unique IDs to recognize parent category and its childer but also to filter news
                $relationParentCategory=$subcategory->relationParentCategory->order_by*Api::ORDER_BY_MULTIPLY;

                //check if parent category id is in array, because news can be added to multiple children categories with the same parent category
                if(!in_array ($relationParentCategory, $categoryIds))
                    $categoryIds[] = $relationParentCategory;


                $categoryIds[] = $relationParentCategory+$subcategory->order_by;
            }

            $list[] = StoryApi::storyContent($story,$countryIds,$categoryIds,$story->id,$deeplinkCategory);
            $i++;
        }


        //var_dump($list);
        return $list;
       /* return $this->render('@backend/views/site/index', [

            ]);*/
    }

    /*
    *  action to generate json for video
    */
    public function actionVideo($storyId)
    {
        $story = Story::findOne($storyId);
        return  StoryApi::formatVideo($story);
    }

    /*
    *  return single story
    */
    public function actionSingleStory($storyId)
    {
        $story=Story::find()
        ->where(['id'=>$storyId])
        ->with(['relationCountries','relationSubCategories.relationParentCategory', 'relationUser'])
        ->one();

        //check if story exists
        if(empty($story))
            return ["false"];

        $countryIds = [];
        foreach ( $story->relationCountries as $country )
        {
            $countryIds[] = $country->id;
        }

        $categoryIds = [];
        $deeplinkCategory = [];
        foreach ($story->relationSubCategories as $subcategory)
        {
            //category for deeplink, get the last category (chances are that it won't be trending because trending is saved first and I'm overriding here)
            $deeplinkCategory['subcategory_id']=$subcategory->id;
            $deeplinkCategory['subcategory_name']=$subcategory->name;

            $relationParentCategory=$subcategory->relationParentCategory->order_by*Api::ORDER_BY_MULTIPLY;

            //check if parent category id is in array, because news can be added to multiple children categories with the same parent category
            if(!in_array ($relationParentCategory, $categoryIds))
                $categoryIds[] = $relationParentCategory;


            $categoryIds[] = $relationParentCategory+$subcategory->order_by;
        }

        $list[] = StoryApi::storyContent($story, $countryIds, $categoryIds, $story->id, $deeplinkCategory);

        return $list;

    }

}