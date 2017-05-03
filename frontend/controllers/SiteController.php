<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use backend\models\Language;
use backend\models\CategoriesLevelOne;
use backend\models\Category;
use backend\models\Country;
use backend\models\CountryStory;
use backend\models\Story;
use backend\models\CategoryStory;

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
 * Site controller
 */
class SiteController extends Controller
{

    public function beforeAction($action)
    {
        $action_tmp=Yii::$app->controller->action->id;

        date_default_timezone_set("UTC");
        $expire=time()+864000;
        CommonHelpers::createCookie(\Yii::$app->params['frontend_timezone_cookie'], "UTC", $expire);

        //set timezone, language and country
        Helpers::setLanguage();
        if($action_tmp!="detect-timezone" && $action_tmp!="traffic") {
            //Helpers::setTimezone();
        }
        Helpers::setCountry();

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
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['change-language', 'change-category', 'change-edition','detect-timezone'],
                        'allow' => true,
                        'roles' => ['@', '?'],
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
     public function actionTest()
    {
        $url_params=['id'=>55, 'seo_url'=>"a-b-c", 'type'=>"category", 'name'=>"trending", 'categoryid'=>5, 'page'=>0];
        $a=\frontend\components\LinkGenerator::linkStoryView(null, $url_params,"short");
        var_dump($a);
        /*$a=Story::find()
                    ->where(['language_id' => 7])
                    //->andWhere("type=:sponsored", [':sponsored'=>Story::TYPE_SPONSORED])
                    ->orderBy(['date_published' => SORT_DESC])
                    ->limit(10)
                    ->asArray()
                    ->all();

        $rand_keys = array_rand($a, 1);
       // var_dump($rand_keys);
        var_dump($a[$rand_keys]);  */
    }

    /*
    *  detect timezone
    */
    public function actionDetectTimezone()
    {
        $expire=time()+864000;
        //if JS detected timezone set timezone, else set server's timezone
        if(isset($_GET["timezone"]))
        {
            CommonHelpers::createCookie(\Yii::$app->params['frontend_timezone_cookie'], $_GET["timezone"], $expire);
            return $this->redirect(['/story/1/category/top-stories']);
        }

        return $this->render('detect_timezone');

    }

    /**
    *  change edition/country
    * detect country by IP
    */
    public function actionChangeEdition()
    {
        if(isset($_POST["edition"]) && !empty($_POST["edition"]))
        {
            foreach($_POST["edition"] as $value)
            {
                $cookie[]=$value;
            }

            CommonHelpers::createCookie(\Yii::$app->params['frontend_edition_country_id_cookie'], implode("-",$cookie), NULL);
        }
        $this->goHome();

    }
    /*
    *  change language
    */
    public function actionChangeLanguage()
    {

        if (isset($_GET['language']))
        {
            $language=(int)$_GET['language'];

            //get language from database
            $lang_temp=Language::findById($language); //id in languages table
            //if you cannot find anything in database
            if(empty($lang_temp))
            {
                $language_id=7; //set english language
                $language_code='en';
            }
            else
            {
                $language_id=$lang_temp->id;
                $language_code=$lang_temp->code;
            }
            // add a new cookie
            CommonHelpers::createCookie(\Yii::$app->params['frontend_language_id_cookie'], $language_id, NULL);
            CommonHelpers::createCookie(\Yii::$app->params['frontend_language_code_cookie'], $language_code, NULL);

            //when changing language - set International as default edition
            CommonHelpers::createCookie(\Yii::$app->params['frontend_edition_country_id_cookie'], 53, NULL);


            return $this->redirect(['/story/index']);
        }
    }

    /*
    *  redirect traffic from outside to defined story
    */
    public function actionTraffic()
    {
        //detect language
        $language = Helpers::detectLanguage();

        //set app language from detected language
        Yii::$app->language=$language['code'];

        //depending on the language, set story which you want to redirect visitors to (story id and category id)
        switch ($language['code']) {
        //english
        case "en":
            $story_id = 18051;
            $category_id = 9;
            break;
        //croatian
        case "hr":
            $story_id = 17515;
            $category_id = 59;
            break;
        //german
        case "de":
            $story_id = 18082;
            $category_id = 1;
            break;
        //french
        case "fr":
            $story_id = 16751;
            $category_id = 59;
            break;
        //spanish
        case "es":
            $story_id = 8782;
            $category_id = 8;
            break;
        //russian
        case "ru":
            $story_id = 13;
            $category_id = 11;
            break;
        //ukrainian
        case "uk":
            $story_id = 13;
            $category_id = 11;
            break;
        //thai
        case "th":
            $story_id = 13;
            $category_id = 11;
            break;
        //hungarian
        case "hu":
            $story_id = 13;
            $category_id = 11;
            break;
        //macedonian
        case "mk":
            $story_id = 13;
            $category_id = 11;
            break;
        //romanian
        case "ro":
            $story_id = 13;
            $category_id = 11;
            break;
        //serbian
        case "sr":
            $story_id = 13;
            $category_id = 11;
            break;
        //czech
        case "cs":
            $story_id = 13;
            $category_id = 11;
            break;
        //arabic
        case "ar":
            $story_id = 13;
            $category_id = 11;
            break;
        //portuguese
        case "pt":
            $story_id = 13;
            $category_id = 11;
            break;
        //indonesian
        case "id":
            $story_id = 13;
            $category_id = 11;
            break;
        //vietnamese
        case "vi":
            $story_id = 13;
            $category_id = 11;
            break;
        //malay
        case "ms":
            $story_id = 13;
            $category_id = 11;
            break;
        //greek
        case "el":
            $story_id = 13;
            $category_id = 11;
            break;
        //turkish
        case "tr":
            $story_id = 13;
            $category_id = 11;
            break;
        //hindi
        case "hi":
            $story_id = 13;
            $category_id = 11;
            break;
        //bulgarian
        case "bg":
            $story_id = 13;
            $category_id = 11;
            break;
        //bengali
        case "bn":
            $story_id = 13;
            $category_id = 11;
            break;
        //uzbek
        case "uz":
            $story_id = 13;
            $category_id = 11;
            break;
        //swedish
        case "sv":
            $story_id = 13;
            $category_id = 11;
            break;
        }

        //set parameters which are always the same
        $page = 0;
        $type = "subcategory";

        /*$model = Story::find()->where(['id'=>$story_id])->with(["relationSubCategories"])->one();

        foreach($model->relationSubCategories as $subcategory) {
            if($subcategory['id'] == $category_id)
                $name = $subcategory->name;
        }

        $params=['id'=>$model->id, 'seo_url'=>$model->seo_url, 'type'=>$type, 'page'=>$page, 'name'=>$name, 'categoryid'=>$category_id];*/

        //get defined story and name, set params for link generator
        $model = CategoryStory::find()->where(['story_id'=>$story_id, 'category_id'=>$category_id])->with(['relationStory'])->with(['relationCategory'])->one();
        $name = $model->relationCategory->name;

        $name = Helpers::generateSubcategoryName($name);

        $params=['id'=>$model->story_id, 'seo_url'=>$model->relationStory->seo_url, 'type'=>$type, 'page'=>$page, 'name'=>$name, 'categoryid'=>$category_id];
        $storyUrl_full=LinkGenerator::linkStoryView(NULL, $params, "full");

        //redirect to story
        \Yii::$app->response->redirect($storyUrl_full);
    }

    /*
    *  change category
    */
    /*public function actionChangeCategory()
    {
        //set cookie for level_one_category
        if (isset($_GET['levelonecategory']))
        {
            $subcategory=(int)$_GET['levelonecategory'];

            //get category from database
            $subcategory_temp=CategoriesLevelOne::findOne($subcategory); //id in categories_level_one table
            //if you cannot find anything in database
            if(empty($subcategory_temp))
            {
                $subcategory_id=1; //set Trending as category
            }
            else
            {
                $subcategory_id=$subcategory_temp->id;
            }
            //cookie
            $cookies=Yii::$app->response->cookies;
            // add a new cookie
            CommonHelpers::createCookie(\Yii::$app->params['frontend_level_one_category_id_cookie'], $subcategory_id);


            //remove cookies for parent category, because user/guest can switch between parent and child categories, so you can easily detect what he wants
            CommonHelpers::removeCookie(\Yii::$app->params['frontend_parent_category_id_cookie']);

            return $this->redirect(['/story/index']);
        }

        else if (isset($_GET['category']))
        {
            $category=(int)$_GET['category'];

            //get category from database
            $category_temp=Category::findOne($category); //id in categories_level_one table
            //if you cannot find anything in database
            if(empty($category_temp))
            {
                $category_id=1; //set TOP STORIES as category
            }
            else
            {
                $category_id=$category_temp->id;
            }
            // add a new cookie
            CommonHelpers::createCookie(\Yii::$app->params['frontend_parent_category_id_cookie'], $category_id);

            //remove cookies for level one category, because user/guest can switch between parent and child categories, so you can easily detect what he wants
            CommonHelpers::removeCookie(\Yii::$app->params['frontend_level_one_category_id_cookie']);


            return $this->redirect(['/story/index']);
        }
    }  */

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
