<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use backend\assets\AppAsset;
use backend\models\Language;
use yii\helpers\Url;
use backend\components\Helpers;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$currentLanguage = Language::getCurrent();

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="ie9 no-focus"> <![endif]-->
<!--[if gt IE 9]><!-->
<html class="no-focus"  lang="<?= Yii::$app->language ?>"> <!--<![endif]-->

<head>
<meta charset="<?= Yii::$app->charset ?>">
<title>
<?= Html::encode($this->title) ?>
</title>
<?= Html::csrfMetaTags() ?>
<meta name="description" content="Born2Invest - Business and Finance news in 80 words or less">
<meta name="author" content="Born2Invest">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
<script src="<?= Helpers::backendCDN() ?>/assets/js/core/jquery.min.js"></script>
<?php $this->head() ?>
<?php require "head.php"; ?>
</head>
<body>
<?php $this->beginBody() ?>
<!-- Page Container -->
<!--
Available Classes:

'enable-cookies'             Remembers active color theme between pages (when set through color theme list)

'sidebar-l'                  Left Sidebar and right Side Overlay
'sidebar-r'                  Right Sidebar and left Side Overlay
'sidebar-mini'               Mini hoverable Sidebar (> 991px)
'sidebar-o'                  Visible Sidebar by default (> 991px)
'sidebar-o-xs'               Visible Sidebar by default (< 992px)

'side-overlay-hover'         Hoverable Side Overlay (> 991px)
'side-overlay-o'             Visible Side Overlay by default (> 991px)

'side-scroll'                Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (> 991px)

'header-navbar-fixed'        Enables fixed header
-->
<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed">
  <?php require "side-overlay.php";  ?>

  <!-- Sidebar -->
  <?php require "sidebar.php";  ?>
  <!-- END Sidebar -->

  <!-- Header -->
  <?php require "header.php";  ?>
  <!-- END Header -->

  <!-- Main Container -->
  <main id="main-container">

    <!-- Page Content -->
    <div class="content">
        <?= $this->render("@backend/views/_alert"); ?>
      <?=$content?>
    </div>
    <!-- END Page Content -->
  </main>
  <!-- END Main Container -->

  <!-- Footer -->
   <?php require "footer.php"; ?>
  <!-- END Footer -->
</div>
<!-- END Page Container -->

<!-- Apps Modal -->
<?php require "apps-modal.php" ?>
<!-- END Apps Modal -->

<!-- OneUI Core JS: jQuery, Bootstrap, slimScroll, scrollLock, Appear, CountTo, Placeholder, Cookie and App.js -->
<?php require "js.php"; ?>

<?php $this->endBody() ?>
</body>
</html><?php $this->endPage() ?>

