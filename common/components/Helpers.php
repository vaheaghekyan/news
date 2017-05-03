<?php

namespace common\components;
use frontend\models\search\StorySearch;
use backend\models\Story;
use frontend\components\Helpers as FrontendHelpers;
use backend\components\Helpers as BackendHelpers;
use backend\modules\settings\models\SettingsStoryInject;

use Yii;

/*
* Helper class for some extra functions I need all across projet
*/
class Helpers
{

    public static function dbConnection()
    {
        return Yii::$app->db;
    }

    /*
    * $users - email and name of another user
    */
    public static function sendEmailToMultiplePeople($subject, $message, $users, $from)
    {
        //Create a new PHPMailer instance
        $mail = new \PHPMailer;
        $mail->CharSet = 'UTF-8';
        //Set who the message is to be sent from
        $mail->setFrom($from["email"], $from["name"]);

        foreach($users as $key=>$value)
        {
            //Set who the message is to be sent to
            $mail->addAddress($value["email"], $value["name"]);
        }

        //Set the subject line
        $mail->Subject = $subject;
        //msg
        $mail->msgHTML(nl2br($message));
        $mail->send();
    }

    /*
    * send email to me
    * $from_email - who is sending me email
    * $name_from - the name of person who is sending me email
    */
    public static function sendEmailToAdmin($subject, $message, $from_email, $name_from)
    {
        //Create a new PHPMailer instance
        $mail = new \PHPMailer;
        $mail->CharSet = 'UTF-8';
        //Set who the message is to be sent from
        $mail->setFrom($from_email, $name_from);
        //Set who the message is to be sent to
        $mail->addAddress('dario@born2invest.com', 'Dario Trbovic');
        //Set the subject line
        $mail->Subject = $subject;
        //msg
        $mail->msgHTML(nl2br($message));

        //send the message, check for errors
        if (!$mail->send())
        {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Thank you for email error'));
        }
        else
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Thank you for email'));
        }

    }

    /*
    * send email to anyone
    * $from_email - who is sending me email
    * $name_from - the name of person who is sending me email
    * $currentLoggedUser - Yii::$app->user->getIdentity();
    */
    public static function sendEmailToAnyone($subject, $message, $to_email, $to_name, $currentLoggedUser=NULL)
    {
        //Create a new PHPMailer instance
        $mail = new \PHPMailer;
        $mail->CharSet = 'UTF-8';
        //Set who the message is to be sent from
        if($currentLoggedUser==NULL)
            $mail->setFrom(Yii::$app->params['adminEmail'], "Dario Trbovic");
        else
            $mail->setFrom($currentLoggedUser->email, $currentLoggedUser->name);
        //Set who the mesdarsage is to be sent to
        $mail->addAddress($to_email, $to_name);
        //Set the subject line
        $mail->Subject = $subject;
        //msg

        if($currentLoggedUser==NULL)
        {
            //add signiture to the end of message
            $message.="<br><br>Dario Trbovic, Lead Developer @ Born2Invest";
            $message.="<br><a href='http://www.born2invest.com/' target='_blank'>Born2Invest</a>";
        }
        $mail->msgHTML(nl2br($message));

        //send the message, check for errors
        if (!$mail->send())
        {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Email wasnt sent'));
        }
        else
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Email was sent'));
        }

    }

    /*
    * send email to editors from site/contact-everyone
    * $from_email - who is sending me email
    * $name_from - the name of person who is sending me email
    * $currentLoggedUser - Yii::$app->user->getIdentity();
    * $exclude_email_array - don't send emails to those emails
    * $file - attachments
    */
    public static function sendEmailToEditors($subject, $message, $currentLoggedUser, $users, $exclude_email_array, $file)
    {
        $sentTo="";
        //Create a new PHPMailer instance
        $mail = new \PHPMailer;
        $mail->CharSet = 'UTF-8';
        //Set who the message is to be sent from
        $mail->setFrom($currentLoggedUser->email, $currentLoggedUser->name);
        $i=1;
        foreach($users as $key=>$value)
        {
            /*send email only if:
            1. current mail is not in list of excluded emails
            2. if user's account is active
            */
            if(!in_array($value->email,$exclude_email_array) && $value->status==1)
            {
                $mail->AddAddress($value->email, $value->name);  //Set who the message is to be sent to
                $sentTo.="$i. $value->email, $value->name<br>";
                $i++;
            }
        }
        //Set the subject line
        $mail->Subject = $subject;
        //msg
        $mail->msgHTML(nl2br($message));

        if(!empty($file))
        {
            foreach($file as $value)
            {
                //Attach an image file
                $mail->addAttachment(Yii::getAlias("@webroot").Story::PATH_TEMP_IMAGE.$value["file_name"]);
            }
        }


        //send the message, check for errors
        if (!$mail->send())
        {
            Yii::$app->session->addFlash('danger', Yii::t('app', 'Email wasnt sent'));
        }
        else
        {
            $setFlash=Yii::t('app', 'Email was sent')."<br>".$sentTo;
            Yii::$app->session->addFlash('success', $setFlash);
        }

    }

    /*
    * get cookie
    */
    public static function getCookie($cookie_name)
    {
        $cookies = Yii::$app->request->cookies;
        $cookie = $cookies->get($cookie_name);
        if ($cookie !== null)
        {
            return $cookie->value;
        }
        else
            return NULL;
    }

    /*
    * create cookie
    */
    public static function createCookie($cookie_name, $cookie_value, $expire=NULL)
    {
        if($expire==NULL)
            $expire=time() + (60*60*24*365*10); //current time + 10 years

        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => $cookie_name,
            'value' => $cookie_value,
            'expire' => $expire
        ]));
    }

    /*
    * remove cookie
    */
    public static function removeCookie($cookie_name)
    {
        Yii::$app->response->cookies->remove($cookie_name);
    }

    /*
    *  get current url
    */
    public static function currentDomain()
    {
        return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER["HTTP_HOST"];
    }



    /*
    *  return formatter if you need for date or datetime with timezone set
    */
    public static function returnFormatter()
    {
        $Formatter=new \yii\i18n\Formatter;
        $backend_timezone_cookie = Helpers::getCookie(\Yii::$app->params['backend_timezone_cookie']);
        $Formatter->defaultTimeZone=$backend_timezone_cookie;
        return  $Formatter;
    }

    /*
    * merge image stories with sponsored stories
    * dataProviderStory - all NOT sponsored stories
    * dataProviderSponsored - all sponsored stories
    * $array - extra data I need inside function like language_id and country_id
    */
    public static function findStories($dataProviderStory, $dataProviderSponsored, $array)
    {
        //position of sponsored stories, every $sponsored position (5, 10, 15, 20, ...)
        $frequency = Helpers::getSponsoredFrequency($array['language'], $array['country']);

        //if you cannot find in database frequency data then just return normal stories without sponsored
        if(empty($frequency))
            return $dataProviderStory;

        $sponsored = NULL;
        $httpool = NULL;


        if($frequency->type == SettingsStoryInject::NATIVE_SPONSORED)
            $sponsored = $frequency->frequency;
        elseif($frequency->type == SettingsStoryInject::HTTPOOL)
            $httpool = $frequency->frequency;


        //if($dataProviderSponsored != NULL && $sponsored != NULL) {
        $current = 0;
        $sponsoredNr = 0;
        //checking if normal stories exist, they don't exist on sponsored category
        if($dataProviderStory != NULL)
        {
            foreach($dataProviderStory as $story)
            {
                $current++;
                if($sponsored != NULL && $current % $sponsored == 0 && isset($dataProviderSponsored[$sponsoredNr]))
                {
                    //add "SPONSORED" word in front of summary of story
                    //$dataProviderSponsored[$sponsoredNr]['description'] = "SPONSORED - ".$dataProviderSponsored[$sponsoredNr]['description'];
                    $dataProvider[] = $dataProviderSponsored[$sponsoredNr];
                    $sponsoredNr++;
                    $current++;
                }

                //native sposnored and httpool exists inject httpool because you can calculate httpool index place
                if($sponsored != NULL && $httpool != NULL )
                {
                   /* if($current == ($sponsored + ($httpool - $sponsored))) {
                    $dataProvider[] = $httpoolStory;
                    $current++;
                    }*/
                }
                else if ($httpool != NULL)
                {
                   /* if($current == $httpool) {
                    $dataProvider[] = $httpoolStory;
                    $current++;
                    } */
                }

                //inject normal story
                $dataProvider[] = $story;
            }
        }
        else {
            //need to add httpool stories
            if($dataProviderSponsored != NULL) {
                foreach($dataProviderSponsored as $story) {
                $story['description'] = "SPONSORED - ".$story['description'];
                $dataProvider[] = $story;
                }
            }
            else {
                $dataProvider = array();
            }
        }
        //}
        //else
        //    $dataProvider = $dataProviderStory;

        return $dataProvider;
    }

    /*
    * return sponsored/httpool story frequency
    */
    public static function getSponsoredFrequency($language_id, $country_id)
    {
        $frequency =  SettingsStoryInject::find()->where(['language_id'=>$language_id, 'country_id'=>$country_id])->one();
        if ($frequency !== null) {
            return $frequency;
        } else {
            return NULL;
        }
    }

    /*
    * $path_to = Story::PATH_IMAGE, Story::PATH_VIDEO...
    * $model - loaded Story model
    */
    public static function getPathToPicture($model, $path_to)
    {
        return BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($model->date_created, $path_to, $model->image, false);
    }

    /*
    * $path_to = Story::PATH_IMAGE, Story::PATH_VIDEO...
    * $model - loaded Story model
    * $file_field - image_file, logo
    */
    public static function getPathToSponsoredPicture($model, $path_to, $file_field)
    {
        return BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($model->date_created, $path_to, $model->$file_field, false);
    }

}