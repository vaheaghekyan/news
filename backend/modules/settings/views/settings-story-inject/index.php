<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

use backend\models\Language;
use backend\models\Country;
use backend\modules\settings\models\SettingsStoryInject;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\settings\models\search\SettingsStoryInjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Settings Story Injects');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-story-inject-index row">
    <div class="col-sm-12">
        <div class="block">
            <div class="block-header">
                <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="block-content">
                <a href="<?= Url::to(['create'])?>" class="btn btn-minw btn-primary"><?= Yii::t('app', 'Create')?></a>
                <br><br>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class'=>'table table-striped table-borderless table-header-bg'],
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute'=>'language_id',
                            'value'=>function($data)
                            {
                                return $data->relationLanguage->name;
                            },
                            'filter'=>Language::dropDownActiveLanguages()
                        ],
                        [
                            'attribute'=>'country_id',
                            'value'=>function($data)
                            {
                                return $data->relationCountry->name;
                            },
                            'filter'=>Country::dropDownCountries()
                        ],
                        'frequency',
                        [
                            'attribute'=>'type',
                            'value'=>function($data)
                            {
                                return  SettingsStoryInject::returnInjectType($data->type);
                            },
                            'filter'=>SettingsStoryInject::injectType()
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete}'
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
