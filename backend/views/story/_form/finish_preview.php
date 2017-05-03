<?php
use yii\helpers\Html;
use backend\models\Story;
use backend\components\Helpers;
?>
<div class="col-sm-12 col-md-4 col-lg-6">
   <div id="category" class="gpad">
      <h2>
         <?= Yii::t('app', 'Finish') ?>
      </h2>
      <h5>
         <?= Yii::t('app', 'Please verify all information') ?>
         .
      </h5>
      <table id="countries" width="100%" cellpadding="0" cellspacing="0">
         <tr>
            <td class="noborder" width="50%"><input type="button" class="btn btn-minw btn-primary" id="preview-btn" value="<?= Yii::t('app', 'Preview') ?>"></td>
            <td class="noborder" colspan="2" width="50%"><input type="submit" class="btn btn-minw btn-success" id="save-story" value="<?= Yii::t('app', 'Finish') ?>"></td>
            <td class="noborder" colspan="2" width="100%"><b><?=Yii::t("app", "Schedule")?></b>
            <?= Html::textInput("date_published", Helpers::storySession("echo", $model, "story_schedule_"), ['class'=>'datetimepicker date_published form-control-static']);?>
            <input type="hidden" value="<?= date("Y-m-d H:i:s") ?>" id="server_time" />
            <b>Server Time is:</b> UTC<br>
            <b>Server Time is:</b> <br><?= date("Y-m-d H:i:s") ?>
            </td>

         </tr>
      </table>
   </div>
</div>