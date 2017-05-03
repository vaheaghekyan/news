<?php
use backend\components\Helpers;
?>
<script src="<?= Helpers::backendCDN() ?>/assets/js/core/bootstrap.min.js"></script>
<script src="<?= Helpers::backendCDN() ?>/assets/js/core/jquery.slimscroll.min.js"></script>
<script src="<?= Helpers::backendCDN() ?>/assets/js/core/jquery.scrollLock.min.js"></script>
<script src="<?= Helpers::backendCDN() ?>/assets/js/core/jquery.appear.min.js"></script>
<script src="<?= Helpers::backendCDN() ?>/assets/js/core/jquery.countTo.min.js"></script>
<script src="<?= Helpers::backendCDN() ?>/assets/js/core/jquery.placeholder.min.js"></script>
<script src="<?= Helpers::backendCDN() ?>/assets/js/core/js.cookie.min.js"></script>
<script src="<?= Helpers::backendCDN() ?>/assets/js/app.js"></script>

<!-- Page Plugins -->
<script src="<?= Helpers::backendCDN() ?>/assets/js/plugins/slick/slick.min.js"></script>

<!-- DATETIME PICKER https://github.com/xdan/datetimepicker -->
<script src="<?= Helpers::backendCDN() ?>/js/jquery.datetimepicker.js"></script>

<!--BOOTBOX, Modal Popup -->
<script src="<?= Helpers::backendCDN() ?>/js/bootbox.min.js"></script>

<!-- SweetAlert-->
<script src="<?= Helpers::backendCDN() ?>/js/sweetalert.min.js"></script>

<!-- Match Height-->
<script src="<?= Helpers::backendCDN() ?>/js/jquery.matchHeight-min.js"></script>

<!-- Bootstrap file upload style -->
<!--<script src="<?= Helpers::backendCDN() ?>/js/bootstrap-filestyle.min.js"></script>  -->

<!-- B2I js -->
<script src="<?= Helpers::backendCDN() ?>/js/b2i.js"></script>
<?php require Yii::getAlias('@webroot')."/js/b2i.js.php"; ?>

<script>
    $(function () {
        // Init page helpers (Slick Slider plugin)
        App.initHelpers('slick');
    });
</script>