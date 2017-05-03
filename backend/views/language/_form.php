<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\LanguagesAll;

/* @var $this yii\web\View */
/* @var $model backend\models\Language */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="language-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->dropDownList(LanguagesAll::dropDownNewLanguages()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-minw btn-success' : 'btn btn-minw btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
