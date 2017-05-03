<?php
use backend\models\Story;
use backend\components\Helpers;
use backend\models\User;
use yii\helpers\Html;
?>

<div id="category" class="gpad">
    <div class="row">
        <div class="col-sm-12">
          <?= Yii::t('app', 'Title') ?>
          <small>(<?= Yii::t('app', 'Use sentence case') ?>)</small>
          <?= $form->field($model, 'title', [
             'template' => "{input}\n{hint}\n{error}",
             'options'   => [
             ]
             ])->textInput( array( 'required'=>'required','class'=>'count_title form-control', 'maxlength'=>Story::COUNT_TITLE, 'value'=>Helpers::storySession("echo", $model, "story_title_") ) ) ?>
       </div>
   </div>
   <div class="row">
       <div class="col-sm-12">
          <?= Yii::t('app', 'Original Source') ?>
          <small>(<?= Yii::t('app', 'Name of publication or news organization') ?>)</small>
          <?= $form->field($model, 'seo_title', [
             'template' => "{input}\n{hint}\n{error}",
             'options'   => [
             ]
             ])->textInput(array('required'=>'required', 'value'=>Helpers::storySession("echo", $model, "story_seo_title_") )) ?>
       </div>
   </div>

   <div class="row">
       <div class="col-sm-12">
          <?= Yii::t('app', 'Source Link') ?>
          <small>(<?= Yii::t('app', 'URL or original source') ?>)</small>
          <?= $form->field($model, 'link', [
             'template' => "{input}\n{hint}\n{error}",
             'options'   => [
             ]
             ])->textInput(array('required'=>'required', 'value'=>Helpers::storySession("echo", $model, "story_source_link_") )) ?>
       </div>
   </div>

   <div class="row">
       <div class="col-sm-12">
          <?= Yii::t('app', 'Summary') ?>
          <small>(<?= Yii::t('app', 'Use 80 words or less') ?>)</small>
          <?= $form->field($model, 'description', [
             'template' => "{input}\n{hint}\n{error}",
             'options'   => [
             ]
             ])->textArea(array('required'=>'required', 'rows'=>6, 'class'=>'count_description form-control', 'maxlength'=>Story::COUNT_DESCRIPTION, 'value'=>Helpers::storySession("echo", $model, "story_summary_") )) ?>
       </div>
   </div>

   <div class="row">
       <div class="col-sm-12 m-b-20">
          <?= Yii::t('app', 'Keywords') ?>
          <small>(i.e. obama, president, usa)</small>
          <?= $form->field($StoryKeyword, 'keywords', [
             'template' => "{input}\n{hint}\n{error}",
             'options'   => [
             ]
             ])->textInput(array('required'=>'required', 'value'=>Helpers::storySession("echo", $model, "story_keyword_") )) ?>
       </div>
   </div>
</div>