<?php
use backend\models\User;
use backend\models\Language;
use backend\models\Statistics;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title=Yii::t('app', 'Statistics') ;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="block block-themed">
            <div class="block-header bg-amethyst">
                <ul class="block-options">
                    <li>
                        <button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
                    </li>
                </ul>
                <h3 class="block-title"><?= Yii::t('app', 'General') ?></h3>
            </div>
            <div class="block-content block-content-full ">
                <?= Html::beginForm(Url::to(["/statistics/index"]), 'post') ?>
                <?= Yii::t('app', 'Start date') ?>
                <input type="text" class="datetimepicker form-control" name="start_date" required="required" />
                <br>
                <?= Yii::t('app', 'End date') ?>
                <input type="text" class="datetimepicker form-control" name="end_date" required="required" />
                <br>
                <?= Yii::t('app', 'User') ?>
                <?= Html::dropDownList("user", null, User::usersDropDownList(), ['class'=>'form-control'])?>
                <br>
                <?= Yii::t('app', 'Language') ?>
                <?= Html::dropDownList("language", null, Language::dropDownActiveLanguages(), ['class'=>'form-control'])?>
                <br>
                <?= Yii::t('app', 'Type') ?>
                <?= Html::dropDownList("user", null, Statistics::statType(), ['class'=>'form-control'])?>
                <br>
                <?= Html::submitButton(Yii::t('app', 'Submit'), ['class'=>'btn btn-minw btn-success', 'name'=>'submit_statistics'])?>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>

    <?php if(isset($_POST["submit_statistics"])):?>  
    <?php include "_per_country.php";?>
    <?php include "_per_language.php";?>
    <?php require "_per_category.php";?>
    <?php  endif ?>

</div>