<?php
namespace backend\controllers;

use backend\models\ForgotPasswordForm;
use backend\models\Language;
use backend\models\Continent;
use backend\models\Country;
use backend\models\Story;
use backend\models\User;
use backend\models\CountryStory;
use backend\components\Helpers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\models\LoginForm;
use backend\models\ContactForm;
use common\components\Helpers as CommonHelpers;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout', 'contact'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['contact-everyone'],
                        'allow' => true,
                        'roles' => [User::ROLE_SUPERADMIN, User::ROLE_MARKETER],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionTest()
    {
        echo  Yii::getAlias("@webroot");/*
        $date=strtotime("2015-10-20 21:03:54");
        $r=\backend\models\Story::find()->limit(100)->orderBy("date_published DESC")->where("status='PUBLISHED'")->offset(12)->all();

        foreach($r as $v)
            echo "[$v->date_published] ".$v->title."<br>";  */

       /* $i=1;
        $pagination=[];
        foreach($r as $v)
        {
            if(strtotime($v->date_published)>=$date)
                continue;

            $pagination[]=$v;

            if($i==20)
                break;
            $i++;
        }

        foreach($pagination as $v)
            echo "[$v->date_published] ".$v->title."<br>"; */
    }
    /*
    *  contact every editor
    */
    public function actionContactEveryone()
    {  
        if(isset($_POST["send_email"]))
        {
             $file=[];
            //upload file and get file name
            if(isset($_FILES['attachment']))
            {
                foreach($_FILES['attachment']["tmp_name"] as $key => $value )
                {
                    $file_name = $_FILES['attachment']['name'][$key];
                    //$file_size = $_FILES['attachment']['size'][$key];
                    $file_tmp = $_FILES['attachment']['tmp_name'][$key];
                    //$file_type = $_FILES['attachment']['type'][$key];

                    //if it is note empty then upload it
                    if(!empty($file_name))
                        $file[]=Helpers::fileUpload($file_name, $file_tmp, Story::PATH_TEMP_IMAGE);
                }
            }

            //remove all whitespaces with nospaces and explode it(create array)
            $exclude_email_array=explode(",", str_replace(" ","",$_POST["exclude_email"]));
            //current logged user
            $currentLoggedUser=Yii::$app->user->getIdentity();
            //all users
            $users=User::find()->all();
            CommonHelpers::sendEmailToEditors($_POST["subject"], $_POST["body"], $currentLoggedUser, $users, $exclude_email_array, $file);
        }

       return $this->render('contact-everyone');
    }

    /*
    *  send email to me
    */
    public function actionContact()
    {
        if(isset($_POST['send_email']))
        {
            $user=Yii::$app->user->getIdentity();
            $from_email=$user->email;
            $name_from=$user->name;
            CommonHelpers::sendEmailToAdmin('Backend Contact', $_POST['message'], $from_email, $name_from);
        }
        return $this->render('contact');
    }


    /*public function actionMigrateUp()
    {
        $languages = Language::find()->all();
        foreach ( $languages as $language) {

            $country    = Country::getWorldwide($language->id);
            $list       = Story::find()->where(['language_id' => $language->id])->all();
            foreach ( $list as $story ) {

                if ( $story->getCountries()->count() == 0 ) {

                    $countryStory = new CountryStory();
                    $countryStory->country_id   = $country->id;
                    $countryStory->story_id     = $story->id;
                    $countryStory->save(false);

                }

            }

        }
    }*/

    public function actionDownloadimage( $storyId )
    {

        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Connection: close");
        header('Content-type: image/jpeg');

        if ( $storyId && ( $story = Story::findOne($storyId) ) && $story->image ) {


            readfile(Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE, $story->image, true));
        } else {

            throw new \Exception("Requested image has not been found");

        }
        exit;
    }

    public function actionDownloadvideo( $storyId )
    {

        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Connection: close");
        header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header ( "Content-Type: application/force-download" );
        header ( "Content-Type: application/octet-stream" );
        header ( "Content-Type: application/download" );

        if ( $storyId && ( $story = Story::findOne($storyId) ) && $story->video ) {


            header ( "Content-Disposition: attachment; filename=\"" . $story->video . "\";" );
            readfile( Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_VIDEO, $story->video, true));

        } else {

            throw new \Exception("Requested image has not been found");

        }
        exit;
    }

    public function actionIndex()
    {
        if ( Yii::$app->user->isGuest ) {

            return $this->redirect(array("site/login"));

        }
        return $this->redirect(array ( "admin/index",  "language" => Language::DEFAULT_LANGUAGE ));
    }

    /*
    * user login
    */
    public function actionLogin()
    {
        \Yii::$app->view->theme = new \yii\base\Theme([
            'pathMap' => ['@backend/views' => '@backend/themes/login/views'],
            //'baseUrl' => '@backend/themes/login',
        ]);

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
        {

            if ( $model->language > 0 )
            {
                $language = Language::findOne( $model->language );
            }
            else
            {
                $languages = Yii::$app->user->getIdentity()->getLanguages()->all();
                if ( count($languages) > 0)
                {
                    foreach ($languages as $language)
                    {
                        if ($language->code == Language::DEFAULT_LANGUAGE) {
                            break;
                        }
                    }
                }
                else
                {
                    $language = Language::findByCode( Language::DEFAULT_LANGUAGE );
                }

            }
            //set user's language in backend, from database
            Language::setLanguage( $language->code );
            Yii::$app->session->setFlash('success', Yii::t("app", 'Welcome to Born2Invest News Admin. Please be careful.'));
            return $this->redirect(array("admin/index", "language" => $language->code ));

        }
        else
        {
            if ( !$model->language ) {
                $model->language = Language::findByCode( Language::DEFAULT_LANGUAGE )->id;
            }
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /*
    *  change language of CMS from dropdown list
    */
    public function actionChangeLanguage()
    {

        if ( ($language = Yii::$app->request->getQueryParam("languageCode") ) )
        {
            Language::setLanguage( $language );

            $rote = Yii::$app->request->getQueryParam("route", 'admin/index');
            if ( strstr($rote, "update") !== false  ) {

                $rote = "story/index";
            }
            return $this->redirect([$rote, 'language' => $language]);
        }
        return $this->goBack();

    }

    /*
    *  if you forgot password set new pass
    */
    public function actionForgotPassword()
    {
        \Yii::$app->view->theme = new \yii\base\Theme([
            'pathMap' => ['@backend/views' => '@backend/themes/login/views'],
            //'baseUrl' => '@backend/themes/login',
        ]);

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new ForgotPasswordForm();
        if ( $model->load(Yii::$app->request->post()) && $model->sendLink() )
        {

            Yii::$app->session->setFlash('success', Yii::t("app", "We have sent an email with a new password. Please check your email."));
            return $this->refresh();

        }

        return $this->render("forgot-password", array(
            'model'     => $model
        ));
    }

    public function actionLogout()
    {
        // unset cookies
        foreach ($_COOKIE as $c_id => $c_value)
        {
            setcookie($c_id, NULL, 1, "/");
        }
        Yii::$app->user->logout();

        return $this->goHome();
    }

	/*
	* this is used for showing errors
	*/
	public function actionError()
	{
	  $exception = Yii::$app->errorHandler->exception;
		if ($exception !== null) {
			return $this->render('error', ['exception' => $exception]);
		}
	}


	/*public function actionInsertTimezones()
	{
		$arr= [
"Africa/Abidjan","Africa/Accra","Africa/Addis_Ababa","Africa/Algiers","Africa/Asmara","Africa/Asmera","Africa/Bamako","Africa/Bangui","Africa/Banjul","Africa/Bissau","Africa/Blantyre","Africa/Brazzaville","Africa/Bujumbura","Africa/Cairo","Africa/Casablanca","Africa/Ceuta","Africa/Conakry","Africa/Dakar","Africa/Dar_es_Salaam","Africa/Djibouti","Africa/Douala","Africa/El_Aaiun","Africa/Freetown","Africa/Gaborone","Africa/Harare","Africa/Johannesburg","Africa/Juba","Africa/Kampala","Africa/Khartoum","Africa/Kigali","Africa/Kinshasa","Africa/Lagos","Africa/Libreville","Africa/Lome","Africa/Luanda","Africa/Lubumbashi","Africa/Lusaka","Africa/Malabo","Africa/Maputo","Africa/Maseru","Africa/Mbabane","Africa/Mogadishu","Africa/Monrovia","Africa/Nairobi","Africa/Ndjamena","Africa/Niamey","Africa/Nouakchott","Africa/Ouagadougou","Africa/Porto-Novo","Africa/Sao_Tome","Africa/Timbuktu","Africa/Tripoli","Africa/Tunis","Africa/Windhoek","America/Adak","America/Anchorage","America/Anguilla","America/Antigua","America/Araguaina","America/Argentina/Buenos_Aires","America/Argentina/Catamarca","America/Argentina/ComodRivadavia","America/Argentina/Cordoba","America/Argentina/Jujuy","America/Argentina/La_Rioja","America/Argentina/Mendoza","America/Argentina/Rio_Gallegos","America/Argentina/Salta","America/Argentina/San_Juan","America/Argentina/San_Luis","America/Argentina/Tucuman","America/Argentina/Ushuaia","America/Aruba","America/Asuncion","America/Atikokan","America/Atka","America/Bahia","America/Bahia_Banderas","America/Barbados","America/Belem","America/Belize","America/Blanc-Sablon","America/Boa_Vista","America/Bogota","America/Boise","America/Buenos_Aires","America/Cambridge_Bay","America/Campo_Grande","America/Cancun","America/Caracas","America/Catamarca","America/Cayenne","America/Cayman","America/Chicago","America/Chihuahua","America/Coral_Harbour","America/Cordoba","America/Costa_Rica","America/Creston","America/Cuiaba","America/Curacao","America/Danmarkshavn","America/Dawson","America/Dawson_Creek","America/Denver","America/Detroit","America/Dominica","America/Edmonton","America/Eirunepe","America/El_Salvador","America/Ensenada","America/Fort_Wayne","America/Fortaleza","America/Glace_Bay","America/Godthab","America/Goose_Bay","America/Grand_Turk","America/Grenada","America/Guadeloupe","America/Guatemala","America/Guayaquil","America/Guyana","America/Halifax","America/Havana","America/Hermosillo","America/Indiana/Indianapolis","America/Indiana/Knox","America/Indiana/Marengo","America/Indiana/Petersburg","America/Indiana/Tell_City","America/Indiana/Vevay","America/Indiana/Vincennes","America/Indiana/Winamac","America/Indianapolis","America/Inuvik","America/Iqaluit","America/Jamaica","America/Jujuy","America/Juneau","America/Kentucky/Louisville","America/Kentucky/Monticello","America/Knox_IN","America/Kralendijk","America/La_Paz","America/Lima","America/Los_Angeles","America/Louisville","America/Lower_Princes","America/Maceio","America/Managua","America/Manaus","America/Marigot","America/Martinique","America/Matamoros","America/Mazatlan","America/Mendoza","America/Menominee","America/Merida","America/Metlakatla","America/Mexico_City","America/Miquelon","America/Moncton","America/Monterrey","America/Montevideo","America/Montreal","America/Montserrat","America/Nassau","America/New_York","America/Nipigon","America/Nome","America/Noronha","America/North_Dakota/Beulah","America/North_Dakota/Center","America/North_Dakota/New_Salem","America/Ojinaga","America/Panama","America/Pangnirtung","America/Paramaribo","America/Phoenix","America/Port-au-Prince","America/Port_of_Spain","America/Porto_Acre","America/Porto_Velho","America/Puerto_Rico","America/Rainy_River","America/Rankin_Inlet","America/Recife","America/Regina","America/Resolute","America/Rio_Branco","America/Rosario","America/Santa_Isabel","America/Santarem","America/Santiago","America/Santo_Domingo","America/Sao_Paulo","America/Scoresbysund","America/Shiprock","America/Sitka","America/St_Barthelemy","America/St_Johns","America/St_Kitts","America/St_Lucia","America/St_Thomas","America/St_Vincent","America/Swift_Current","America/Tegucigalpa","America/Thule","America/Thunder_Bay","America/Tijuana","America/Toronto","America/Tortola","America/Vancouver","America/Virgin","America/Whitehorse","America/Winnipeg","America/Yakutat","America/Yellowknife","","Antarctica/Casey","Antarctica/Davis","Antarctica/DumontDUrville","Antarctica/Macquarie","Antarctica/Mawson","Antarctica/McMurdo","Antarctica/Palmer","Antarctica/Rothera","Antarctica/South_Pole","Antarctica/Syowa","Antarctica/Vostok","","Arctic/Longyearbyen","","Asia/Aden","Asia/Almaty","Asia/Amman","Asia/Anadyr","Asia/Aqtau","Asia/Aqtobe","Asia/Ashgabat","Asia/Ashkhabad","Asia/Baghdad","Asia/Bahrain","Asia/Baku","Asia/Bangkok","Asia/Beirut","Asia/Bishkek","Asia/Brunei","Asia/Calcutta","Asia/Choibalsan","Asia/Chongqing","Asia/Chungking","Asia/Colombo","Asia/Dacca","Asia/Damascus","Asia/Dhaka","Asia/Dili","Asia/Dubai","Asia/Dushanbe","Asia/Gaza","Asia/Harbin","Asia/Hebron","Asia/Ho_Chi_Minh","Asia/Hong_Kong","Asia/Hovd","Asia/Irkutsk","Asia/Istanbul","Asia/Jakarta","Asia/Jayapura","Asia/Jerusalem","Asia/Kabul","Asia/Kamchatka","Asia/Karachi","Asia/Kashgar","Asia/Kathmandu","Asia/Katmandu","Asia/Khandyga","Asia/Kolkata","Asia/Krasnoyarsk","Asia/Kuala_Lumpur","Asia/Kuching","Asia/Kuwait","Asia/Macao","Asia/Macau","Asia/Magadan","Asia/Makassar","Asia/Manila","Asia/Muscat","Asia/Nicosia","Asia/Novokuznetsk","Asia/Novosibirsk","Asia/Omsk","Asia/Oral","Asia/Phnom_Penh","Asia/Pontianak","Asia/Pyongyang","Asia/Qatar","Asia/Qyzylorda","Asia/Rangoon","Asia/Riyadh","Asia/Saigon","Asia/Sakhalin","Asia/Samarkand","Asia/Seoul","Asia/Shanghai","Asia/Singapore","Asia/Taipei","Asia/Tashkent","Asia/Tbilisi","Asia/Tehran","Asia/Tel_Aviv","Asia/Thimbu","Asia/Thimphu","Asia/Tokyo","Asia/Ujung_Pandang","Asia/Ulaanbaatar","Asia/Ulan_Bator","Asia/Urumqi","Asia/Ust-Nera","Asia/Vientiane","Asia/Vladivostok","Asia/Yakutsk","Asia/Yekaterinburg","Asia/Yerevan","Atlantic/Azores","Atlantic/Bermuda","Atlantic/Canary","Atlantic/Cape_Verde","Atlantic/Faeroe","Atlantic/Faroe","Atlantic/Jan_Mayen","Atlantic/Madeira","Atlantic/Reykjavik","Atlantic/South_Georgia","Atlantic/St_Helena","Atlantic/Stanley","","Australia/ACT","Australia/Adelaide","Australia/Brisbane","Australia/Broken_Hill","Australia/Canberra","Australia/Currie","Australia/Darwin","Australia/Eucla","Australia/Hobart","Australia/LHI","Australia/Lindeman","Australia/Lord_Howe","Australia/Melbourne","Australia/North","Australia/NSW","Australia/Perth","Australia/Queensland","Australia/South","Australia/Sydney","Australia/Tasmania","Australia/Victoria","Australia/West","Australia/Yancowinna","Europe/Amsterdam","Europe/Andorra","Europe/Athens","Europe/Belfast","Europe/Belgrade","Europe/Berlin","Europe/Bratislava","Europe/Brussels","Europe/Bucharest","Europe/Budapest","Europe/Busingen","Europe/Chisinau","Europe/Copenhagen","Europe/Dublin","Europe/Gibraltar","Europe/Guernsey","Europe/Helsinki","Europe/Isle_of_Man","Europe/Istanbul","Europe/Jersey","Europe/Kaliningrad","Europe/Kiev","Europe/Lisbon","Europe/Ljubljana","Europe/London","Europe/Luxembourg","Europe/Madrid","Europe/Malta","Europe/Mariehamn","Europe/Minsk","Europe/Monaco","Europe/Moscow","Europe/Nicosia","Europe/Oslo","Europe/Paris","Europe/Podgorica","Europe/Prague","Europe/Riga","Europe/Rome","Europe/Samara","Europe/San_Marino","Europe/Sarajevo","Europe/Simferopol","Europe/Skopje","Europe/Sofia","Europe/Stockholm","Europe/Tallinn","Europe/Tirane","Europe/Tiraspol","Europe/Uzhgorod","Europe/Vaduz","Europe/Vatican","Europe/Vienna","Europe/Vilnius","Europe/Volgograd","Europe/Warsaw","Europe/Zagreb","Europe/Zaporozhye","Europe/Zurich","Indian/Antananarivo","Indian/Chagos","Indian/Christmas","Indian/Cocos","Indian/Comoro","Indian/Kerguelen","Indian/Mahe","Indian/Maldives","Indian/Mauritius","Indian/Mayotte","Indian/Reunion","Pacific/Apia","Pacific/Auckland","Pacific/Chatham","Pacific/Chuuk","Pacific/Easter","Pacific/Efate","Pacific/Enderbury","Pacific/Fakaofo","Pacific/Fiji","Pacific/Funafuti","Pacific/Galapagos","Pacific/Gambier","Pacific/Guadalcanal","Pacific/Guam","Pacific/Honolulu","Pacific/Johnston","Pacific/Kiritimati","Pacific/Kosrae","Pacific/Kwajalein","Pacific/Majuro","Pacific/Marquesas","Pacific/Midway","Pacific/Nauru","Pacific/Niue","Pacific/Norfolk","Pacific/Noumea","Pacific/Pago_Pago","Pacific/Palau","Pacific/Pitcairn","Pacific/Pohnpei","Pacific/Ponape","Pacific/Port_Moresby","Pacific/Rarotonga","Pacific/Saipan","Pacific/Samoa","Pacific/Tahiti","Pacific/Tarawa","Pacific/Tongatapu","Pacific/Truk","Pacific/Wake","Pacific/Wallis","Pacific/Yap"
		];

		foreach($arr as $k=>$v)
		{
			$Timezones = new \backend\models\Timezone;
			$Timezones->timezone=$v;
			$Timezones->save();
		}
	} */

   /* public function actionLanguages()
    {
        $arr=
        [
            'ab'=>'Abkhaz',
            'aa'=>'Afar',
            'af'=>'Afrikaans',
            'ak'=>'Akan',
            'sq'=>'Albanian',
            'am'=>'Amharic',
            'ar'=>'Arabic',
            'an'=>'Aragonese',
            'hy'=>'Armenian',
            'as'=>'Assamese',
            'av'=>'Avaric',
            'ae'=>'Avestan',
            'ay'=>'Aymara',
            'az'=>'Azerbaijani',
            'bm'=>'Bambara',
            'ba'=>'Bashkir',
            'eu'=>'Basque',
            'be'=>'Belarusian',
            'bn'=>'Bengali',
            'bh'=>'Bihari',
            'bi'=>'Bislama',
            'bs'=>'Bosnian',
            'br'=>'Breton',
            'bg'=>'Bulgarian',
            'my'=>'Burmese',
            'ca'=>'Catalan; Valencian',
            'ch'=>'Chamorro',
            'ce'=>'Chechen',
            'ny'=>'Chichewa; Chewa; Nyanja',
            'zh'=>'Chinese',
            'cv'=>'Chuvash',
            'kw'=>'Cornish',
            'co'=>'Corsican',
            'cr'=>'Cree',
            'hr'=>'Croatian',
            'cs'=>'Czech',
            'da'=>'Danish',
            'dv'=>'Divehi; Dhivehi; Maldivian;',
            'nl'=>'Dutch',
            'en'=>'English',
            'eo'=>'Esperanto',
            'et'=>'Estonian',
            'ee'=>'Ewe',
            'fo'=>'Faroese',
            'fj'=>'Fijian',
            'fi'=>'Finnish',
            'fr'=>'French',
            'ff'=>'Fula; Fulah; Pulaar; Pular',
            'gl'=>'Galician',
            'ka'=>'Georgian',
            'de'=>'German',
            'el'=>'Greek, Modern',
            'gn'=>'Guaraní',
            'gu'=>'Gujarati',
            'ht'=>'Haitian; Haitian Creole',
            'ha'=>'Hausa',
            'he'=>'Hebrew (modern)',
            'hz'=>'Herero',
            'hi'=>'Hindi',
            'ho'=>'Hiri Motu',
            'hu'=>'Hungarian',
            'ia'=>'Interlingua',
            'id'=>'Indonesian',
            'ie'=>'Interlingue',
            'ga'=>'Irish',
            'ig'=>'Igbo',
            'ik'=>'Inupiaq',
            'io'=>'Ido',
            'is'=>'Icelandic',
            'it'=>'Italian',
            'iu'=>'Inuktitut',
            'ja'=>'Japanese',
            'jv'=>'Javanese',
            'kl'=>'Kalaallisut, Greenlandic',
            'kn'=>'Kannada',
            'kr'=>'Kanuri',
            'ks'=>'Kashmiri',
            'kk'=>'Kazakh',
            'km'=>'Khmer',
            'ki'=>'Kikuyu, Gikuyu',
            'rw'=>'Kinyarwanda',
            'ky'=>'Kirghiz, Kyrgyz',
            'kv'=>'Komi',
            'kg'=>'Kongo',
            'ko'=>'Korean',
            'ku'=>'Kurdish',
            'kj'=>'Kwanyama, Kuanyama',
            'la'=>'Latin',
            'lb'=>'Luxembourgish, Letzeburgesch',
            'lg'=>'Luganda',
            'li'=>'Limburgish, Limburgan, Limburger',
            'ln'=>'Lingala',
            'lo'=>'Lao',
            'lt'=>'Lithuanian',
            'lu'=>'Luba-Katanga',
            'lv'=>'Latvian',
            'gv'=>'Manx',
            'mk'=>'Macedonian',
            'mg'=>'Malagasy',
            'ms'=>'Malay',
            'ml'=>'Malayalam',
            'mt'=>'Maltese',
            'mi'=>'Maori',
            'mr'=>'Marathi (Mara?hi)',
            'mh'=>'Marshallese',
            'mn'=>'Mongolian',
            'na'=>'Nauru',
            'nv'=>'Navajo, Navaho',
            'nb'=>'Norwegian Bokmal',
            'nd'=>'North Ndebele',
            'ne'=>'Nepali',
            'ng'=>'Ndonga',
            'nn'=>'Norwegian Nynorsk',
            'no'=>'Norwegian',
            'ii'=>'Nuosu',
            'nr'=>'South Ndebele',
            'oc'=>'Occitan',
            'oj'=>'Ojibwe, Ojibwa',
            'cu'=>'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
            'om'=>'Oromo',
            'or'=>'Oriya',
            'os'=>'Ossetian, Ossetic',
            'pa'=>'Panjabi, Punjabi',
            'pi'=>'Pali',
            'fa'=>'Persian',
            'pl'=>'Polish',
            'ps'=>'Pashto, Pushto',
            'pt'=>'Portuguese',
            'qu'=>'Quechua',
            'rm'=>'Romansh',
            'rn'=>'Kirundi',
            'ro'=>'Romanian, Moldavian, Moldovan',
            'ru'=>'Russian',
            'sa'=>'Sanskrit (Sa?sk?ta)',
            'sc'=>'Sardinian',
            'sd'=>'Sindhi',
            'se'=>'Northern Sami',
            'sm'=>'Samoan',
            'sg'=>'Sango',
            'sr'=>'Serbian',
            'gd'=>'Scottish Gaelic; Gaelic',
            'sn'=>'Shona',
            'si'=>'Sinhala, Sinhalese',
            'sk'=>'Slovak',
            'sl'=>'Slovene',
            'so'=>'Somali',
            'st'=>'Southern Sotho',
            'es'=>'Spanish; Castilian',
            'su'=>'Sundanese',
            'sw'=>'Swahili',
            'ss'=>'Swati',
            'sv'=>'Swedish',
            'ta'=>'Tamil',
            'te'=>'Telugu',
            'tg'=>'Tajik',
            'th'=>'Thai',
            'ti'=>'Tigrinya',
            'bo'=>'Tibetan Standard, Tibetan, Central',
            'tk'=>'Turkmen',
            'tl'=>'Tagalog',
            'tn'=>'Tswana',
            'to'=>'Tonga (Tonga Islands)',
            'tr'=>'Turkish',
            'ts'=>'Tsonga',
            'tt'=>'Tatar',
            'tw'=>'Twi',
            'ty'=>'Tahitian',
            'ug'=>'Uighur, Uyghur',
            'uk'=>'Ukrainian',
            'ur'=>'Urdu',
            'uz'=>'Uzbek',
            've'=>'Venda',
            'vi'=>'Vietnamese',
            'vo'=>'Volapük',
            'wa'=>'Walloon',
            'cy'=>'Welsh',
            'wo'=>'Wolof',
            'fy'=>'Western Frisian',
            'xh'=>'Xhosa',
            'yi'=>'Yiddish',
            'yo'=>'Yoruba',
            'za'=>'Zhuang, Chuang',
        ];

        foreach($arr as $key=>$value)
        {
            $model = new \backend\models\LanguagesAll;
            $model->name=$value;
            $model->code=$key;
            $model->save();
        }
    } */

    /*
    poredaj slike u direktorije
    */
    /*public function actionDirektorij()
    {

        foreach(Story::find()->where("image IS NOT NULL")->all() as $key=>$value)
        {

            $year=date("Y", strtotime($value->date_created));
            $month=date("m", strtotime($value->date_created));
            $day=date("d", strtotime($value->date_created))  ;

            $path=Yii::getAlias('@webroot'). Story::PATH_IMAGE;
            $path_year=Yii::getAlias('@webroot').Story::PATH_IMAGE.$year."/";
            $path_month=$path_year.$month."/";
            $path_day=$path_month.$day."/";

            if(!file_exists($path_year))
                mkdir($path_year,0755,true);

            if(!file_exists($path_month))
                mkdir($path_month,0755,true);

            if(!file_exists($path_day))
                mkdir($path_day,0755,true);

            if(!file_exists($path.$value->image) || $value->image==NULL)
                continue;

            if(rename($path.$value->image,$path_day.$value->image))
                echo "OK<br>";
            else
                echo "NOT<br>";


            //thumbnail
            $path=Yii::getAlias('@webroot'). Story::PATH_IMAGE_THUMB;
            $path_year=Yii::getAlias('@webroot').Story::PATH_IMAGE_THUMB.$year."/";
            $path_month=$path_year.$month."/";
            $path_day=$path_month.$day."/";

            if(!file_exists($path_year))
                mkdir($path_year,0755,true);

            if(!file_exists($path_month))
                mkdir($path_month,0755,true);

            if(!file_exists($path_day))
                mkdir($path_day,0755,true);

            if(!file_exists($path."640_508_".$value->image) || $value->image==NULL)
                continue;

            if(rename($path."640_508_".$value->image,$path_day."640_508_".$value->image))
                echo "OK Thumb<br>";
            else
                echo "NOT Thum<br>";

        }

        foreach(Story::find()->where("video IS NOT NULL")->all() as $key=>$value)
        {

            //video

            $path=Yii::getAlias('@webroot'). Story::PATH_VIDEO;
            $path_year=Yii::getAlias('@webroot').Story::PATH_VIDEO.$year."/";
            $path_month=$path_year.$month."/";
            $path_day=$path_month.$day."/";

            if(!file_exists($path_year))
                mkdir($path_year,0755,true);

            if(!file_exists($path_month))
                mkdir($path_month,0755,true);

            if(!file_exists($path_day))
                mkdir($path_day,0755,true);

            if(!file_exists($path.$value->video) || $value->video==NULL)
                continue;

            if(rename($path.$value->video,$path_day.$value->video))
                echo "OK VIDEO<br>";
            else
                echo "NOT VIDEO<br>";
        }
    } */

    /*
    stvori seo url
    */
    /*public function actionSeourl()
    {

        foreach(Story::find()->where("seo_url=''")->all() as $key=>$value)
        {
            $value->seo_url=\backend\components\Helpers::url_slug($value->title);
            if(empty($value->title))
                $value->title='News';
            if(empty($value->seo_title))
                $value->seo_title='News';
            if(empty($value->description))
                $value->description='News';
            if($value->save())
            echo 'OK<br>';
            else
            echo 'NOT<br>';
        }


    } */

     /*
    stvori auth_key url
    */
    /*public function actionAuthkey()
    {

        foreach(\backend\models\User::find()->where("auth_key=''")->all() as $key=>$value)
        {
             $value->auth_key = \Yii::$app->security->generateRandomString();
            if($value->save())
            echo 'OK<br>';
            else
            echo 'NOT<br>';
        }


    } */

    /**
    * reset everyones password
    */
    /*public function actionPassword()
    {
        foreach(\backend\models\User::find()->all() as $key=>$value)
        {
            $length = 10;

            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            $pass=hash('sha512', $randomString);
            $value->password=$pass;
            $value->save();

            $message="New CMS is up. Ie had to reset your password. You new password is: <b>$randomString</b>";

            \common\components\Helpers::sendEmailToAnyone("[Born2Invest] New Password", $message, $value->email, $value->name);

            $array[$value->email]=$randomString;
        }

        $send="";
        foreach($array as $key=>$value)
        {
            $send.=$key." => ".$value."<br>";
        }
        \common\components\Helpers::sendEmailToAnyone("[Born2Invest] New Passwords", $send, "eddie@born2invest.com", "Eddie");
        \common\components\Helpers::sendEmailToAnyone("[Born2Invest] New Passwords", $send, "dario@born2invest.com", "Dario");

    }

    public function actionCountrylanguage()
    {
        $countries=\backend\models\Country::find()->joinWith(['relationCountryExt'])->orderBy('name ASC')->all();

        foreach($countries as $country)
        {

             if($country->relationCountryExt!=NULL)
             {
                $e=explode(",", $country->relationCountryExt->languages);

                foreach($e as $lang)
                {
                     $lang_tmp=\backend\models\Language::find()->where(['code'=>$lang])->one();
                     if(!empty($lang_tmp))
                    {
                         $CountryLanguage=new \backend\models\CountryLanguage;
                        $CountryLanguage->language_id=$lang_tmp->id;
                        $CountryLanguage->country_id=$country->id;
                        if($CountryLanguage->save())
                            echo "OK<br>";
                        else
                            echo "NOT OK<br>";
                    }
                }
            }


        }
    }*/

        /*
    * add all users to Mixpanel
    */
    /*public function actionAddUserMixpanel()
    {
        $mp = new \Mixpanel("52821a0b90594f32db2c1b525316303f");

        $user=\backend\models\User::find()->all();
        $i=0;
        foreach($user as $v)
        {
            $mp->people->set($v->id, array(
                '$first_name'       => $v->name,
                '$email'            => $v->email,
                'Date registered'   => $v->date,
            ));
            echo $i." ".$v->name."<br>";
            $i++;
        }
    }*/
}
