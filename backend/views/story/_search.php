<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Story;
/* @var $this yii\web\View */
/* @var $model backend\models\search\StorySearch */
/* @var $form yii\widgets\ActiveForm */
$action=Yii::$app->controller->action->id;
?>

<div class="story-search row" style="display:none;">
    <div class="col-sm-12 ">
        <div class="block block-themed">
            <div class="block-header bg-smooth-dark">
                <ul class="block-options">
                </ul>
                <h3 class="block-title"><?= Yii::t('app', 'Search') ?></h3>
            </div>
            <div class="block-content">
                <?php $form = ActiveForm::begin([
                    'action' => [$action],
                    'method' => 'get',
                ]); ?>

                <?php $items=['date_created'=>Yii::t('app', 'Date Created'), 'date_published'=>Yii::t('app', 'Date Published'), 'date_modified'=>Yii::t('app', 'Last Modification Date')]; ?>
                <?= Html::dropDownList("between_what", null,  $items, ['class'=>'form-control'] )?>
                <br>
                <div class="col-sm-6"><?= Html::textInput ('between_start', null, ['class'=>'datepicker form-control', 'placeholder'=>Yii::t('app', 'Start date 2')] ) ?></div>
                <div class="col-sm-6"><?= Html::textInput ('between_end',  null, ['class'=>'datepicker form-control', 'placeholder'=>Yii::t('app', 'End date 2')] ) ?></div>

                <b><?=Yii::t('app', 'Link')?></b>
                <?= Html::activeTextInput($model,'link', ['class'=>'form-control']) ?>
                <br>  
                 <b><?=Yii::t('app', 'Author')?></b>
                <?= Html::activeTextInput($model,'filter_author', ['class'=>'form-control']) ?>
                <br>
                <b><?=Yii::t('app', 'Country')?></b>
                <?= Html::activeDropDownList($model,'filter_country', $dropDownCountriesStoryDepending, ['class'=>'form-control', 'prompt'=>'']) ?>
                <br>
                <b><?=Yii::t('app', 'Category')?> </b>
                <?= Html::activeDropDownList($model,'filter_category', $dropDownListCategories, ['class'=>'form-control', 'prompt'=>'']) ?>
                <br>
                 <b><?=Yii::t('app', 'Media')?> </b>
                <?= Html::activeDropDownList($model, "filter_media", Story::dropDownFilterMedia(), ['class'=>'form-control'] ) ?>
                <br>
                <div class="form-group">
                    <?= Html::submitButton('Search', ['class' => 'btn btn-success']) ?>
                    <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>