<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\preroll\models\AdsGeolocationTags */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Ads Geolocation Tags',
]) . ' ' . $model->tagId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ads Geolocation Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tagId, 'url' => ['view', 'id' => $model->tagId]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ads-geolocation-tags-update row">
    <div class="col-sm-12 col-lg-12">
        <div class="block">
            <div class="block-header">
                <ul class="block-options">
                </ul>
                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="block-content">
                <p>
                <?= $this->render('_form', [
                    'model' => $model,
                    'countries' => $countries,
                ]) ?>
                </p>
            </div>
        </div>
    </div>
</div>
