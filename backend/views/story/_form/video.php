<?php
use yii\helpers\Url;
?>

<div class="row">
    <div class="col-sm-12">
        <table id="create" class="table">
           <tr>
              <td class="noborder" valign="top">
                 <input type="button" value="<?= Yii::t('app', 'Video') ?>" class="btn btn-minw btn-primary btn-block">
                 <?php if ( $model->video ) : ?>
                 <p>
                    <a href="<?=Url::toRoute(['site/downloadvideo', 'storyId' => $model->id]) ?>" target="_blank" class="btn btn-minw btn-warning btn-block m-t-20" > <?= Yii::t('app', 'Download Video') ?></a>
                 </p>
                 <?php endif;?>
              </td>
              <td class="bginputtext noborder">
                 <?= $form->field($model, 'upload_video_field', [
                    'template'  => "{input}\n{hint}\n{error}",
                    'options'   => [
                        'id'        => 'upload-file-2',
                    ]
                    ])->textInput(array()) ?>
              </td>
           </tr>
        </table>
    </div>
</div>