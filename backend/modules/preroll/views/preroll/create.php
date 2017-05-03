<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\preroll\models\AdsGeolocationTags */

$this->title = Yii::t('app', 'Create Ads Geolocation Tags');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ads Geolocation Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ads-geolocation-tags-create row">
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
                ]) ?>
                </p>
            </div>
        </div>
    </div>
</div>
