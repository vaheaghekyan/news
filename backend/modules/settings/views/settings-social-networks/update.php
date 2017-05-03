<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\settings\models\SettingsSocialNetworks */

$this->title = Yii::t('app', 'Update');

?>

<div class="settings-social-networks-create row">
     <div class="col-md-12">
         <div class="block">
            <div class="block-header">
                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="block-content">
                <p>
                  <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
                </p>

            </div>
        </div>
    </div>
</div>
