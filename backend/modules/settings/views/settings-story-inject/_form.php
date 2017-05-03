<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Language;
use backend\models\Country;
use backend\modules\settings\models\SettingsStoryInject
/* @var $this yii\web\View */
/* @var $model backend\modules\settings\models\SettingsStoryInject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="settings-story-inject-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'language_id')->dropDownList(Language::dropDownActiveLanguages()) ?>

    <?= $form->field($model, 'country_id')->dropDownList(Country::dropDownCountries()) ?>

    <?= $form->field($model, 'frequency')->input('number') ?>

    <?= $form->field($model, 'type')->dropDownList(SettingsStoryInject::injectType() ) ?>

    <div class="form-group">                         
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-minw btn-success' : 'btn btn-minw btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
