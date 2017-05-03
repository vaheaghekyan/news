<?php

namespace backend\components;

use backend\models\User;
use backend\models\Story;
use backend\models\Language;
use common\helpers\Helpers as CommonHelpers;
use Yii;

/*
* Helper class for some extra functions I need all across projet
*/
class Helpers
{

    /*
    *  get full backend domain
    */
    public static function backendDomain()
    {
        if(in_array($_SERVER['REMOTE_ADDR'], \Yii::$app->params['local_ip']))
        {
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://localhost:8002"; //so you can fetch image from frontend
        }
        else if(strpos($_SERVER['HTTP_HOST'], "estfrontend") || strpos($_SERVER['HTTP_HOST'], "estbackend"))
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://testbackend.born2invest.com";
        else
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://cms.born2invest.com"; //so you can fetch image from frontend
    }

    public static function backendCDN()
    {
        if(in_array($_SERVER['REMOTE_ADDR'], \Yii::$app->params['local_ip']))
        {
//            return "http://localhost:8002";
            return "http://backend.born2invest.dev";
        }
        else if(strpos($_SERVER['HTTP_HOST'], "estfrontend") || strpos($_SERVER['HTTP_HOST'], "estbackend"))
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://testbackend.born2invest.com";
        else
            return "http://cdn.cms.born2invest.com";
    }

    /*
    *  get current daabase connection
    */
    public static function databaseConnection()
    {
        return Yii::$app->db;
    }

    /*When user wants to search GridView by date, but in database there is datetime, call this and create datetime format
    see views/story/stories.php
    * $type = "begin" (e.g. 2015-12-29 00:00:00) and "end" (e.g. 2015-12-29 23:59:59)
    * $date = e.g. 2015-12-29
    */
    public static function createDateTimeBetween($type, $date)
    {
        if(!empty($date))
        {
            if($type=="begin")
                $return = $date." 00:00:00";
            else
                $return = $date." 23:59:59";

            return $return;
        }
        else
            return NULL;
    }

    /*
    * calculate date difference between 2 dates
    * if date1 is null return current date
    * $date1 - older date, $date2 - newer date
    * $format = how to format date, in hours, days...

    http://php.net/manual/en/dateinterval.format.php
    */
    public static function dateDifferenceStory($date1, $date2)
    {
        if($date1==NULL)
            $date1=date("Y-m-d H:i:s");

        $datetime1 = new \DateTime($date1);
        $datetime2 = new \DateTime($date2);
        $interval = $datetime1->diff($datetime2);

        $minute=$interval->format('%i');
        $hour=$interval->format('%h');
        $day=$interval->format('%d');
        $month=$interval->format('%m');
        $year=$interval->format('%y');


        //you have to go from years down to minutes because minutes will always be some number or 0, because it doesn't calculate total minutes, it calculates up to 60 minutes then changes hour, reset minute to 0 and so on...
        //if few years or one year passed, show in years, if few months passed show in months and so on

        /*
        This month (4 weeks old)
        1 month ago (5 weeks - 7 weeks old)
        2 months ago (8 weeks old - 11 weeks old)
        3 months ago (12 weeks old)
        */
        if($month==3)
           return Yii::t("app", "3 months ago", ['0'=>$month]);
        else if($month==2)
           return Yii::t("app", "2 months ago", ['0'=>$month]);
       else if($month==1)
            return Yii::t("app", "1 month ago", ['0'=>$month]);
        else if($day>=28 && $day<=31)
            return Yii::t("app", "This month", ['0'=>$month]);

        /*
        Last week (1 week old)
        2 weeks ago (2 weeks old)
        3 weeks ago (3 weeks old)
        */
        else if($day>=21 && $day<28)
            return Yii::t("app", "3 weeks ago", ['0'=>3]);
        else if($day>=14 && $day<21)
            return Yii::t("app", "2 weeks ago", ['0'=>2]);
        else if($day>7 && $day<14)
            return Yii::t("app", "Last week");

        /*
        1 day ago (anything past 24 hours)
        2 days ago (24 - 48 hours)
        3 days ago (49 - 72 hours)
        This week ( anything over 3 days old to 7 days)
        */
        else if($day>3 && $day<=7)
            return Yii::t("app", "This week");
        else if($day==3)
            return Yii::t("app", "3 days ago", ['0'=>$day]);
        else if($day==2)
            return Yii::t("app", "2 days ago", ['0'=>$day]);
        else if($day==1)
            return Yii::t("app", "1 day ago", ['0'=>$day]);

        /*
        1 hour ago (1 hr to 1:59)
        2 hours ago (2 hrs to 2:59)
        3 hours ago (3 hrs to 3:59)
        Today (for anything over 4 hours and below 6 hours)
        Earlier today (over 6 hours to 24 hours)
        */
        else if($hour>=6 && $hour<=24)
            return Yii::t("app", "Earlier today");
        else if($hour>=4 && $hour<6)
            return Yii::t("app", "Today");
        else if($hour>=3 && $hour<4)
            return Yii::t("app", "3 hours ago", ['0'=>$hour]); 
        else if($hour>=2 && $hour<3)
            return Yii::t("app", "2 hours ago", ['0'=>$hour]);
        else if($hour>=1 && $hour<2)
            return Yii::t("app", "1 hour ago", ['0'=>$hour]);

        /*
        Just now (for the first 30 mins)
        30 minutes ago (from min 31 - 59)
        */
        else if($minute>=31 && $minute<=59)
            return Yii::t("app", "30 minutes ago", ['0'=>$minute]);
        else if($minute<=30)
            return Yii::t("app", "Just now");
    }

    /*
    *  Load Kraken class
    * $path - path to image with image name: e.g. web/uploads/image/1.jpg
    */
    public static function krakenUploadFile($path)
    {
        $api_key="a7708a020aeabb3b48b684aa7a5d62ea";
        $api_secret="1c7fddd06291e8e082bdc5823dbf1e421b8211c5";
        $kraken = new \Kraken($api_key, $api_secret);

        $params = array(
            "file" => $path,
            "wait" => true,
            "lossy" => true,
        );

        $data = $kraken->upload($params);

        if ($data["success"])
        {
            //echo "Success. Optimized image URL: " . $data["kraked_url"];
            //if something was wrong, create warning message
            if(!file_put_contents($path, fopen($data["kraked_url"], 'r')))
            {
                 Yii::$app->session->setFlash('danger', Yii::t('app', 'Image couldnt be compressed'));
            }
        }
        else
        {
           //if something was wrong, create warning message
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Image couldnt be compressed'));
        }

   }

 /**
     * Create a web friendly URL slug from a string.
     *
     * Although supported, transliteration is discouraged because
     *     1) most web browsers support UTF-8 characters in URLs
     *     2) transliteration causes a loss of information
     *
     * @author Sean Murphy <sean@iamseanmurphy.com>
     * @copyright Copyright 2012 Sean Murphy. All rights reserved.
     * @license http://creativecommons.org/publicdomain/zero/1.0/
     *
     * @param string $str
     * @param array $options
     * @return string
     */
    public static function url_slug($str, $options = array())
    {
    	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
    	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

    	$defaults = array(
    		'delimiter' => '-',
    		'limit' => null,
    		'lowercase' => true,
    		'replacements' => array(),
    		'transliterate' => false,
    	);

    	// Merge options
    	$options = array_merge($defaults, $options);

    	$char_map = array(
    		// Latin
    		'A' => 'A', 'Á' => 'A', 'Â' => 'A', 'A' => 'A', 'Ä' => 'A', 'A' => 'A', 'A' => 'AE', 'Ç' => 'C',
    		'E' => 'E', 'É' => 'E', 'E' => 'E', 'Ë' => 'E', 'I' => 'I', 'Í' => 'I', 'Î' => 'I', 'I' => 'I',
    		'?' => 'D', 'N' => 'N', 'O' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'O' => 'O', 'Ö' => 'O', 'Õ' => 'O',
    		'O' => 'O', 'U' => 'U', 'Ú' => 'U', 'U' => 'U', 'Ü' => 'U', 'Û' => 'U', 'Ý' => 'Y', '?' => 'TH',
    		'ß' => 'ss',
    		'a' => 'a', 'á' => 'a', 'â' => 'a', 'a' => 'a', 'ä' => 'a', 'a' => 'a', 'a' => 'ae', 'ç' => 'c',
    		'e' => 'e', 'é' => 'e', 'e' => 'e', 'ë' => 'e', 'i' => 'i', 'í' => 'i', 'î' => 'i', 'i' => 'i',
    		'?' => 'd', 'n' => 'n', 'o' => 'o', 'ó' => 'o', 'ô' => 'o', 'o' => 'o', 'ö' => 'o', 'õ' => 'o',
    		'o' => 'o', 'u' => 'u', 'ú' => 'u', 'u' => 'u', 'ü' => 'u', 'û' => 'u', 'ý' => 'y', '?' => 'th',
    		'y' => 'y',
    		// Latin symbols
    		'©' => 'c',
    		// Greek
    		'?' => 'A', '?' => 'B', '?' => 'G', '?' => 'D', '?' => 'E', '?' => 'Z', '?' => 'H', '?' => '8',
    		'?' => 'I', '?' => 'K', '?' => 'L', '?' => 'M', '?' => 'N', '?' => '3', '?' => 'O', '?' => 'P',
    		'?' => 'R', '?' => 'S', '?' => 'T', '?' => 'Y', '?' => 'F', '?' => 'X', '?' => 'PS', '?' => 'W',
    		'?' => 'A', '?' => 'E', '?' => 'I', '?' => 'O', '?' => 'Y', '?' => 'H', '?' => 'W', '?' => 'I',
    		'?' => 'Y',
    		'?' => 'a', 'ß' => 'b', '?' => 'g', '?' => 'd', '?' => 'e', '?' => 'z', '?' => 'h', '?' => '8',
    		'?' => 'i', '?' => 'k', '?' => 'l', 'µ' => 'm', '?' => 'n', '?' => '3', '?' => 'o', '?' => 'p',
    		'?' => 'r', '?' => 's', '?' => 't', '?' => 'y', '?' => 'f', '?' => 'x', '?' => 'ps', '?' => 'w',
    		'?' => 'a', '?' => 'e', '?' => 'i', '?' => 'o', '?' => 'y', '?' => 'h', '?' => 'w', '?' => 's',
    		'?' => 'i', '?' => 'y', '?' => 'y', '?' => 'i',
    		// Turkish
    		'ª' => 'S', 'I' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'G' => 'G',
    		'º' => 's', 'i' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'g' => 'g',
    		// Russian
    		'?' => 'A', '?' => 'B', '?' => 'V', '?' => 'G', '?' => 'D', '?' => 'E', '?' => 'Yo', '?' => 'Zh',
    		'?' => 'Z', '?' => 'I', '?' => 'J', '?' => 'K', '?' => 'L', '?' => 'M', '?' => 'N', '?' => 'O',
    		'?' => 'P', '?' => 'R', '?' => 'S', '?' => 'T', '?' => 'U', '?' => 'F', '?' => 'H', '?' => 'C',
    		'?' => 'Ch', '?' => 'Sh', '?' => 'Sh', '?' => '', '?' => 'Y', '?' => '', '?' => 'E', '?' => 'Yu',
    		'?' => 'Ya',
    		'?' => 'a', '?' => 'b', '?' => 'v', '?' => 'g', '?' => 'd', '?' => 'e', '?' => 'yo', '?' => 'zh',
    		'?' => 'z', '?' => 'i', '?' => 'j', '?' => 'k', '?' => 'l', '?' => 'm', '?' => 'n', '?' => 'o',
    		'?' => 'p', '?' => 'r', '?' => 's', '?' => 't', '?' => 'u', '?' => 'f', '?' => 'h', '?' => 'c',
    		'?' => 'ch', '?' => 'sh', '?' => 'sh', '?' => '', '?' => 'y', '?' => '', '?' => 'e', '?' => 'yu',
    		'?' => 'ya',
    		// Ukrainian
    		'?' => 'Ye', '?' => 'I', '?' => 'Yi', '?' => 'G',
    		'?' => 'ye', '?' => 'i', '?' => 'yi', '?' => 'g',
    		// Czech
    		'È' => 'C', 'Ï' => 'D', 'Ì' => 'E', 'Ò' => 'N', 'Ø' => 'R', 'Š' => 'S', '' => 'T', 'Ù' => 'U',
    		'Ž' => 'Z',
    		'è' => 'c', 'ï' => 'd', 'ì' => 'e', 'ò' => 'n', 'ø' => 'r', 'š' => 's', '' => 't', 'ù' => 'u',
    		'ž' => 'z',
    		// Polish
    		'¥' => 'A', 'Æ' => 'C', 'Ê' => 'e', '£' => 'L', 'Ñ' => 'N', 'Ó' => 'o', 'Œ' => 'S', '' => 'Z',
    		'¯' => 'Z',
    		'¹' => 'a', 'æ' => 'c', 'ê' => 'e', '³' => 'l', 'ñ' => 'n', 'ó' => 'o', 'œ' => 's', 'Ÿ' => 'z',
    		'¿' => 'z',
    		// Latvian
    		'A' => 'A', 'È' => 'C', 'E' => 'E', 'G' => 'G', 'I' => 'i', 'K' => 'k', 'L' => 'L', 'N' => 'N',
    		'Š' => 'S', 'U' => 'u', 'Ž' => 'Z',
    		'a' => 'a', 'è' => 'c', 'e' => 'e', 'g' => 'g', 'i' => 'i', 'k' => 'k', 'l' => 'l', 'n' => 'n',
    		'š' => 's', 'u' => 'u', 'ž' => 'z'
    	);

    	// Make custom replacements
       	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

    	// Transliterate characters to ASCII
    	if ($options['transliterate']) {
    		//$str = str_replace(array_keys($char_map), $char_map, $str);
    	}

    	// Replace non-alphanumeric characters with our delimiter
    	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

    	// Remove duplicate delimiters
       	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

    	// Truncate slug to max. characters
    	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

    	// Remove delimiter from ends
    	$str = trim($str, $options['delimiter']);

    	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    /*
    * delete all files and their folder
    */
    public static function deleteFilesAndFolder($directory)
    {
        foreach(glob("{$directory}/*") as $file)
        {
            if(is_dir($file))
            {
                self::recursiveRemoveDirectory($file);
            }
            else
            {
                unlink($file);
            }
        }
        rmdir($directory);
    }

    //return true or false whether column should be visible for user or not
    public static function columnVisible($roles)
    {
        $user_role=Yii::$app->user->getIdentity()->role;
        foreach($roles as $role)
        {
            if($role==$user_role)
            {
                return true;
            }
        }

        return false;
    }

    /*
    *  rename specific file
    */
    public static function renameFile($directory, $oldFile, $newFile)
    {
        rename($directory.$oldFile, $directory.$newFile);
    }

    /*
    * add time, +1h ,+1day...
    * $datetime - Y-m-d H:i:s
    * $interval - how many hours/min/day... to add
    */
    public static function addDateTime($datetime, $interval=NULL)
    {
        $date = new \DateTime($datetime);
        if($interval==NULL)
            $date->add(new \DateInterval('PT1H'));

        return $date->format('Y-m-d H:i:s');
    }

    /*
    *  file upload
    * $path - Story::PATH_IMAGE, Story::PATH_VIDEO...
    * $date_created - Y-m-d or Y-m-d H:i:s so I can create directory
    */
    public static function fileUpload($_FILE_NAME, $_FILE_TMP_NAME, $path, $date_created=NULL)
    {
        if($date_created!=NULL)
        {
            $year=date("Y", strtotime($date_created));
            $month=date("m", strtotime($date_created));
            $day=date("d", strtotime($date_created));
            $path=$path."/".$year."/".$month."/".$day."/"; //=> /uploads/image/2015/10/05/
        }
        $target_dir = Yii::getAlias("@webroot").$path;
        $target_file = $target_dir.basename($_FILE_NAME);
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        $imageName = pathinfo($target_file,PATHINFO_FILENAME);

        //new file name
        $file_name=$imageName.mt_rand().".".$imageFileType;
        $target_file = $target_dir.$file_name;

        $uploadOk = 1;

        //create directory if it doesn't exist
        if(!file_exists($target_dir))
            mkdir($target_dir,0755,true);

        // Check if file already exists
       /* if (file_exists($target_file))
        {
            Yii::$app->session->setFlash('error', Yii::t("app", "Sorry, file already exists."));
            $uploadOk = 0;
        } */
        // Check file size
        /*if ($_FILES["fileToUpload"]["size"] > 500000)
        {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        } */
        // Allow certain file formats
        /*
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        */
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0)
        {
            Yii::$app->session->addFlash('error', Yii::t("app", "Sorry, your file was not uploaded."));

        }
        // if everything is ok, try to upload file
        else
        {
            if (move_uploaded_file($_FILE_TMP_NAME, $target_file))
            {
                $uploadOk=1;
                $msg="The file $file_name has been uploaded.";
                Yii::$app->session->addFlash('success', $msg);
            }
            else
            {
                $uploadOk = 0;
                Yii::$app->session->addFlash('error', Yii::t("app", "Sorry, there was an error uploading your file."));
            }
        }

        return ["file_name"=>$file_name, 'uploadOk'=>$uploadOk];
    }

    /*
    *  show only specific countries per lang
    */
    public static function showCountriesPerLang()
    {
        return
        [
            //language id => [country id]
            14=>[16, 19, 10, 22], //French
            10=>[25, 93, 95, 136], //Spanish
            13=>[36, 15], //Portuguese
            7=>[2, 3, 12, 41, 29, 8, 81, 10], //English
            12=>[48, 72, 92, 91], //Arabic
        ];
    }

    /*
    * substract date
    * $amount - how much do you need to substract
    * $type - days, months, years...
    */
    public static function subDate($amount, $type)
    {
        $date = new \DateTime();
        if($type=="month")
        {
            $date->sub(new \DateInterval('P'.$amount.'M'));
        }
        return $date->format('Y-m-d H:i:s');
    }

    /*
    * cache component
    */
    public static function cache()
    {
        return Yii::$app->cache;
    }

    /*
    *  set language depending on if user is admin or not
    */
    public static function setLanguage()
    {
        $identity=Yii::$app->user->getIdentity();
        //for admins and superadmins always put english lang
        if(!empty($identity))
        {
            if($identity->role==User::ROLE_SUPERADMIN || $identity->role==User::ROLE_ADMIN)
                Yii::$app->language="en";
            else
                Yii::$app->language=Language::getCurrent(); //cookie is set on login
        }
        else
            Yii::$app->language=Language::getCurrent(); //cookie is set on login
    }

    /*
    *  set or reset session files
    * $action="set", "reset", "echo"
    * $session_key - session key to check and echo
    * $model - loaded Story model via php or number via javascript, so you can create unique ID for session
    */
    public static function storySession($action, $model=NULL, $session_key=NULL)
    {
        //since I'm passing loaded model or just number I have to check if this is new record or I can take "id"
        //it is being passed from js and from php
        if(is_numeric($model))
            $ID=$model;
        else if($model->isNewRecord)
            $ID=-1;
        else
            $ID=$model->id;


        $session=["story_title_"," story_original_source_", "story_source_link_", "story_summary_", "story_keyword_", "story_alt_tag_", "story_img_name_", "story_schedule_", "story_seo_title_"];
        if($action=="reset")
        {
            foreach($session as $value)
            {
                unset($_SESSION[$value.$ID]);
            }
        }
        else if($action=="set" && isset($_POST["Story"]["title"]))
        {
            $_SESSION["story_title_".$ID]=$_POST["Story"]["title"];
            $_SESSION["story_seo_title_".$ID]=isset($_POST["Story"]["seo_title"]) ? $_POST["Story"]["seo_title"] : "";
            $_SESSION["story_source_link_".$ID]=isset($_POST["Story"]["link"]) ? $_POST["Story"]["link"] : "";
            $_SESSION["story_summary_".$ID]=isset($_POST["Story"]["description"]) ? $_POST["Story"]["description"] : "";
            $_SESSION["story_alt_tag_".$ID]=isset($_POST["Story"]["alt_tag"]) ? $_POST["Story"]["alt_tag"] : "";
            $_SESSION["story_img_name_".$ID]=isset($_POST["image_name"]) ? $_POST["image_name"] : "";
            $_SESSION["story_schedule_".$ID]=isset($_POST["date_published"]) ? $_POST["date_published"] : "";
            $_SESSION["story_keyword_".$ID]=isset($_POST["StoryKeyword"]["keywords"]) ? $_POST["StoryKeyword"]["keywords"] : "";
            return true;
        }
        //echo this session if it is set
        else if($action=="echo" && $session_key!=NULL)
        {
            if(isset($_SESSION[$session_key.$ID]) && !empty($_SESSION[$session_key.$ID]))
                return $_SESSION[$session_key.$ID];
            else
                return NULL;
        }

    }

}
