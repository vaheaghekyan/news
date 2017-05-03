<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model backend\models\LoginForm */

$this->title = $title = Yii::t('app','Settings');
$this->title .= ' - '.Yii::t('app','Change Password');
$this->params['breadcrumbs'][] = $this->title ;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="block">
            <div class="block-header">
                <ul class="block-options">
                    <li>
                        <!--<button type="button"><i class="si si-settings"></i></button> -->
                    </li>
                </ul>
                <h3 class="block-title"><?= Html::encode($title) ?></h3>
            </div>
            <div class="block-content">
                <p>
                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                  //  'enableAjaxValidation' => false,
                    'id' => 'change-pass-form',
                    'options' => ['class' => 'form-horizontal'],
                    /*'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-1 control-label'],
                    ],*/
                ]); ?>

                <?= $form->field($model, 'old_password', [
                    //'template' => "{label}\n{input}\n{hint}\n{error}",
                    'options'   => [
                    ]
                ])->passwordInput(array()) ?>

                 <?= $form->field($model, 'new_password', [
                    'template' => "{label}\n{input}\n{hint}\n{error}",
                    'options'   => [
                    ]
                ])->passwordInput(array()) ?>


                 <?= $form->field($model, 'repeat_password', [
                    'template' => "{label}\n{input}\n{hint}\n{error}",
                    'options'   => [
                    ]
                ])->passwordInput(array()) ?>

                <input type="submit" value="<?= Yii::t('app','Submit')?>"class="btn btn-minw btn-success">

                 <?php ActiveForm::end(); ?>
                </p>
            </div>
        </div>
    </div>
</div>





