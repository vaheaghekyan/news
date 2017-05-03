<?php
use yii\web\View;
use backend\models\Country;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\Story;

$this->title="Update";

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/upload-file-field.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/create-story.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.charactercounter.js', array('position'  => View::POS_END));
$this->registerJsFile('http://content.jwplatform.com/libraries/YXCkOeBj.js', array('position'  => View::POS_END));

if($model->sponsored_story==1)
{
    $type_title=Yii::t('app', 'Sponsored');
}
else if($model->type==Story::TYPE_VIDEO)
{
    $type_title=Yii::t('app', 'Video');
}
else if($model->type==Story::TYPE_IMAGE)
{
    $type_title=Yii::t('app', 'Image');
}
else if($model->type==Story::TYPE_CLIPKIT)
{
    $type_title='Clipkit';
}
else if($model->type==Story::TYPE_3RD_PARTY_VIDEO)
{
    $type_title='3rd Party Video';
}


?>

<div class="block">
  <div class="block-header">
    <h2 class="block-title1">[<?=$type_title?>] <?= Yii::t('app', 'Edit story') ?></h2>
  </div>
  <div class="block-content">
    <div class="row items-push">
      <div class="col-sm-12">
        <!--<h3 class="push-15">Flexible Grid</h3>  -->
        <div id="createfilter">
            <?=$this->render("_form", array(
            'model'         => $model,
            'countries'     => $countries,
            'categories'    => $categories,
            'selectedCheckbox_category'=>$selectedCheckbox_category,
            'selectedCheckbox_countries'=>$selectedCheckbox_countries,
            'StoryKeyword' => $StoryKeyword,
            'StoryClipkit' => $StoryClipkit,
            'SponsoredLevelTwo'=>$SponsoredLevelTwo,
            'Story3rdPartyVideo'=>$Story3rdPartyVideo
            ))?>
        </div>
        <!-- if user saved story show option to publish or schedule -->
         <?php if ( $model->id ) : ?>

          <div class="col-sm-12 m-t-20">
            <b><?= Yii::t('app', 'Published on') ?>  </b>
            <?=($model->date_published ? $model->date_published : "---")?>
          </div>
          <div class="col-sm-12">
            <?php
            echo Html::beginForm('', 'post');
            echo Html::hiddenInput ("selection[]", $model->id, $options = [] );

             //if story is already published, don't let them publish it again
            if($model->status!=Story::STATUS_PUBLISHED)
            {
                echo "<div class='m-b-20 col-md-6'>";
                echo Html::submitButton ('<i class="fa fa-play"></i>&nbsp;'.Yii::t('app', 'Publish'), ['formaction'=>Url::to(['publish']), 'class'=>'btn btn-minw btn-success' ]);
                echo "</div>";
            }

            //if story is pending don't show button
            if($model->status!=Story::STATUS_UNPUBLISHED)
            {
                echo "<div class='m-b-20 col-md-6'>";
                echo Html::submitButton ('<i class="fa fa-pause"></i>&nbsp;'.Yii::t('app', 'Unpublish'), ['formaction'=>Url::to(['unpublish']), 'class'=>'btn btn-minw btn-warning' ]);
                echo "</div>";
            }

           /* echo "<div class='col-md-6'>";
            echo Html::submitButton ('<i class="fa fa-calendar"></i>&nbsp;'.Yii::t('app', 'Schedule'), ['formaction'=>Url::to(['schedule-publish']), 'class'=>'btn btn-minw btn-primary'] );
            echo "\t".Html::activeTextInput(new Story, 'date_published', ['class'=>'datetimepicker form-control-static']);
            echo "</div>";  */
            echo Html::endForm();
            ?>
            <div class='m-b-20 col-md-6'>
            <a href="<?= Url::to(['/story/delete', 'id'=>$model->id]) ?>" title="Delete" aria-label="Delete" data-confirm="Are you sure you want to delete this item?" data-method="post" class="btn btn-minw btn-danger"><?=Yii::t('app', 'Delete')?></a>
            </div>

          </div>

        <?php endif;?>

      </div>

    </div>
  </div>
</div>



<script>

    $(function(){
        createStoryModule.init({
            //wwCountryId             : 0 <?php //$countryWw->id?>,
            storyId                 : <?=$model->id?>,
            datePublished           : '<?=$model->date_published?>',
            storyPublishUrl         : '<?=Url::to(['story/publish'])?>',
            storySchedulePublishUrl : '<?=Url::to(['story/schedule-publish'])?>',
            mode                    : '<?=$mode?>'

        });
    });


</script>