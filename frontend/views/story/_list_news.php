<?php
use backend\components\Helpers as BackendHelpers;
use backend\models\Story;
use backend\models\SponsoredStory;
use backend\models\SponsoredLevelTwo;
use yii\helpers\Url;
use frontend\components\LinkGenerator;
use yii\web\View;

?>
<?php
$model->title = str_replace("'", "&#39;", $model->title);

//create url to single story
if(isset($_GET["page"]) && !isset($page))
    $page=$_GET["page"];

//id is id in Story
$params=['id'=>$model->id, 'seo_url'=>$model->seo_url, 'type'=>$type, 'page'=>$page, 'name'=>$name, 'categoryid'=>$categoryid];
$storyUrl=LinkGenerator::linkStoryView($model->title, $params, "html");
$storyUrl_full=LinkGenerator::linkStoryView(NULL, $params, "full");


//check if is single story (not category or subcategory page)
$singleStory = 0;
if(Yii::$app->controller->action->id=="view")
    $singleStory = 1;
?>

<?php if($model->type == Story::TYPE_VIDEO): ?>
    <?php  ob_start(); ?>
    var path_to_video, path_to_image;
    path_to_video="<?= BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_VIDEO, $model->video, false); ?>";
    //path_to_image="<?= BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE, $model->image, false); ?>";

    var playerInstance = jwplayer('myVideo_<?=$model->id?>');
    playerInstance.setup({
        file: path_to_video,
        /*advertising: {
            client: 'vast',
            tag: 'http://api-si.toboads.com/vast2/?zone_id=d7f0d9e50&format=300x250'
          }*/
    });
    <?php
    $script = ob_get_contents();
    ob_end_clean();
    ?>
    <?php $this->registerJs($script,  View::POS_END); ?>
<?php endif; ?>

<?php
if($model->sponsored_story==1 && $model->relationSponsoredStories->sponsored_type == SponsoredStory::SPONSORED_TYPE_IA)

    $urlTo = Url::to(['/sponsored/sponsored-level-two', 'id'=>$model->id]);
else {
    $urlTo = Url::to(['/story/external', 'id'=>$model->id]);
}

?>

<?php if($singleStory > 0) { ?>
<script>
$(document).ready(function(){
$( 'h1.post_title a' ).attr("href", "<?= $urlTo; ?>");
$( 'h1.post_title a' ).click(GoogleAnalyticsTitleClick);
});
</script>
<?php } ?>

<script>

</script>


<div class="column column_2_3" style="margin-left: 0;">
    <div class="row">
       <div class="post single" style="position:relative">
          <div style="clear:both;">
             <h1 id="web_title" class="post_title">
                <?=$storyUrl?>
             </h1>
             <div>
                <?php if(isset($url_plus_1) && isset($url_minus_1)): ?>
                <div id="previous"><a href="<?=$url_minus_1?>" style="display:block; width:100%; height:100%"><i class="fa fa-chevron-left" style="position:absolute; left:20%; top:25%;"></i>
                   </a>
                </div>
                <div id="next"><a href="<?=$url_plus_1?>" style="display:block; width:100%; height:100%"><i class="fa fa-chevron-right" style="position:absolute; right:20%; top:25%;"></i>
                   </a>
                </div>
                <?php endif; ?>

                    <?php if($model->type === Story::TYPE_IMAGE)
                    {
                    ?>
                        <img title="<?= $model->title ?>" alt="<?= $model->alt_tag ?>" src="<?= BackendHelpers::backendCDN().Story::getFullDirectoryToImageVideo($model->date_created, Story::PATH_IMAGE, $model->image, false)?>" class="img-responsive m-b-10 main-image" />
                    <?php
                    }
                    elseif($model->type === Story::TYPE_VIDEO)
                    {
                    ?>
                        <div style="width: 730px; height: 580px;" id='myVideo_<?=$model->id?>'>Loading the player ...</div>
                    <?php
                    }
                    elseif($model->type === Story::TYPE_CLIPKIT)
                    {
                        $this->registerJsFile('http://api.clipkit.de/embed/dist/clipkit-embed.js', array('id'=>'clipkit-embed','position'  => View::POS_END));
                        echo $model->relationStoryClipkit->clipkit_code;
                    }
                    elseif($model->type === Story::TYPE_3RD_PARTY_VIDEO)
                    {
                        echo $model->relationStory3rdPartyVideo->video_code;
                    }
                ?>
             </div>
             <h1 id="mobile_title" class="post_title">
                <?=$storyUrl?>
             </h1>
          </div>
          <div>
             <div class="mobpadding">
                <!--<h6 id="story">STOCKS</h6>-->
                <div class="row page_margin_top">
                   <div class="share_box clearfix">
                      <ul class="post_details">
                         <li class="date">
                            <?= $model->relationUser->name ?>
                         </li>
                         <li class="date">
                            <?= BackendHelpers::dateDifferenceStory(NULL,$model->date_published).' / '.$model->seo_title ?>
                         </li>
                      </ul>
                      <ul style="padding-top: 25px;" class="social_icons clearfix">
                        <!--<li> <div class="text-center" data-url="<?= $storyUrl_full ?>" data-title="<?= $model->title ?>"></div>
                        </li>-->

                        <li>

                        <div id="fb-root"></div>
                        <script>(function(d, s, id) {
                          var js, fjs = d.getElementsByTagName(s)[0];
                          if (d.getElementById(id)) return;
                          js = d.createElement(s); js.id = id;
                          js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
                          fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));</script>
                        <div class="fb-share-button" data-href="<?= $storyUrl_full ?>" data-layout="button"></div>

                        </li>

                        <li>

                        <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?= $storyUrl_full ?>" data-text="<?= $model->title ?>" data-count="none">Tweet</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

                        </li>

                        <li>

                        <?php $this->registerJsFile('//platform.linkedin.com/in.js'); ?>
                        <!--<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>-->
                        <script type="IN/Share" data-url="<?= $storyUrl_full ?>"></script>

                        </li>

                        <li style="cursor:pointer; background-color: #83ac40; border-radius: 3px; padding: 3px 13px;" onclick='shareEmail("<?= $model->title ?>", "<?= $storyUrl_full ?>")'>

                        Mail

                        </li>
                        <!-- <li>
                            <a target="_blank" title="" href="http://facebook.com/QuanticaLabs" class="social_icon facebook">
                            &nbsp;
                            </a>
                         </li>
                         <li>
                            <a target="_blank" title="" href="https://twitter.com/QuanticaLabs" class="social_icon twitter">
                            &nbsp;
                            </a>
                         </li>
                         <li>
                            <a title="" href="mailto:contact@pressroom.com" class="social_icon mail">
                            &nbsp;
                            </a>
                         </li>
                         <li>
                            <a title="" href="#" class="social_icon skype">
                            &nbsp;
                            </a>
                         </li>
                         <li>
                            <a title="" href="http://themeforest.net/user/QuanticaLabs?ref=QuanticaLabs" class="social_icon envato">
                            &nbsp;
                            </a>
                         </li>
                         <li>
                            <a title="" href="#" class="social_icon instagram">
                            &nbsp;
                            </a>
                         </li>
                         <li>
                            <a title="" href="#" class="social_icon pinterest">
                            &nbsp;
                            </a>
                         </li>-->
                      </ul>
                   </div>
                </div>
                <div class="post_content page_margin_top_section clearfix">
                   <div class="content_box">
                      <div class="text">
                         <p><?=$model->description ?> </p>
                      </div>
                   </div>
                </div>
             </div>
             <a style="text-align:center" onClick="GoogleAnalyticsClick('event', 'Read More', 'Click', 'Button')" class="read_more" target="_blank" href="<?= $urlTo; ?>" ><span><?= Yii::t('app', 'Read More') ?></span><span><i class="fa fa-circle"></i> <i class="fa fa-circle"></i>
             <i class="fa fa-circle"></i>
             </span></a>
          </div>
       </div>
    </div>
 </div>