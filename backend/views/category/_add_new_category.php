<?php
use backend\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="row add_new_cat" style="display:none">
    <div class="col-sm-12">

        <div class="block">
            <div class="block-header">
                <ul class="block-options">
                    <li>
                        <!--<button type="button"><i class="si si-settings"></i></button>-->
                    </li>
                </ul>
                <h3 class="block-title"><?= Yii::t('app', 'Add category')?></h3>
            </div>
            <div class="block-content block-content-narrow">
                <?= Html::beginForm(Url::to(['/category/create']), 'post'); ?>
                    <div class="form-group">
                        <label for="example-nf-email"><?= Yii::t('app', 'Category name')?></label>
                        <?= Html::activeTextInput($Category, 'name', ['class'=>'form-control'] ); ?>
                    </div>
                    <div class="form-group">
                        <label for="example-nf-password"><?= Yii::t('app', 'Subcategory name')?></label>
                         <?= Html::activeTextInput($CategoriesLevelOne, 'name', ['class'=>'form-control']); ?>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-minw btn-success" type="submit"><?= Yii::t('app', 'Submit')?></button>
                    </div>
                <?=  Html::endForm(); ?>
            </div>
        </div>
    </div>
</div>
