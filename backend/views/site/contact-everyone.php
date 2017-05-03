<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model backend\models\LoginForm */

$this->title = Yii::t('app', 'Contact everyone');
?>
<div class="row">
    <div class="col-sm-12 col-lg-12">
        <div class="block">
            <div class="block-header">
                <ul class="block-options">
                </ul>
                <h3 class="block-title"><?=Yii::t('app', 'Contact everyone')?></h3>
            </div>
            <div class="block-content">
                <p>
                <?=Html::beginForm('', 'POST', ["enctype"=>"multipart/form-data"])?>
                <u><b><?=Yii::t('app', 'Exclude emails')?></b></u> (Separated by comma)
                <?=Html::textInput('exclude_email', null, ['class'=>'form-control', 'placeholder'=>'dario@born2invest.com,mario@born2invest.com...'] )?>
                <br>
                <?=Yii::t('app', 'Subject')?>
                <?=Html::textInput('subject', '', ['class'=>'form-control', 'required'=>'required'] )?>
                <br>
                <?=Yii::t('app', 'Message')?>
                <?=Html::textarea('body', '', ['class'=>'form-control', 'rows'=>10, 'required'=>'required'] )?>
                <br>
                <input type="file" name="attachment[]" multiple>
                <br>
                <br>
                <?=Html::submitButton(Yii::t('app', 'Submit'), ['class'=>'btn btn-minw btn-success', 'name'=>'send_email', 'required'=>'required'] ) ?>
                <?=Html::endForm()?>
                </p>
            </div>
        </div>
    </div>
</div>