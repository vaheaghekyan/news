<?php

namespace backend\modules\settings\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\AccessRule;
use yii\filters\AccessControl;
use backend\models\User;
use backend\models\Language;

/**
 * TranslationController implements the CRUD actions for SettingsSocialNetworks model.
 */
class TranslationController extends Controller
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
                        'actions' => ['index', 'update', 'index-android', 'update-android', 'index-ios', 'update-ios', 'generate-ios', 'generate-android', 'index-web', 'update-web'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    //**************************GENERATE LANG***************************************
    private function generateLang($device, $lang)
    {
        $messages_dir=Language::messageDir($lang);

        //get directory of current language
        $directory = opendir($messages_dir);

        echo "<xmp>";
        if($device=="android")
        {
           echo '<?xml version="1.0" encoding="utf-8"?>';
           echo "\n";
           echo '<resources>';
           echo "\n";
        }
       //Scan through the folder one file at a time
       while(($file = readdir($directory)) != false)
       {

           //load only files, not folders
           if($file!="." && $file!=".." &&  $file!="app.php" && !is_dir($messages_dir.$file))
           {
                $file=include $messages_dir.$file;
                foreach($file as $key=>$value)
                {
                    if($device=="android")
                    {
                        //replace \' with ' if there are any so you can later add \ to ' because maybe someone just added new ', otherwise it might add extra slash like: \\'
                        $value=str_replace("\'", "'", $value);
                        //replace ' with \', because android needs it
                        $value=str_replace("'", "\'", $value);
                    }
                    else
                    {
                        //since iOS requires double quotes, reconstruct it
                        //replace " with \" if there are any, because now there are double quotes and you need to escape them
                        $value=str_replace('"', '\"', $value);
                        //replace \' with ', since there are double quotes now and you dont need \'
                        $value=str_replace("\'", "'", $value);
                    }

                    if($device=="android")
                    {
                        echo '<string name="'.$key.'">'.$value.'</string>';
                    }
                    else
                        echo '"'.$key.'"="'.$value.'";';

                    echo "\n";

                }
           }


       }
        if($device=="android")
            echo "\n</resources>";
        echo "</xmp>";
    }
    /**
    * generate lang file for iOS, only strings that you can copy
    */
    public function actionGenerateIos()
    {
        echo '<meta charset="UTF-8">';
        $lang=Language::findById($_GET["lang"]);
        $lang="$lang->code/ios";
        $this->generateLang("ios", $lang);
    }

    /*
    *  generate strings for android lang file
    */
    public function actionGenerateAndroid()
    {
        echo '<meta charset="UTF-8">';
        $lang=Language::findById($_GET["lang"]);
        $lang="$lang->code/android";
        $this->generateLang("android", $lang);
    }

    //**************************INDEX-UPDATE***************************************
    /*
    *  used in index actions
    * $type = new/old
    * $lang=current language, for example: hr, hr/ios, hr/android
    * $en_dir = english directory: en, en/ios, en/android
    * $currentAction - Yii::$app->controller->action->id, e.g.: index, index-ios
    */
    private function index($type, $lang, $en_dir, $currentAction=NULL)
    {
        if($currentAction=="index-web")
        {
            $messages_dir=Language::frontendMessageDir($lang);
            $messages_dir_en=Language::frontendMessageDir($en_dir);
        }
        else
        {
            $messages_dir=Language::messageDir($lang);
            $messages_dir_en=Language::messageDir($en_dir);
        }


        $string=[];

        //go through english language and check if its value exists in current language
        $directory = opendir($messages_dir_en);
       //Scan through the folder one file at a time
       while(($file = readdir($directory)) != false)
       {

           if($file!="." && $file!=".." &&  $file!="app.php" && !is_dir($messages_dir_en.$file))
            {
                //include array of file into variable
                $en_file=include $messages_dir_en.$file;
                $current_file=include $messages_dir.$file;

                //if they adding new strings that don't exist in current language you will scan english file
                if($type=="new")
                {
                    //go through english file and check if its key exists in current language
                    foreach($en_file as $key=>$value)
                    {
                        //check if key exists in new file, if it doesn't exist, put it in array. That means that new file doesn't have that string translated and now you have option to add it
                        if(!array_key_exists($key,$current_file))
                        {
                            //$string["general.php"]["TOP STORIES"]="TOP PRIČE"
                            $string[$file][$key]=$value;
                        }
                    }
                }
                else
                {
                    //go through current lang file and take everything
                    foreach($current_file as $key=>$value)
                    {
                        //$string["general.php"]["TOP STORIES"]="TOP PRIČE"
                        $string[$file][$key]=$value;
                    }
                }

                //save english word here so you can show it as original above text field
                foreach($en_file as $key=>$value)
                {
                    //if file is lang.php save its key not value, because my en lang.php is translated to every language locally so you have to show keys of lang.php file on frontend, because they are in english. But only for index action, because you have lang.php in android and ios folder
                    if($file=="lang.php" && $currentAction=="index")
                        $value=$key;

                    //$string["TOP STORIES"]="TOP STORIES"
                    $en_word[$key]=$value;
                }

            }
       }

       /*
       $string=
       Array
        (
            [categories.php] => Array
            (
                [TOP STORIES] => TOP STORIES
                [FINANCE] => FINANCE
                [BUSINESS] => BUSINESS
                [METALS] => METALS
                [LUXURY] => LUXURY
                [Trending] => Trending
                [Investing] => Investing
                [Real Estate] => Real Estate
                [Tech] => Tech
                [Your Money] => Your Money
                [Economy] => Economy
                [Markets] => Markets
                [Media] => Media
                [Auto] => Auto
                [Mining] => Mining
                [Silver] => Silver
                [Gold] => Gold
                [Other] => Other
                [Nickel] => Nickel
                [Wealth] => Wealth
                [Collect] => Collect
                [Health] => Health
                [Travel] => Travel
                [Rare] => Rare
                [Life] => Life
                [Drive] => Drive
            )
            [countries.php] => Array
            (...)
        )
       */

        //Language object
        $current_language=Language::getCurrentLanguage();

        return ['current_language'=>$current_language, 'en_word'=>$en_word, 'string'=>$string];
    }



    /*
    * $translate = $_POST["translate"]  - submitted text inputs from all tabs as array of values
    * $lang- current langauge for example: en, so you can get folder for that language and save files there
    * $currentAction - Yii::$app->controller->action->id, e.g.: index, index-ios
    */
    private function update($translate, $type, $lang, $currentAction)
    {

        /*
           $translate=
           Array
            (
                [categories.php] => Array
                (
                    [TOP STORIES] => TOP STORIES
                    [FINANCE] => FINANCE
                    [BUSINESS] => BUSINESS
                    [METALS] => METALS
                    [LUXURY] => LUXURY
                    [Trending] => Trending
                    [Investing] => Investing
                    [Real Estate] => Real Estate
                    [Tech] => Tech
                    [Your Money] => Your Money
                    [Economy] => Economy
                    [Markets] => Markets
                    [Media] => Media
                    [Auto] => Auto
                    [Mining] => Mining
                    [Silver] => Silver
                    [Gold] => Gold
                    [Other] => Other
                    [Nickel] => Nickel
                    [Wealth] => Wealth
                    [Collect] => Collect
                    [Health] => Health
                    [Travel] => Travel
                    [Rare] => Rare
                    [Life] => Life
                    [Drive] => Drive
                )
                [countries.php] => Array
                (...)
            )
           */

        if($currentAction=="update-web")
        {
            $messages_dir=Language::frontendMessageDir($lang);//find directory for current lanauge
        }
        else
        {
            $messages_dir=Language::messageDir($lang);//find directory for current lanauge
        }


        foreach($translate as $file_name=>$file_array) //$file_name = categories.php, $file_array=array within categories.php
        {
            $temp=[];//empty on beginning


            //if you are adding new string to a file you have to merge existing array of that file with new array
            if($type=="new")
            {
                $old_array=include $messages_dir.$file_name; //include array of current file into variable
                $arrays=array_merge($old_array,$file_array); //merge two arrays because old array(file) might have some values
            }
            //otherwise if you are updating old file you just put everything in it as new since all fields will be submitted
            else
                $arrays=$file_array;


            foreach($arrays as $string_key=>$string_value) //$string_key=TOP STORIES, $string_value=TOP PRIČE
            {
                //first strip all slashes from single quote
                $string_value=str_replace("\'", "'", $string_value);
                $string_key=str_replace("\'", "'", $string_key);
                //add one slash on single quotes because there could be new single quotes where you have to add slashes
                $string_value=str_replace("'", "\'", $string_value);
                $string_key=str_replace("'", "\'", $string_key);
                $temp[]="'$string_key'=>'$string_value'"; //'TOP STORIES'=>'TOP STORIES',
            }

            $file_content="<?php return [".implode(",\n",$temp)."] ?>";

            if(file_put_contents($messages_dir.$file_name,$file_content))
                $success=true;
            else
                $success=false;
        }

        if($success==true)
            Yii::$app->session->setFlash('success', Yii::t('app', 'Everything went fine'));
        else
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Something was wrong'));

    }

    //--------------------------------- WEB Version TRANSLATION -------------------------------
    /**
     * Lists all SettingsSocialNetworks models.
     * @return mixed
     * $type=new - for updating only new strings, old - for updating everything
     */
    public function actionIndexWeb($type="new")
    {
        $lang=Language::getCurrent();

        //ONLY DARIO CAN EDIT IT
        $user=Yii::$app->user->getIdentity();
        if($user->id!=\Yii::$app->params['adminId'] && $lang=="en")
            throw new \yii\web\HttpException(403, 'Only Dario can edit English language');

        $en_dir="en";
        $currentAction=Yii::$app->controller->action->id;
        $index=$this->index($type, $lang, $en_dir, $currentAction);

        return $this->render('index', [
        'string'=>$index['string'],
        'current_language'=>$index['current_language'],
        'type'=>$type,
        'en_word'=>$index['en_word'],
        ]);
    }


    /**
     * update lang files
     * $type=new - for updating only new strings, old - for updating everything
     */
    public function actionUpdateWeb($type)
    {
        if(isset($_POST["submit_translation"]) && isset($_POST["translate"]))
        {
            $currentAction=Yii::$app->controller->action->id;
            $lang=Language::getCurrent();//get current language, hr, en...
            $this->update($_POST["translate"], $type, $lang, $currentAction);
        }

        $this->redirect('index-web');
    }

    //--------------------------------- WEB TRANSLATION -------------------------------
    /**
     * Lists all SettingsSocialNetworks models.
     * @return mixed
     * $type=new - for updating only new strings, old - for updating everything
     */
    public function actionIndex($type="new")
    {
        $lang=Language::getCurrent();

        //ONLY DARIO CAN EDIT IT
        $user=Yii::$app->user->getIdentity();
        if($user->id!=\Yii::$app->params['adminId'] && $lang=="en")
            throw new \yii\web\HttpException(403, 'Only Dario can edit English language');

        $en_dir="en";
        $currentAction=Yii::$app->controller->action->id;
        $index=$this->index($type, $lang, $en_dir, $currentAction);

        return $this->render('index', [
        'string'=>$index['string'],
        'current_language'=>$index['current_language'],
        'type'=>$type,
        'en_word'=>$index['en_word'],
        ]);
    }


    /**
     * update lang files
     * $type=new - for updating only new strings, old - for updating everything
     */
    public function actionUpdate($type)
    {
        if(isset($_POST["submit_translation"]) && isset($_POST["translate"]))
        {
            $currentAction=Yii::$app->controller->action->id;
            $lang=Language::getCurrent();//get current language, hr, en...
            $this->update($_POST["translate"], $type, $lang, $currentAction);
        }

        $this->redirect('index');
    }

    //--------------------------------- iOS TRANSLATION -------------------------------
    /**
     * Lists all SettingsSocialNetworks models.
     * @return mixed
     * $type=new - for updating only new strings, old - for updating everything
     */
    public function actionIndexIos($type="new")
    {
        $lang=Language::getCurrent();

        //ONLY DARIO CAN EDIT IT
        $user=Yii::$app->user->getIdentity();
        if($user->id!=\Yii::$app->params['adminId'] && $lang=="en")
            throw new \yii\web\HttpException(403, 'Only Dario can edit English language');

        $lang="$lang/ios";
        $en_dir="en/ios";

        $index=$this->index($type, $lang, $en_dir);

        return $this->render('index', [
        'string'=>$index['string'],
        'current_language'=>$index['current_language'],
        'type'=>$type,
        'en_word'=>$index['en_word'],
        ]);
    }

    /**
     * update lang files
     * $type=new - for updating only new strings, old - for updating everything
     */
    public function actionUpdateIos($type)
    {
        if(isset($_POST["submit_translation"]) && isset($_POST["translate"]))
        {
            $currentAction=Yii::$app->controller->action->id;
           $lang=Language::getCurrent();//get current language, hr, en...
           $lang="$lang/ios";
           $this->update($_POST["translate"], $type, $lang, $currentAction);
        }

        $this->redirect('index-ios');
    }

    //--------------------------- ANDROID TRANSLATION ----------------------------------
    //http://php.net/manual/en/function.xml-parse-into-struct.php
    public function actionIndexAndroid($type="new")
    {
        $lang=Language::getCurrent();

        //ONLY DARIO CAN EDIT IT
        $user=Yii::$app->user->getIdentity();
        if($user->id!=\Yii::$app->params['adminId'] && $lang=="en")
            throw new \yii\web\HttpException(403, 'Only Dario can edit English language');

        $lang="$lang/android";
        $en_dir="en/android";

        $index=$this->index($type, $lang, $en_dir);

        return $this->render('index', [
        'string'=>$index['string'],
        'current_language'=>$index['current_language'],
        'type'=>$type,
        'en_word'=>$index['en_word'],
        ]);
    }


    public function actionUpdateAndroid($type)
    {
        if(isset($_POST["submit_translation"]) && isset($_POST["translate"]))
        {
            $currentAction=Yii::$app->controller->action->id;
            $lang=Language::getCurrent();//get current language, hr, en...
            $lang="$lang/android";
            $this->update($_POST["translate"], $type, $lang, $currentAction);
        }

        $this->redirect('index-android');
    }


}
