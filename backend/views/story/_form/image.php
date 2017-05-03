<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\components\Helpers;

//get image name, but if model is newRecord then you have to autogenerate image name
//if you can get name from session get it
if (empty($model->image))
{
    $ssn=Helpers::storySession("echo", $model, "story_img_name_");
    $image_name=$ssn;
    if($image_name==NULL)
        $image_name="img";
}
else
    $image_name=$model->image;
?>
<div class="row">
    <div id="category" class="gpad col-sm-12">
       <h2>
          <?= Yii::t('app', 'Media') ?>
       </h2>
       <div class="m-b-20 m-t-20">
          <?= Yii::t('app', 'Image/Video name') ?>
          <small>(e.g. Obama is new president of USA)</small>
          <?=Html::textInput ("image_name", $image_name,
             ['class'=>'form-control', 'required'=>'required'] ) ?>
       </div>
      <div class="m-b-20 m-t-20">
          <?= Yii::t('app', 'Alt tag') ?>
          <small>(e.g. Obama is new president of USA)</small>
          <?= $form->field($model, 'alt_tag', [
             'template' => "{input}\n{hint}\n{error}",
             'options'   => [
             ]
             ])->textInput(array('required'=>'required', 'value'=>Helpers::storySession("echo", $model, "story_alt_tag_") )) ?>
       </div>
       <table id="create" class="table">
          <tr>
             <td class="noborder" valign="top">
                <input type="button" value="<?= Yii::t('app', 'HD Image') ?>" class="btn btn-minw btn-primary btn-block">
                <?php if ( $model->image ) : ?>
                <p>
                   <a href="<?=Url::toRoute(['site/downloadimage', 'storyId' => $model->id]) ?>" target="_blank" class="btn btn-minw btn-warning btn-block m-t-20" >
                   <?= Yii::t('app', 'Download Image') ?>
                   </a>
                </p>
                <?php endif;?>
             </td>
             <td class="bginputtext noborder">
                <?= $form->field($model, 'upload_image_field', [
                   'template'  => "{input}\n{hint}\n{error}",
                   'options'   => [
                       'id'        => 'upload-file-1',
                   ]
                   ])->textInput(array()) ?>
                <div class="progress active">
                   <div class="progress-bar progress-bar-success progress-bar-striped progress_bar_upload_image" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%</div>
                </div>
             </td>
          </tr>
       </table>
    </div>
</div>