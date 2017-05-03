<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model backend\models\LoginForm */

$this->title = $title = Yii::t('app', 'Forgot password');
$this->params['breadcrumbs'][] = $this->title ;
?>

<div class="row">
  <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
    <!-- Reminder Block -->
    <div class="block block-themed animated fadeIn">
      <div class="block-header bg-primary">
        <ul class="block-options">
          <li> <a href="<?= Url::to(['/site/login']) ?>" data-toggle="tooltip" data-placement="left" title="<?= Yii::t('app', 'Log in') ?>"><i class="si si-login"></i></a> </li>
        </ul>
        <h3 class="block-title">
          <?= Yii::t('app', 'Password Reminder') ?>
        </h3>
      </div>
      <div class="block-content block-content-full block-content-narrow">
        <!-- Reminder Title -->
        <img src="/img/b2ilogo.png" class="img-responsive">
        <p>
          <?= Yii::t('app', 'Please provide your accounts email and we will send you your password.')?>
        </p>
        <!-- END Reminder Title -->

        <!-- Reminder Form -->
        <!-- jQuery Validation (.js-validation-reminder class is initialized in js/pages/base_pages_reminder.js) -->
        <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>
        <div class="form-group">
          <div class="col-xs-12">
            <div class="form-material form-material-primary floating">
              <?= $form->field($model, 'email', [
                'template' => "{input}\n{hint}",
                'options'   => [
                    "class" => "bginputtext"
                ]
            ])->textInput(array("placeholder" => "Email")) ?>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12 col-sm-6 col-md-5">
            <button class="btn btn-block btn-primary" type="submit"><i class="si si-envelope-open pull-right"></i> Send Mail</button>
          </div>
        </div>
        <?= $form->errorSummary($model, [
                "class" => "red small left mtop"
            ]); ?>
        <?php ActiveForm::end(); ?>

        <!-- END Reminder Form -->
      </div>
    </div>
    <!-- END Reminder Block -->
  </div>
</div>



