<?php
use yii\helpers\Html;

$this->title=Yii::t('app', 'Contact');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="block">
            <div class="block-header">

                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="block-content">
                <p>
                 <?= Html::beginForm ('','post', $options = [] ) ?>
                 <?= Yii::t('app', 'Message')?>
                 <?= Html::textarea ('message',  '', ['class'=>'form-control', 'required'=>'required', 'rows'=>5]  ) ?>
                 <br>
                 <?= Html::submitButton (Yii::t('app', 'Submit'), ['class'=>'btn btn-minw btn-success', 'name'=>'send_email'] ) ?>
                </p>
            </div>
        </div>
    </div>
</div>