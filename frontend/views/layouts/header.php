<?php
use frontend\components\Helpers;
?>

<div class="header_container">
   <div class="header clearfix">
      <div class="logo">
         <h1><a href="/story/1/category/top-stories" title="BORN2INVEST"><img alt="BORN2INVEST" title="BORN2INVEST" src="<?= Helpers::frontendCDN() ?>/images/born2invest.png" style="width:300px;"></a></h1>
         <h4><?= Yii::t('app', 'Business and Finance News, in 80 words or less.')?></h4>
      </div>
      <div class="placeholder">

        <!-- BEGIN JS TAG - 728x90 < - DO NOT MODIFY --> <!-- Load ad from our other page, so we can dynamically change it on slide change -->

            <!-- <SCRIPT SRC="https://secure.adnxs.com/ttj?id=5742970&cb=[CACHEBUSTER][2]" TYPE="text/javascript"></SCRIPT> -->

            <iframe style="width: 728px; height: 90px;" id="tophttpool" src="/story/tophttpool" frameborder="0"></iframe>

        <!-- END TAG -->

    </div>
    <?php $detect = new \Mobile_Detect; if($detect->isMobile()) { $language = Yii::$app->language; if($detect->isiOS()) $link = "https://itunes.apple.com/".$language."/app/born2invest/id1048044533"; if($detect->isAndroidOS()) $link = "https://play.google.com/store/apps/details?id=com.borntoinvest.borntoinvest&hl=".$language."&referrer=utm_source%3Dnewspage%26utm_medium%3Dbanner%26utm_content%3D".$language; ?>
        <div style="padding-top: 20px;">
            <a onclick="GoogleAnalyticsClick('event', 'Redirect to Play Store', 'Click', <?= $language ?>)" href="<?= $link ?>"><img src="<?= Helpers::frontendCDN() ?>/images/B2I_320x50.gif" alt="Mobile" title="Mobile"></a>
        </div>
    <?php } ?>
   </div>
</div>