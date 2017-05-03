<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Timezone;

/* @var $this yii\web\View */
/* @var $model backend\models\TimezoneUser */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'timezone_id')->dropDownList(Timezone::dropDownListTimezone()) ?>


<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-minw btn-success' : 'btn btn-minw btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
