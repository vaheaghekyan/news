<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use backend\assets\AppAsset;
use backend\models\Language;
use yii\helpers\Url;
use backend\models\User;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$currentLanguage = Language::getCurrent();

?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <!--<link href='https://fonts.googleapis.com/css?family=Ubuntu:400,300,300italic,400italic,500,500italic,700,700italic' rel='stylesheet' type='text/css' />-->
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,700italic,400italic' rel='stylesheet' type='text/css'>
    <script>
        var USER_ROLE         = "<?=(!Yii::$app->user->isGuest ? Yii::$app->user->getIdentity()->role : "GUEST")?>";
        var ROLE_SUPERADMIN   = "<?=User::ROLE_SUPERADMIN?>";
        var ROLE_ADMIN        = "<?=User::ROLE_ADMIN?>";
        var ROLE_SENIOREDITOR = "<?=User::ROLE_SENIOREDITOR?>";
        var ROLE_EDITOR       = "<?=User::ROLE_EDITOR?>";
        var USER_ID           = <?=Yii::$app->user->getId()?>;
    </script>
</head>
<body>
<?php $this->beginBody() ?>
<div class="section group">
    <div id="sidebar" class="col span_1_of_10 matchheight">
        <div id="fixednav">
            <div class="center"><img src="/img/logo.png"></div>
            <ul id="menu">
                <li class="dashboard <?=(Yii::$app->controller->id == "admin" && Yii::$app->controller->action->id == "index" ? "active" : "")?>"><a href="<?=Url::to(["admin/index"])?>">Dashboard</a></li>
                <li class="categories <?=(Yii::$app->controller->id == "category" ? "active" : "")?>"><a href="<?=Url::to(["category/index"])?>">Categories</a></li>
                <li class="stories <?=(Yii::$app->controller->id == "story" &&  Yii::$app->controller->action->id == "index" ? "active" : "")?>"><a href="<?=Url::to(["story/index"])?>">Stories</a></li>
                <li class="create-story <?=(Yii::$app->controller->id == "story" &&  Yii::$app->controller->action->id == "create" ? "active" : "")?>"><a href="<?=Url::to(["story/create"])?>">Create Story</a></li>
                <li class="countries <?=(Yii::$app->controller->id == "country" ? "active" : "")?>"><a href="<?=Url::to(["country/index"])?>">Countries</a></li>
                <li class="users <?=(Yii::$app->controller->id == "user" ? "active" : "")?>"><a href="<?=Url::to(["user/index"])?>">Users</a></li>
                <li class="settings <?=(Yii::$app->controller->id == "admin" && Yii::$app->controller->action->id == "settings" ? "active" : "")?>"><a href="<?=Url::to(["admin/settings"])?>">Settings</a></li>
            </ul>
        </div>
    </div>
    <div id="content" class="col span_9_of_10 matchheight">
        <div id="welcome" class="section group pright">
            <div class="col span_1_of_5">

                <select name="language">
                    <?php

                    if ( Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN ||
                        Yii::$app->user->getIdentity()->role == User::ROLE_ADMIN  ) {

                        $languages = Language::find()->all();

                    } else {

                        $languages = Yii::$app->user->getIdentity()->getLanguages()->all();

                    }

                    ?>
                    <?php foreach ( $languages as $language ) : ?>
                        <option value="<?=$language->code?>" <?=($currentLanguage == $language->code ? "selected" : "")?> ><?=$language->name?></option>
                    <?php endforeach; ?>
                </select>

            </div>
            <div class="col span_4_of_5">
                <p class="pright">Welcome <?=Yii::$app->user->getIdentity()->name?>, you are logged as <?=Yii::$app->user->getIdentity()->role?>. <a href="<?=Url::to(['site/logout'])?>">Logout</a></p>
            </div>
        </div>

        <?php if ( Yii::$app->session->hasFlash('success') ) : ?>
        <div id="blue" class="section group pright">
            <div class="col span_5_of_5">
                <?=Yii::$app->session->getFlash('success')?>
            </div>
        </div>
        <?php endif; ?>

        <?=$content?>
    </div>
</div>

<?php $this->endBody() ?>

<script src="/js/jquery.matchHeight.js"></script>

<script type="text/javascript">
    $(function($){
        $('.matchheight').matchHeight();
        $('select[name=language]').change(function(){

            document.location.href = '<?=Url::to(['site/change-language'])?>?languageCode=' + $(this).val() + "&route=<?=Yii::$app->controller->getRoute()?>";

        });
    });
</script>
</body>
</html>
<?php $this->endPage() ?>
