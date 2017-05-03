<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Language */


$this->title = Yii::t('app', 'Add Language') ; ;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="block">
            <div class="block-header">
                <ul class="block-options">
                    <li>
                        <button type="button"><i class="si si-settings"></i></button>
                    </li>
                </ul>
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
