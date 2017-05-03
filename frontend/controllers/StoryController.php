<?php

namespace frontend\controllers;

use Yii;
use backend\models\Story;
use backend\models\Country;
use backend\models\CountryLanguage;
use frontend\models\search\StorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\components\Helpers as CommonHelpers;
use backend\components\Helpers as BackendHelpers;
use frontend\components\Helpers;
use frontend\components\LinkGenerator;
use backend\models\Language;

/**
 * StoryController implements the CRUD actions for Story model.
 */
class StoryController extends Controller
{
    public function beforeAction($action)
    {
        $action_tmp=Yii::$app->controller->action->id;

        date_default_timezone_set("UTC");
        $expire=time()+864000;
        CommonHelpers::createCookie(\Yii::$app->params['frontend_timezone_cookie'], "UTC", $expire);

        //set timezone, language and country
        Helpers::setLanguage();

        //don't check it in view so you can share this story on facebook
        if($action_tmp!="view" && $action_tmp!="sponsored-level-two")
        {
            //Helpers::setTimezone();
            Helpers::setCountry();
        }

        //becuase you are posting to view via ajax
        if($action_tmp=='edition')
        {
            $this->enableCsrfValidation = false;
        }

        //just redirect so it shows full url because I'm aking parameter from that url
        if($action_tmp=="index")
        {

            if(!isset($_GET["type"]) && !isset($_GET["categoryid"]) && !isset($_GET["name"]))
            {
                \Yii::$app->response->redirect(Url::to(["/story/index", "type"=>"category", "categoryid"=>1, "name"=>"top-stories" ]), 301)->send();
                die();
            }
        }
        return parent::beforeAction($action);
    }


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                /*'ruleConfig' => [
                    'class' => AccessRule::className(),
                ], */
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'edition', 'set-session-var', 'external', 'righthttpool', 'tophttpool', 'shareemail', 'sitemap', 'sponsored-level-two'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ]
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'edition' => ['post'],
                ],
            ],
        ];
    }

    /*
    * return list of checkbox for edition (countries)
    * $nativeLangauge -
        true = return langauge name in their native language, so Croatian is Hrvatski, Spanish is Espanol...
        false = return all in English
    */
    public function actionEdition($nativeLanguage)
    {
        //you are getting string now
        $selected = CommonHelpers::getCookie(\Yii::$app->params['frontend_edition_country_id_cookie']);
        //explode it to get array
        $selected = explode("-",$selected);

        $language = CommonHelpers::getCookie(\Yii::$app->params['frontend_language_id_cookie']);

        $query=Country::find()
        ->joinWith(["relationCountryLanguages"])
        ->where([CountryLanguage::tableName().'.language_id'=>$language]
        )->orWhere([Country::tableName().'.id'=>53])
        ->orderBy('order_index ASC, name ASC')
        ->all();

        if($nativeLanguage==true)
        {
            $array = ArrayHelper::map($query, 'id', function($query)
            {
                return Yii::t('app', $query->name);
            });
        }
        else
            $array = ArrayHelper::map($query, 'id', 'name');

        $result=Html::beginForm(Url::to(['/site/change-edition']), 'post', ['id'=>'change_edition_form']);
        $result.=Html::checkboxList('edition', $selected, $array, ['class'=>'div_bootbox_edition']);
        $result.=Html::endform();
        echo json_encode(['result'=>$result]);
    }

    /* Creating pages with httpool ads only so we can dynamically change them on slide change */
    public function actionRighthttpool()
    {
        echo '<body style="margin: 0; overflow: hidden;"><div id="httpool"><SCRIPT SRC="https://secure.adnxs.com/ttj?id=5742964&cb=[CACHEBUSTER][1]" TYPE="text/javascript"></SCRIPT></div></body>';
    }

    /**
     * Displays a single Story model.
     * @param integer $id
     * @return mixed
     * $id is id of story in Story
     * $page - it is page number where that story belongs so I can directly open next or previous story if user swipe
     * $type - "category" or "subcategory"
     * $categoryid - id of specific subcategory /CategoriresLevelOne
     */


//    function generate_hash($params, $secret) {
//        ksort($params);
//        $s = '';
//        foreach ($params as $key => $value) {
//            $s .= "$key=$value,";
//        }
//        $s = substr($s, 0, -1);
//        $hash = hash_hmac('md5', $s, $secret);
//        return $hash;
//        $hash = $_GET['hmac'];
//        unset($_GET['hmac']);
//        $signature = generate_hash($_GET, 'xyzKEY');
//        error_log("req hmac".$hash);
//        error_log("sig hmac".$signature);
//
//
//        if($hash != $signature) { header('HTTP/1.1 403 Forbidden'); echo "Signature did not match"; exit; }
//
//
//        if(check_duplicate_orders($_GET['oid']) { header('HTTP/1.1 403 Forbidden'); echo "Duplicate order"; exit; }
//
//
//        if(!give_item_to_player($_GET['sid'], $_GET['product']) { header('HTTP/1.1 500 Internal Server Error'); echo "Failed to give item to the player"; exit; }
//
//
//        if(save_order_number($_GET['oid']) { header('HTTP/1.1 500 Internal Server Error'); echo "Order ID saving failed, user granted item"; exit; }
//
//// everything OK, return "1"
//        header('HTTP/1.1 200 OK');
//        echo "1";
//
//    }




//    function show_pings() {
//
//        $country=Story::tableName();
//        $query=Country::find()->all();
//
////        echo '<pre>';
////        print_r($query);die;
//
//        if ($post_pingtrackbacks = $query) {
//
//            $number_of_pingtrackbacks = count($post_pingtrackbacks);
//
//            if ($number_of_pingtrackbacks == 1) {
//                echo "<div id='pingtrackback'><h3 style='font-size:16px;margin-bottom:10px'>One Pingback/Trackback</h3><ul>";
//            } else {
//                echo "<div id='pingtrackback'><h3 style='font-size:16px;margin-bottom:10px'>" . $number_of_pingtrackbacks . " Pingbacks/Trackbacks</h3><ul>";
//            }
//
//            foreach ($post_pingtrackbacks as $post_pingtrackback) {
//
//                echo "<strong>";
//                echo date( 'd F Y \a\t g:ma', strtotime( $post_pingtrackback->comment_date ));
//                echo "</strong><br>";
//                $comment_summary = $post_pingtrackback->comment_content;
//                echo substr( $comment_summary, 0, strrpos( substr( $comment_summary, 0, 90), ' ' ) ) . ' ...';
//
//                echo "\n<li><a href='";
//                echo $post_pingtrackback->comment_author_url;
//                echo "'>";
//                $author = $post_pingtrackback->comment_author;
//                echo html_entity_decode($author);
//                echo "</a>";
//                echo "</li>";
//            }
//            echo "</ul></div>";
//        }
//    }

    public function actionView($id, $seo_url, $page, $type, $name, $categoryid)
    {
//        $postback = $this->show_pings();

//        $isPostBack = false;
//
//        $referer = "";
//        $thisPage = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
//
////        echo '<pre>';
////        print_r($_SERVER);die;
//
//        if (isset($_SERVER['HTTP_REFERER'])){
//            $referer = $_SERVER['HTTP_REFERER'];
//        }
//
//        if ($referer == $thisPage){
//            $isPostBack = true;
//        }
//
//// Personally postback
//        $secret_key = 'key';
//
//// Persona.ly server IP addresses
//        $allowed_ips = array(
//            '46.162.194.162',
//        );
//
//// Proceess only requests from Persona.ly IP addresses
//// This is optional validation
//
//        if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
//            echo 0;
//            die();
//        }
//
//// Get params
//        $user_id = $_REQUEST['user_id'];
//        $amount = $_REQUEST['amount'];
//        $offer_id = $_REQUEST['offer_id'];
//        $app_id = $_REQUEST['app_id'];
//        $signature = $_REQUEST['signature'];
//        $offer_name = $_REQUEST['offer_name'];
//// Create validation signature
//        $validation_signature = md5($user_id . ':' . $app_hash . ':' . $secret_key); // the app_hash can be found in your app settings
//        if ($signature != $validation_signature) {
//            // Signatures not equal - send error code
//            echo 0;
//            die();
//        }
//// Validation was successful. Credit user process.
//        echo 1;
//        die();



        $model = $this->findViewModel($id, $seo_url);
        $image=BackendHelpers::backendDomain().Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE, $model->image, false);
        $description=substr($model->description, 0, 200)."...";

        //if($model->type == Story::TYPE_SPONSORED)
        //    $model->description = "SPONSORED - ".$model->description;

        if($model->relationStoryKeywords != NULL)
            $keywords = $model->relationStoryKeywords->keywords;
        else
            $keywords = "";

        //set language from unique story if it is different than current story, and set international edition
        $language_id = $model->language_id;
        if(CommonHelpers::getCookie(\Yii::$app->params['frontend_language_id_cookie']) != $language_id) {
            $language_code = Language::findById($language_id)->code;
            CommonHelpers::createCookie(\Yii::$app->params['frontend_language_id_cookie'], $language_id, NULL);
            CommonHelpers::createCookie(\Yii::$app->params['frontend_language_code_cookie'], $language_code, NULL);
            CommonHelpers::createCookie(\Yii::$app->params['frontend_edition_country_id_cookie'], 53, NULL);
            Yii::$app->language=$language_code;
        }

        //Facebook tags
        Helpers::facebookMetaTags($model->title, $image, $description);

        //Twitter tags
        Helpers::twitterMetaTags($model->title, $image, $description);

        //website tags
        Helpers::registerMetaTag($description, $keywords);

        //Set timezone for view if exists in cookie
        $timezone=CommonHelpers::getCookie(\Yii::$app->params['frontend_timezone_cookie']);

        if($timezone !== NULL)
            date_default_timezone_set($timezone);

        $date_published = $model->date_published;

        //get language and country id's
        $language['id'] = CommonHelpers::getCookie(\Yii::$app->params['frontend_language_id_cookie']);
        $country['id'] = CommonHelpers::getCookie(\Yii::$app->params['frontend_edition_country_id_cookie']);
        $countries = explode("-",$country['id']);

        //if no language id, get it
        if($language['id'] == NULL)
            $language = Helpers::detectLanguage();

        //if no country id or multiple countries selected, set international
        if($country['id'] == NULL || count($countries) > 1)
            $country['id'] = 53;

        $searchModel = new StorySearch();

        //get all normal (no sponsored ones) stories
        $dataProviderStory = $searchModel->searchStory(Yii::$app->request->queryParams, $type, $categoryid, $date_published);
        //get all sponsored stories
        $dataProviderSponsored = $searchModel->searchSponsored(Yii::$app->request->queryParams, $type, $categoryid, $date_published);

        //fill the array to pass to findStories
        $array['language'] = $language['id'];
        $array['country'] = $country['id'];
        //inject sponsored stories into normal stories
        $dataProvider = CommonHelpers::findStories($dataProviderStory, $dataProviderSponsored, $array);

        //$this->registerMetaTag(['name' => 'keywords', 'content' => 'yii, framework, php']);
        return $this->render('view', [
            'model' => $model,
            'page'=>$page,
            'type'=>$type,
            'name'=>$name,
            'categoryid'=>$categoryid,
            'dataProvider'=>$dataProvider
        ]);
    }

    /**
     * Generate sitemaps for Google for each language
     */
    public function actionSitemap()
    {
        $languages = Language::find()->all();

        $published=Story::STATUS_PUBLISHED;
        $stories = (new \yii\db\Query())
            ->select("stories.id, stories.language_id, stories.date_modified, stories.seo_url, categories_level_one.name AS Cname, category_stories.category_id AS Cid")
            ->from('stories')
            ->leftJoin('category_stories', 'stories.id = category_stories.story_id')
            ->leftJoin('categories_level_one', 'category_stories.category_id = categories_level_one.id')
            ->where(["status"=>$published])
            ->all();

        $string_start = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"  xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $string_end = '</urlset>';

        foreach($languages as $language) {
            $string[$language->id] = "";
        }

        foreach($stories as $story)
        {
            $params=['id'=>$story['id'],
                'seo_url'=>$story['seo_url'],
                'type'=>'subcategory',
                'page'=>'0',
                'name'=>Helpers::generateSubcategoryName($story['Cname']),
                'categoryid'=>$story['Cid']];

            $storyUrl=LinkGenerator::linkStoryView(NULL, $params, "full");

            $string[$story['language_id']] .= "<url>
            <loc>".$storyUrl."</loc>
            <lastmod>".date("c", strtotime($story['date_modified']))."</lastmod>
            <changefreq>never</changefreq>
            </url>\n";
        }

        foreach($languages as $language) {
            $sitemap = $string_start.$string[$language->id].$string_end;
            file_put_contents("sitemap/sitemap_".$language->code.".xml", $sitemap);
        }
    }

    /**
     * Displays a single Story in new tab with iframe.
     * @param integer $id
     * @return mixed
     * $id is id of story in Story
     */
    public function actionExternal($id)
    {
        //set theme file for showing external stories (story opened in iframe on source page)
        \Yii::$app->view->theme = new \yii\base\Theme([
            'pathMap' => ['@frontend/views' => '@frontend/themes/iframe/views'],
            //'baseUrl' => '@frontend/themes/iframe',
        ]);

        //find current story
        $model = Story::findOne($id);

        //get url and headers
        $url = $model->link;
        $headers = get_headers($url, 1);

        //check if url is blocked to open in iframe
        if(array_key_exists('X-Frame-Options', $headers) || array_key_exists('x-frame-options', $headers) || array_key_exists('Content-Security-Policy', $headers)  || array_key_exists('content-security-policy', $headers)) {
            \Yii::$app->response->redirect($url);
        }
        //if not, open in iframe
        else {
            return $this->render('iframe', [
                'model' => $model
            ]);
        }

    }


    /**
     * Creates a new Story model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Story();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Story model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Story model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findViewModel($id, $seo_url)
    {
        if (($model =  Story::find()->where(['id'=>$id, 'seo_url'=>$seo_url])->with(["relationStoryKeywords"])->one() ) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTophttpool()
    {
        echo '<body style="margin: 0; overflow: hidden;"><div id="httpool"><SCRIPT SRC="https://secure.adnxs.com/ttj?id=5742970&cb=[CACHEBUSTER][2]" TYPE="text/javascript"></SCRIPT></div></body>';
    }

    /**
    * Sharing stories via email
    */
    public function actionShareemail()
    {
        $email = $_POST['email'];
        $title = $_POST['title'];
        $url = $_POST['url'];

        $content="<b>".$title."</b><br><br>";
        $content.='<a href="'.$url.'" style="background-color:#82AC40; padding:7px; text-decoration:none; display: inline-block;"><span style="color: white;">'.Yii::t("app","Read More").'</span></a>';

        $subject=Yii::t("app", "Check out this great article I read on BORN2INVEST");
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {

            //Create a new PHPMailer instance
            $mail = new \PHPMailer;
            $mail->CharSet = 'UTF-8';
            //Set who the message is to be sent from
            $mail->setFrom('no-reply@born2invest.com', 'Born2Invest');
            //Set who the message is to be sent to
            $mail->addAddress($email);
            //Set the subject line
            $mail->Subject = $subject;
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $mail->msgHTML($content);
            //send the message, check for errors
            $mail->send();

        }
    }

    /**
     * Lists all Story models.
     * @return mixed
     * $categoryid - id of specific subcategory /CategoriresLevelOne
     * $name="trending", name of subcategory or category
     * $type - category/subcategory
     */
    public function actionIndex($type, $categoryid, $name)
    {
        /*
        if(isset($_POST["date_published"]))
            $date_published=$_POST["date_published"];
        else
            $date_published=NULL;

        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$type, $categoryid, $date_published);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'type'=>$type,
            'name'=>$name,
            'categoryid'=>$categoryid
        ]);
        */

        if(isset($_POST["date_published"]))
            $date_published=$_POST["date_published"];
        else
            $date_published=NULL;

        //get language and country id's
        $language['id'] = CommonHelpers::getCookie(\Yii::$app->params['frontend_language_id_cookie']);
        $country['id'] = CommonHelpers::getCookie(\Yii::$app->params['frontend_edition_country_id_cookie']);
        $countries = explode("-",$country['id']);

        //if no language id, get it
        if($language['id'] == NULL)
            $language = Helpers::detectLanguage();

        //if no country id or multiple countries selected, set international
        if($country['id'] == NULL || count($countries) > 1)
            $country['id'] = 53;

        $searchModel = new StorySearch();

        //get all normal (no sponsored ones) stories
        $dataProviderStory = $searchModel->searchStory(Yii::$app->request->queryParams, $type, $categoryid, $date_published);
        //get all sponsored stories
        $dataProviderSponsored = $searchModel->searchSponsored(Yii::$app->request->queryParams, $type, $categoryid, $date_published);

        //fill the array to pass to findStories
        $array['language'] = $language['id'];
        $array['country'] = $country['id'];
        //inject sponsored stories into normal stories
        $dataProvider = CommonHelpers::findStories($dataProviderStory, $dataProviderSponsored, $array);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'type'=>$type,
            'name'=>$name,
            'categoryid'=>$categoryid
        ]);
    }

}
