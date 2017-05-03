<?php
use yii\web\View;
use backend\models\Country;
$this->title=Yii::t('app', 'Add timezone');

?>
<div class="block">
  <div class="block-header">
    <h3 class="block-title"><?= Yii::t('app', 'Add timezone') ?></h3>
  </div>
  <div class="block-content">
    <div class="row items-push">
        <div class="col-sm-12 col-lg-12">
            <!-- Warning Alert -->
            <div class="alert alert-warning alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <p><?= Yii::t('app', 'Add timezone info')?></p>
            </div>
            <!-- END Warning Alert -->
        </div>
      <div class="col-sm-12">
            <?= $this->render('_form', [
            'model' => $model,
            ]) ?>
      </div>

    </div>
  </div>
</div>
