<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model backend\models\LoginForm */

$this->title = Yii::t('app', 'Welcome to Born2Invest News Admin');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
  <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
    <!-- Login Block -->
    <div class="block block-themed animated fadeIn">
      <div class="block-header bg-primary">
        <ul class="block-options">
          <li>
            <a href="<?=Url::to(['site/forgot-password'])?>">
              <?=Yii::t('app', 'Forgot Password?')?>
              </a>
          </li>
          <!--<li>
                                    <a href="base_pages_register.html" data-toggle="tooltip" data-placement="left" title="New Account"><i class="si si-plus"></i></a>
                                </li>-->
        </ul>
        <h3 class="block-title"><?= Yii::t('app', 'Login') ?></h3>
      </div>
      <div class="block-content block-content-full block-content-narrow">
        <!-- Login Title -->
        <h1 class="h2 font-w600 push-30-t push-5"><img src="/img/b2ilogo.png" class="img-responsive"></h1>
        <!-- END Login Title -->
        <!-- Login Form -->
        <!-- jQuery Validation (.js-validation-login class is initialized in js/pages/base_pages_login.js) -->
        <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]);
        ?>

        <div class="form-group">
            <div class="col-xs-12">
                <div class="form-material form-material-primary floating">
                    <?= $form->field($model, 'email',
                    [
                        'template' => "{input}\n{hint}",
                        'options'   => [
                        "class" => "bginputtext"
                    ]
                    ])->textInput(array("placeholder" => "Email"));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <div class="form-material form-material-primary floating">
                    <?= $form->field($model, 'password',
                    [
                        'template'  => "{input}\n{hint}",
                        'options'   => [
                        "class" => "bginputtext"
                    ]

                ])->passwordInput(array("placeholder" => "Password"));
                ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-6 col-md-4">
                <button class="btn btn-block btn-primary" type="submit"><i class="si si-login pull-right"></i><?= Yii::t('app', 'Log in') ?></button>
            </div>
        </div>
        
        <?= $form->errorSummary($model,
        [
            "class" => "red small left mtop"
        ]); ?>
        <?php ActiveForm::end(); ?>
        <!-- END Login Form -->
      </div>
    </div>
    <!-- END Login Block -->
  </div>
</div>



