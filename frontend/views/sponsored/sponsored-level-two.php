<?php
use backend\models\SponsoredLevelTwo;
use yii\helpers\Url;
use backend\components\Helpers as BackendHelpers;
use frontend\components\Helpers;
?>
<!DOCTYPE html>

<html>

<head>
    <title><?= $model->title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link href='//fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,300,300italic,400italic,700,700italic&subset=latin,greek,vietnamese,greek-ext,cyrillic-ext,latin-ext' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,700,300,100&subset=latin,cyrillic-ext,latin-ext,greek,greek-ext,vietnamese,cyrillic' rel='stylesheet' type='text/css'>
    <style>
    body
    {
        background-color:#f7f7f7;
        font-family:"Roboto";
    }

    .container
    {
        padding-bottom: 30px;
        padding-top: 150px;
    }

    .main-text
    {
        margin-top:20px;
        color:#969696;
    }

    div.hr
    {
        border-bottom:1px solid #c0c0c0;
        margin-top:5px;
        margin-bottom:5px;
    }

    .caption
    {
        margin-top:5px;
        margin-bottom:40px;
    }

    .h1, .h2, .h3, h1, h2, h3 {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .btn-trade-now
    {
        border:2px solid #81aa40;
        color:#81aa40;
        font-weight:bold;
        background-color:#f7f7f7;
    }

    .btn-more-info
    {
        border:2px solid #81aa40;
        color:white;
        font-weight:bold;
        background-color:#81aa40;
    }

    .main-story
    {
        margin-bottom:30px;
    }

    .main-img
    {
        margin-top:40px
    }

    .company_name
    {
        text-align:center;
    }

    .modal-backdrop.in {
        z-index: 40000;
    }

    .modal-open .modal {
        z-index: 50000;
    }

    .header_container
    {
    	background-color: #f7f7f7;
    	padding-bottom: 26px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        margin: 0 auto;
        z-index: 30000;
    }

    .logo {
        float: left;
        text-align: center !important;
        width: 300px;
    }

    .header
    {
    	text-align: center;
    	padding-top: 7px;
        width: 1050px;
    	margin-left: auto;
    	margin-right: auto;
    }
    .header h1
    {
    	font-size: 66px;
    	font-weight: 700;
    	font-family: 'Roboto Condensed';
    	color: #81aa3f;
    	letter-spacing: -0.01em;
    	line-height: 1;
        margin: 0;
        padding: 0;
    }
    .header h1 a
    {
    	color: #81aa3f;
    }

    .header h4 {
        color: black;
        font-family: "Roboto Condensed";
        line-height: 1;
        margin-top: 3px;
        font-size: 14px;
        font-weight: normal;
    }
    .header .placeholder
    {
    	/*display: none;*/
    	float: right;
    	font-size: 30px;
    	font-family: 'Roboto Condensed';
    	font-weight: 300;
    	background: #F0F0F0;
    	width: 728px;
    	height: 61px;
    	margin-top: 7px;
    	color: #ABABAB;
    	text-align: center;
    }

    </style>

    <script>
    $(document).ready(function()
    {
        setTimeout(function(){ caption() }, 500);
    });

    $(window).resize(function()
    {
        caption();
    });

    //adjust caption size to image
    function caption()
    {
        var w = $(".main-img").width();
        $(".caption").width(w);
    }
    </script>
</head>

<body>

<div class="header_container">
   <div class="header clearfix">
      <div class="logo">
         <h1><a href="/story/1/category/top-stories" title="BORN2INVEST"><img alt="BORN2INVEST" title="BORN2INVEST" src="http://cdn.news.born2invest.com/images/born2invest.png" style="width:300px;"></a></h1>
         <h4><?= Yii::t('app', 'Business and Finance News, in 80 words or less.')?></h4>
      </div>
      <div class="placeholder">

        <!-- BEGIN JS TAG - 728x90 < - DO NOT MODIFY --> <!-- Load ad from our other page, so we can dynamically change it on slide change -->

            <!-- <SCRIPT SRC="https://secure.adnxs.com/ttj?id=5742970&cb=[CACHEBUSTER][2]" TYPE="text/javascript"></SCRIPT> -->

            <iframe style="width: 728px; height: 90px;" id="tophttpool" src="/story/tophttpool" frameborder="0"></iframe>

        <!-- END TAG -->

    </div>
    <?php $detect = new \Mobile_Detect; if($detect->isMobile()) { $language = Yii::$app->language; if($detect->isiOS()) $link = ""; if($detect->isAndroidOS()) $link = "https://play.google.com/store/apps/details?id=com.borntoinvest.borntoinvest&hl=".$language."&referrer=utm_source%3Dnewspage%26utm_medium%3Dbanner%26utm_content%3D".$language; ?>
        <div style="padding-top: 20px;">
            <a onclick="GoogleAnalyticsClick('event', 'Redirect to Play Store', 'Click', <?= $language ?>)" href="<?= $link ?>"><img src="<?= Helpers::frontendCDN() ?>/images/B2I_320x50.gif" alt="Mobile" title="Mobile"></a>
        </div>
    <?php } ?>
   </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12 main-story">
            <div class="company_name"><?=$model->company_name?></div>
            <div class="hr"></div>
            <h1><?= $model->title ?></h1>
            <div class="hr"></div>
            <div class="main-text">
                <p>
                    <?php
                    if($model->image_position==SponsoredLevelTwo::IMAGE_POS_1)
                    {
                        echo $model->paragraph_one;
                        echo $image_file;
                    }
                    else
                        echo $model->paragraph_one;
                    ?>
                </p>

                <p>
                    <?php
                    if($model->image_position==SponsoredLevelTwo::IMAGE_POS_2)
                    {
                        echo $model->paragraph_two;
                        echo $image_file;
                    }
                    else
                        echo $model->paragraph_two;
                    ?>
                </p>

                <p>
                    <?php
                    if($model->image_position==SponsoredLevelTwo::IMAGE_POS_3)
                    {
                        echo $model->paragraph_three;
                        echo $image_file;
                    }
                    else
                        echo $model->paragraph_three;
                    ?>
                </p>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6 text-right">
                <a href="" class="btn btn-default btn-lg btn-trade-now"><?= strtoupper(Yii::t("app", "Trade now")) ?></a>
            </div>
            <div class="col-xs-6 text-left">
                <a href="javascript:;" class="btn btn-default btn-lg btn-more-info" onClick="bootboxDialogSponsored()"><?= strtoupper(Yii::t("app", "More info")) ?></a>
            </div>
        </div>
    </div>



<!-- BOOTSTRAP -->
<script src="<?= Helpers::frontendCDN() ?>/extra/js/bootstrap.min.js"></script>
<!--BOOTBOX, Modal Popup -->
<script src="<?= BackendHelpers::backendCDN() ?>/js/bootbox.min.js"></script>

<script>
function bootboxDialogSponsored()
{
    bootbox.dialog(
    {
        //title: "",
        message: '<p style="width: 20%;"><?= $logo ?></p><h2 style="text-align: center; padding-bottom: 15px; border-bottom: 1px solid black;"><?= $model->title ?></h2><p><?= $wufoo ?></p>',
        /*buttons: {
            success:
            {
                label: "",
                callback: function ()
                {

                }
            },
            danger:
            {
                label: "",
                callback: function ()
                {

                }
            }

        }*/
    });
}
</script>
</body>
</html>