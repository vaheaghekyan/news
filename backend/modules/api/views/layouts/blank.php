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
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="ie9 no-focus"> <![endif]-->
<!--[if gt IE 9]><!-->
<html class="no-focus"  lang=""> <!--<![endif]-->

<head>
<meta charset="<?= Yii::$app->charset ?>">
<title>

</title>

<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

      <?=$content?>

<?php $this->endBody() ?>
</body>
</html><?php $this->endPage() ?>

