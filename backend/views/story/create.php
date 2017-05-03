<?php
use yii\web\View;
use backend\models\Country;
use backend\models\Story;
use backend\models\User;
use yii\helpers\Url;

$this->title=Yii::t('app', 'Add story');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/upload-file-field.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/create-story.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.charactercounter.js', array('position'  => View::POS_END));

$role=Yii::$app->user->getIdentity()->role;
?>
<script>
window.onbeforeunload = function () {
    return "Are you sure?";
};
</script>
<div class="block">
  <div class="block-header">
    <h2 class="block-title1"><?= Yii::t('app', 'Add story') ?></h2>
  </div>
  <div class="block-content">
    <div class="row items-push">
      <div class="col-sm-12">

        <div class="btn-group btn-group-justified">
           <div class="btn-group">
              <a class="btn <?=($model->scenario==Story::SCENARIO_IMAGE_STORY) ? "btn-primary" : "btn-info"?>" href="<?= Url::to(["/story/create", "story_type"=>Story::TYPE_IMAGE]) ?>"><?= Yii::t('app', 'Image story') ?></a>
           </div>
           <?php  if($role==User::ROLE_MARKETER || $role==User::ROLE_SUPERADMIN): ?>
           <div class="btn-group">
              <a class="btn  <?=($model->scenario==Story::SCENARIO_CLIPKIT_STORY) ? "btn-primary" : "btn-info"?>" href="<?= Url::to(["/story/create", "story_type"=>Story::TYPE_CLIPKIT]) ?>"><?= Yii::t('app', 'Clipkit story') ?></a>
           </div>
           <div class="btn-group">
              <a class="btn  <?=($model->scenario==Story::SCENARIO_VIDEO_STORY) ? "btn-primary" : "btn-info"?>" href="<?= Url::to(["/story/create", "story_type"=>Story::TYPE_VIDEO]) ?>"><?= Yii::t('app', 'Video story') ?></a>
           </div>
           <div class="btn-group">
              <a class="btn  <?=($model->scenario==Story::SCENARIO_3RD_PARTY_VIDEO_STORY) ? "btn-primary" : "btn-info"?>" href="<?= Url::to(["/story/create", "story_type"=>Story::TYPE_3RD_PARTY_VIDEO]) ?>"><?= Yii::t('app', '3rd Party Video') ?></a>
           </div>
           <?php endif ?>
        </div>

        <hr>

        <div id="createfilter">
        <?=$this->render("_form", array(
              'model'         => $model,
              'countries'     => $countries,
              'categories'    => $categories,
              'StoryKeyword'  => $StoryKeyword,
              'StoryClipkit' => $StoryClipkit,
              'SponsoredLevelTwo' => $SponsoredLevelTwo,
              'Story3rdPartyVideo'=>$Story3rdPartyVideo

          ))?>
        </div>

      </div>

    </div>
  </div>
</div>

<script>
$(function()
{
    createStoryModule.init({
        //wwCountryId : 0
    });
});
</script>
