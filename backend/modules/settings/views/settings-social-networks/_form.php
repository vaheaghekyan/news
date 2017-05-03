<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\settings\models\SettingsSocialNetworks;
use backend\models\Country;

/* @var $this yii\web\View */
/* @var $model backend\modules\settings\models\SettingsSocialNetworks */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
.checkbox_list_label
{

}

</style>
<div class="settings-social-networks-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'country_id')->dropDownList(Country::dropDownCountries()) ?>

    <?= $form->field($model, 'social_network')->checkboxList(SettingsSocialNetworks::socialNetworkSites(), [
                                                            'itemOptions'=>['labelOptions'=>['class'=>'col-md-3']]]) ?>

    <div class="form-group" style="clear:both">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
