<?php
   /* @var $this \yii\web\View */
   /* @var $content string */

   use yii\helpers\Html;
   use yii\bootstrap\Nav;
   use yii\bootstrap\NavBar;
   use yii\widgets\Breadcrumbs;
   use frontend\assets\AppAsset;
   use common\widgets\Alert;
   use backend\components\Helpers as BackendHelpers;
   use common\components\Helpers as CommonHelpers;
   use frontend\components\Helpers;

   AppAsset::register($this);
   ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
   <head>
      <meta charset="<?= Yii::$app->charset ?>">
      <?= Html::csrfMetaTags() ?>
      <title>
         <?= Html::encode($this->title) ?>
      </title>
      <script src="<?= BackendHelpers::backendCDN() ?>/assets/js/core/jquery.min.js"></script>
      <?php $this->head() ?>
      <?php require "head.php"; ?>
   </head>
   <body>
      <div id="settings"><a href="javascript:bootboxDialogEdition();"><img src="<?= Helpers::frontendCDN() ?>/images/icons/settings.png"></a></div>
      <?php $this->beginBody() ?>
      <div class="site_container">
        <?php require "header.php" ?>
      <?php require "top-menu.php" ?>

        <div class="page">
            <div class="page_layout page_margin_top clearfix">
                <div class="row page_margin_top">
                    <?= Alert::widget() ?>
                    <?= $content ?>
                    <?php require "right_ads.php" ?>
                </div>
            </div>
        </div>

      </div>
      <?php require "footer.php"; ?>
      <?php require "js.php"; ?>
      <?php $this->endBody() ?>

   </body>
</html>
<?php $this->endPage() ?>