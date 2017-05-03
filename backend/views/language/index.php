<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\LanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Languages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12 col-lg-12">
        <div class="block">
            <div class="block-header">
                <ul class="block-options">
                    <li></li>
                </ul>
                <h3 class="block-title"><?= Html::encode($this->title)?></h3>
            </div>
            <div class="block-content">
                <p>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions'=>['class'=>'table table-striped table-borderless table-header-bg'],
                    'columns' => [
                       // ['class' => 'yii\grid\SerialColumn'],

                        //'id',
                        'name',
                        'code',

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template'=>'{delete}'
                        ],
                    ],
                ]); ?>
                </p>
            </div>
        </div>
    </div>
</div>
