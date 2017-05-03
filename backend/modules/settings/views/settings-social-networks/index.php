<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Country;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\settings\models\search\SettingsSocialNetworksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', Yii::t('app', 'Settings Social Networks'));
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-social-networks-index row">
    <div class="col-md-12">
        <div class="block">
            <div class="block-header">
                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="block-content">
                <?= Html::a(Yii::t('app', Yii::t('app', 'Create')), ['create'], ['class' => 'btn btn-minw btn-success']) ?>
                <p>
                <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions'=>['class'=>'table table-striped table-borderless table-header-bg'],
                'columns' => [
                  //  ['class' => 'yii\grid\SerialColumn'],

                   // 'id',
                    [
                        'attribute'=>'country_id',
                        'value'=>function($data)
                        {
                            return $data->relationCountry->name;
                        },
                        'filter'=>Html::activeDropDownList($searchModel, 'country_id', Country::dropDownCountries(), ['class'=>'form-control', 'prompt'=>''])
                    ],
                    [
                        'attribute'=>'group_concat_social_network_alias',
                        'format'=>'html',
                        'label'=>Yii::t('app', 'Social networks'),

                    ],
                    //'social_network',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template'=>'{update}'
                    ],
                ],
            ]); ?>
                </p>

            </div>
        </div>
    </div>
</div>
