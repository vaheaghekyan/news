<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;
use backend\models\Language;
use backend\models\Category;
use frontend\components\Helpers;
use yii\web\View;
use common\components\Helpers as CommonHelpers;
use backend\models\Story;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\StorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stories');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Yii::$app->request->baseUrl.'/extra/js/story.index.js', array('position'  => View::POS_END));
$lastRecord=end($dataProvider);
if($lastRecord != NULL) $lastRecordDatePublished = $lastRecord->date_published; else $lastRecordDatePublished = "";
$page=0; //slider is zero-based
?>

<?php
//mobile detect
$detect = new \Mobile_Detect;
    //check if visitor is coming from mobile
    if ($detect->isMobile())
    {
        //check if visitor is coming from iOS
        if($detect->isiOS()) { ?>

            <script>
                //try to open deeplink in browser (should redirect to app), if fails, open backup url = app in store
                var now = new Date().valueOf();
                setTimeout(function () {
                    if (new Date().valueOf() - now > 500) return;
                    //set backup url = app in store
                    window.location = "https://itunes.apple.com/en/app/born2invest/id1048044533";
                }, 50);
                //set deeplink to app
                window.location = "born2invest://";
            </script>

        <?php }
        //check if visitor is coming from android
        if($detect->isAndroidOS()) { ?>

            <script>
                //try to open deeplink in browser (should redirect to app), if fails, open backup url = app in store
                var now = new Date().valueOf();
                setTimeout(function () {
                    if (new Date().valueOf() - now > 500) return;
                    //set backup url = app in store
                    window.location = "https://play.google.com/store/apps/details?id=com.borntoinvest.borntoinvest";
                }, 50);
                //set deeplink to single app
                window.location = "b2i://borntoinvest";
            </script>

        <?php }
    }
?>

<script>
//config variables used in story.index.js
var url="<?= Helpers::frontendDomain() ?>";
var h2_tag="<?= Yii::t('app', 'Swipe left right to read story')?>";

//send user to specific slide (story) if they go back from story/view using arrows (left/right)
<?php if(isset($_GET["page"])): ?>
var goToSlide=<?=$_GET["page"]?>;
<?php else: ?>
var goToSlide=false;
<?php endif; ?>
</script>

<div class="column column_2_3" style="margin-left:0;">
    <ul class="bxslider">
        <?php foreach($dataProvider as $story): ?>
        <?php if($story->type == Story::TYPE_IMAGE && $story->image == NULL) continue; ?>
        <li>
        <?php echo $this->render("_list_news", ['model'=>$story, 'type'=>$type, 'name'=>$name, 'categoryid'=>$categoryid, 'page'=>$page]) ?>
        </li>
        <?php $page++ ?>
        <?php endforeach; ?>
        <li>
            <?= Html::beginForm('', 'post') ?>
            <?= Html::submitButton(mb_strtoupper(Yii::t('app', 'Load more stories'), 'UTF-8'), ['class'=>'btn btn-info btn-block text-center', 'style'=>'margin-top:250px;', 'onClick'=>"GoogleAnalyticsClick('event', 'Load More Stories', 'Click', 'Load More Stories')"])?>
            <?= Html::hiddenInput("date_published", $lastRecordDatePublished,  [] ) ?>
            <?= Html::endForm() ?>
        </li>
    </ul>
</div>

<?php
use backend\components\Helpers as BackendHelpers;
?>

<!-- Middle-carousel colum -->
<div class="carousel_column">
<?php $number = 0; foreach($dataProvider as $story): ?>
<div class="title<?= $number ?> carousel_title <?php if($number == 0 || $number > 7) echo 'not_show'; ?>" onclick="slider.goToSlide(<?= $number ?>)">
<?= $story->title ?>
<?php if($story->type === Story::TYPE_IMAGE && $story->image != NULL) { ?>
<img class="img<?= $number ?> <?php if(!in_array($number, array(3, 7))) echo 'not_show'; ?>" src="<?= BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE, $story->image, false)?>" alt="<?= $story->title ?>" title="<?= $story->title ?>" />
<?php } ?>
</div>
<?php $number++; endforeach; ?>
</div>