<?php
use backend\models\User;
use backend\models\Language;
use backend\models\Statistics;
use backend\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title=Yii::t('app', 'Daily Report') ;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="block">
            <div class="block-header">
                <h2 class="block-title"><?=Yii::t("app", "Daily report for")?>: <?= $date?></h2>
            </div>
            <div class="block-content">
                <?= Html::beginForm(Url::to(["/statistics/daily-report"]), 'get', []); ?>
                <?=Yii::t("app", "Choose different date")?>
                <b>(Date created)</b>
                <?php $select=(isset($_GET["date"])) ? $_GET["date"] : date("Y-m-d"); ?>
                <?= Html::textInput ('date', $select, ['class'=>'datepicker form-control']); ?>
                <br>


                <?php if(Helpers::columnVisible([User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_MARKETER]) ): ?>
                <?=Yii::t("app", "Author")?>
                <?php $select=(isset($_GET["user"])) ? $_GET["user"] : NULL; ?>
                <?= Html::dropDownList("user", $select, User::usersDropDownList(), ['prompt'=>'-', 'class'=>'form-control'] ); ?>
                <br>
                <?php endif; ?>

                <?= Html::submitButton(Yii::t('app', 'Generate daily report'),  ['class'=>'btn btn-minw btn-success'] ); ?>
                <?= Html::endForm(); ?>

                <p>
                <h3><?=Yii::t("app", "Total")?>: <?=count($stories)?></h3>
                </p>

                <p>
                <?php
                foreach($stories as $story)
                {
                    echo "<b>".$story->title."</b><br>";
                    echo $story->description."<br>";
                    echo '<a href="'.$story->link.'">'.$story->link."</a><br>";
                    echo "<hr>";
                }
                ?>
                </p>
                <br>
            </div>
        </div>
    </div>
</div>