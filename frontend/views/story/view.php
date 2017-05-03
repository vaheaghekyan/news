<?php
use yii\web\View;
use yii\helpers\Url;
use frontend\components\LinkGenerator;
use frontend\models\search\StorySearch;

//mobile detect
$detect = new \Mobile_Detect;
    //check if visitor is coming from mobile
    if ($detect->isMobile())
    {
        //check if visitor is coming from iOS
        if($detect->isiOS()) {

//Facebook and Twitter deeplink is not working, so if opened from Facebook or Twitter, redirect to web version
//if(strpos($_SERVER['HTTP_USER_AGENT'], 'Facebook/') !== true && strpos($_SERVER['HTTP_USER_AGENT'], 'twitter') !== true) {

        ?>

            <script>
                //try to open deeplink in browser (should redirect to app), if fails, open backup url = app in store
                var now = new Date().valueOf();
                setTimeout(function () {
                    if (new Date().valueOf() - now > 500) return;
                    //set backup url = app in store
                    window.location = "https://itunes.apple.com/en/app/born2invest/id1048044533";
                }, 50);
                //set deeplink to single story
                window.location = "born2invest://<?php echo $model->id; ?>";
            </script>

        <?php //} else {}

        }

        //check if visitor is coming from android
        if($detect->isAndroidOS()) {

//LinkedIn deeplink is not working, so if opened from LinkedIn, redirect to web version
if($_SERVER['HTTP_X_REQUESTED_WITH'] != "com.linkedin.android") {
?>

            <script>
                //try to open deeplink in browser (should redirect to app), if fails, open backup url = app in store
                var now = new Date().valueOf();
                setTimeout(function () {
                    if (new Date().valueOf() - now > 500) return;
                    //set backup url = app in store
                    window.location = "https://play.google.com/store/apps/details?id=com.borntoinvest.borntoinvest";
                }, 50);
                //set deeplink to single story
                window.location = "b2i://borntoinvest?storyId=<?php echo $model->id; ?>";
            </script>

 <?php  } else {}
    }
    }

$this->title=$model->title;
$this->registerJsFile(Yii::$app->request->baseUrl.'/extra/js/story.view.js', array('position'  => View::POS_END));

//check if page=0, because you cannot go to page 0
if($page==0)
    $page_minus_1=$page;
else
    $page_minus_1=$page-1;

//check if page is 100, because this is limit per session
if($page==StorySearch::LIMIT_STORY)
    $page_plus_1=$page;
else
    $page_plus_1=$page+1;

//next page
// if this is empty, that means this is last story and you cannot go further
if(!empty($dataProvider))
{
    $params=['id'=>$dataProvider[0]->id, 'seo_url'=>$dataProvider[0]->seo_url, 'type'=>$type, 'page'=>$page, 'name'=>$name, 'categoryid'=>$categoryid];
    $storyUrl_full=LinkGenerator::linkStoryView(NULL, $params, "full");
}
else
    $storyUrl_full="javascript:;";
//$url_plus_1=LinkGenerator::linkStoryIndex(NULL, ['categoryid'=>$categoryid, 'type'=>$type, 'name'=>$name, 'page'=>$page_plus_1], "short");
$url_plus_1=$storyUrl_full;
//previous page
$url_minus_1=LinkGenerator::linkStoryIndex(NULL, ['categoryid'=>$categoryid, 'type'=>$type, 'name'=>$name, 'page'=>$page_minus_1], "short");
?>
<div class="column column_2_3">
<?php
echo $this->render('_list_news',
[
    'model' => $model,
    'type' => $type,
    'name' => $name,
    'categoryid' => $categoryid,
    'url_plus_1'=>$url_plus_1,
    'url_minus_1'=>$url_minus_1
]);
?>
</div>

<?php
use backend\components\Helpers as BackendHelpers;
use backend\models\Story;
?>

<!-- Middle-carousel colum -->
<div class="carousel_column">
<?php $number = 1; foreach($dataProvider as $story): ?>
<div class="title<?= $number ?> carousel_title <?php if($number > 7) echo 'not_show'; ?>" onclick="slider.goToSlide(<?= $number ?>)">
<?php
$params=['id'=>$story->id, 'seo_url'=>$story->seo_url, 'type'=>$type, 'page'=>$page, 'name'=>$name, 'categoryid'=>$categoryid];
$storyUrl_full=LinkGenerator::linkStoryView(NULL, $params, "full");
?>
<a style="color: black;" href="<?= $storyUrl_full ?>">
<?= $story->title ?>
<?php if($story->type === Story::TYPE_IMAGE && $story->image != NULL) { ?>
<img class="img<?= $number ?> <?php if(!in_array($number, array(3, 7))) echo 'not_show'; ?>" src="<?= BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($story->date_created, Story::PATH_IMAGE, $story->image, false)?>" alt="<?= $story->title ?>" title="<?= $story->title ?>" />
</a>
<?php } ?>
</div>
<?php $number++; endforeach; ?>
</div>