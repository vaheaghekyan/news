<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
?>
<div class="row">
    <div class="col-sm-12 col-md-4 col-lg-6">
       <div id="category" class="gpad container_select_country">
          <h2>
             <?= Yii::t('app', 'Countries') ?>
          </h2>
          <h5>
             <?= Yii::t('app', 'Select at least one country') ?>
          </h5>
          <a class="expander collapsed btn btn-sm btn-default btn-block" data-target="countries-list"> <i class="fa fa-arrow-down pull-left"></i>
          <?= strtoupper(Yii::t('app','Country')); /*strtoupper(Language::getCurrentLanguage()->name) */?>
          </a></strong>
          <div class="countries-list">
             <?php
                $arrayHelper=ArrayHelper::map($countries, 'id', function($countries)
                {
                    return Yii::t('app', $countries->name);
                });

                echo Html::checkboxList ('country',
                $selectedCheckbox_country_tmp,
                $arrayHelper,
                [['separator'=>'<br>'], 'itemOptions'=>['labelOptions'=>['class'=>'label label-default label-cstm bg-muted']]] )

                ?>
          </div>
       </div>
    </div>
</div>