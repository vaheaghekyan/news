<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
?>
<div class="col-sm-12 col-md-4 col-lg-6">
   <div id="category" class="gpad container_select_category">
      <h2>
         <?= Yii::t('app', 'Category') ?>
      </h2>
      <h5>
         <?= Yii::t('app', 'Select at least one Category and Subcategory') ?>
      </h5>
      <?php foreach ( $categories as $category ) : ?>
      <?php if($category->name=="SPONSORED") continue; ?>
      <a class="expander collapsed btn btn-sm btn-default btn-block" data-target="category-<?=$category->id?>"> <i class="fa fa-arrow-down pull-left"></i>
      <?=strtoupper(Yii::t('app', $category->name) )?>
      </a></strong>
      <div class="category-<?=$category->id?>">
         <?php
            $arrayHelper=ArrayHelper::map($category->relationCategoriesLevelOne, 'id', function($category)
            {
                return Yii::t('app', $category->name);
            });
            echo Html::checkboxList ('category',
            $selectedCheckbox_category_tmp,
            $arrayHelper,
            [['separator'=>'<br>'], 'itemOptions'=>['labelOptions'=>['class'=>'label label-default label-cstm bg-muted']]] )

            ?>
      </div>
      <?php endforeach; ?>
   </div>
</div>