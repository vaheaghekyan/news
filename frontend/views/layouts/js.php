<?php
use backend\components\Helpers as BackendHelpers;
use frontend\components\Helpers;
use common\components\Helpers as CommonHelpers;
?>
<!-- CORE -->
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.ba-bbq.min.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery-ui-1.11.1.custom.min.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.carouFredSel-6.2.1-packed.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.touchSwipe.min.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.transit.min.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.sliderControl.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.timeago.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.hint.js"></script>
<!--<script type="text/javascript" src="/js/jquery.prettyPhoto.js"></script> -->
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.qtip.min.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/main.js"></script>
<script type="text/javascript" src="<?= Helpers::frontendCDN() ?>/js/odometer.min.js"></script>

<!-- BOOTSTRAP -->
<script src="<?= Helpers::frontendCDN() ?>/extra/js/bootstrap.min.js"></script>

<!-- COLORBOX -->
<script src="<?= Helpers::frontendCDN() ?>/extra/js/jquery.colorbox-min.js"></script>

<!--BOOTBOX, Modal Popup -->
<script src="<?= BackendHelpers::backendCDN() ?>/js/bootbox.min.js"></script>

<!-- SweetAlert-->
<script src="<?= Helpers::frontendCDN() ?>/extra/js/sweetalert.min.js"></script>

<!-- SwipePlugin-->
<script src="<?= Helpers::frontendCDN() ?>/extra/js/jquery.touchSwipe.min.js"></script>

<!-- Cookie -->
<script src="<?= Helpers::frontendCDN() ?>/extra/js/jquery.cookie.js"></script>

<!-- JWPlayer -->
<script type="text/javascript" src="http://content.jwplatform.com/libraries/V6rhrb5u.js"></script>

<!-- bxSlider Javascript file -->
<script src="<?= Helpers::frontendCDN() ?>/extra/js/jquery.bxslider.min.js"></script>



<!-- B2I js -->
<script src="<?= Helpers::frontendCDN() ?>/extra/js/b2i_frontend.js"></script>
<?php require Yii::getAlias('@webroot')."/extra/js/b2i_frontend.js.php"; ?>

<!-- GA Code -->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    var userId="<?=CommonHelpers::getCookie(Yii::$app->params['frontend_user_id_cookie']);?>";

    ga('create', 'UA-59123760-6', 'auto', {'userId': userId});
    ga('set', '&uid', userId); //Set the user ID using signed-in user_id.
    ga('send', 'pageview');
    ga('set', 'dimension1', userId);

</script>