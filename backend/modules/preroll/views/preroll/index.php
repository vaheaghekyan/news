<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\preroll\models\search\AdsGeolocationTagsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ads Geolocation Tags');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ads-geolocation-tags-index row">
    <div class="col-sm-12 col-lg-12">
        <div class="block">
            <div class="block-header">
                <ul class="block-options">
                </ul>
                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="block-content">
                <p>
                 <p>
                    <?= Html::a(Yii::t('app', 'Create Ads Geolocation Tags'), ['create'], ['class' => 'btn btn-success']) ?>
                </p>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions'=>['class'=>'table table-striped table-borderless table-header-bg'],
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],

                        //'tagId',
                        'tagName',
                        [
                            'attribute'=>'tagUrl',
                            'format'=>'html',
                            'value'=>function($data)
                            {
                                return Html::a("Url", $data->tagUrl);
                            }
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template'=>'{update} {delete}'
                        ],
                    ],
                ]); ?>
                </p>
            </div>
        </div>
    </div>
</div>
