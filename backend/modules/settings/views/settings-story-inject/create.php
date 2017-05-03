<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\settings\models\SettingsStoryInject */

$this->title = Yii::t('app', 'Create Settings Story Inject');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Settings Story Injects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-story-inject-create row">

    <div class="col-sm-12">
        <div class="block">
            <div class="block-header">
                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="block-content">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
