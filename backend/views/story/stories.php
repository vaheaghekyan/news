<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use backend\models\Story;
use backend\models\User;
use backend\models\Category;
use backend\models\Country;
use backend\components\LinkGenerator;
use frontend\components\LinkGenerator as FrontendLinkGenerator;
use backend\components\Helpers;
use frontend\components\Helpers as FrontendHelpers;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\StorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile('http://content.jwplatform.com/libraries/YXCkOeBj.js', array('position'  => View::POS_END));

if($status==Story::STATUS_UNPUBLISHED)
    $title=Yii::t('app', 'Deleted stories');
else if($status==Story::STATUS_PUBLISHED)
    $title=Yii::t('app', 'Published stories');
else if($status==Story::STATUS_PENDING)
    $title=Yii::t('app', 'Pending stories');

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;

$dropDownCountriesStoryDepending=Country::dropDownCountriesStoryDepending();
$dropDownListCategories=Category::dropDownListCategories();

$action=Yii::$app->controller->action->id;
/*
* used to show checkbox column and submit buttons for schedule, pblish and unpublish
*/
function visibleSubmitElements($action)
{
    if($action=="pending" || $action=="unpublished" || $action=="published" || $action=="sponsored")
        return true;
    else
        return false;
}


?>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<style>
td
{
    max-width:150px;
}
</style>
<script>
$(document).ready(function()
{
    /*$(".show_schedule_btn").click(function()
    {
        var storyid=$(this).data("storyid");
        $(".hide_schedule_btn_"+storyid).toggle();
    });*/

    //when you click on small red/green/blue buttons on the right side table
    $(".one_row_publish").click(function(e)
    {
        e.preventDefault();
        var action, formaction, storyid, mainForm;
        //get story id from "data"
        storyid=$(this).data("storyid");
        //get form action
        formaction=$(this).data("formaction");
        //create url for form
        if(formaction=="publish")
            action="<?= Url::to(["/story/publish"]) ?>";
        else if(formaction=="unpublish")
            action="<?= Url::to(["/story/unpublish"]) ?>";
       /* else if(formaction=="schedule-publish")
        {
            action="<?= Url::to(["/story/schedule-publish"]) ?>";
            //find value of date_published input being submitted
            var date_published=$(".hide_schedule_btn_"+storyid).find(".date_published").val();
            //set this date to every date_published input because you might get: Select date before publishing
            $(".date_published").prop("value",date_published);
        } */

        //uncheck all checkboxes so you don't submit everything checked
        $('input:checkbox').attr('checked',false);

        //check this checkbox
        $("tr[data-key="+storyid+"]").find(".CheckboxColumn").prop("checked", true);
        //click on button to submit
        mainForm = $("#mainForm");
        mainForm.prop('action', action);
        mainForm.submit();
    });
});

//show modal popup for story
function storyPreview(story_id)
{

    $.ajax(
    {
        url:'<?= Url::to(['/story/view']) ?>',
        type:"POST",
        data: {story_id:story_id},
        dataType:"json",
        success:function(data)
        {
            bootboxPreviewStory(data.result);
        }
    });
}

//show search grid
function showSearch()
{
    $(".story-search").toggle();
}
</script>
<div class="story-index row">
  <div class="col-lg-12">
    <!-- Header BG Table -->
    <div class="block">
      <div class="block-header">

        <h3 class="block-title">
          <?= Html::encode($this->title) ?>
          </h3>
      </div>
      <div class="block-content">
        <p>
          <?= Html::a(Yii::t('app', 'Create Story'), ['create'], ['class' => 'btn btn-success']) ?>
           <?= Html::a(Yii::t('app', 'Reset'), [Yii::$app->controller->action->id ], ['class' => 'btn btn-warning']) ?>
           <?php echo Html::a(Yii::t('app', 'Search'), null, ['class' => 'btn btn-primary', 'onClick'=>'showSearch()']) ?>
           <?php echo $this->render('_search', ['model' => $searchModel, 'dropDownCountriesStoryDepending'=>$dropDownCountriesStoryDepending, 'dropDownListCategories'=>$dropDownListCategories]); ?>
          </p>
        <div class="table-responsive">
            <?= Html::beginForm('', 'post', ['id'=>'mainForm'])?>
          <?= GridView::widget([
                  'dataProvider' => $dataProvider,
                  'filterModel' => $searchModel,
                  'tableOptions'=>['class'=>'table table-striped table-borderless table-header-bg'],
                  'columns' =>
                  [
                      //['class' => 'yii\grid\SerialColumn'],

                      //'id',
                     // 'language_id',
                     // 'seo_title',
                     // 'description:ntext',

                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'visible'=>visibleSubmitElements($action),
                            'checkboxOptions'=>['class'=>'CheckboxColumn']
                            // you may configure additional properties here
                        ],
                        [
                            'attribute'=>'id',
                            'format'=>'raw',
                            'value'=>function($data)
                            {

                                if(isset($data->relationCategoryStories[0]))
                                {
                                    $catName=$data->relationCategoryStories[0]->relationCategory->name;
                                    $catId=$data->relationCategoryStories[0]->relationCategory->id;
                                }
                                else
                                {
                                    $catName="none";
                                    $catId=0;
                                }

                                $url_params=[
                                "seo_url"=>$data->seo_url,
                                'type'=>'subcategory',
                                'name'=>FrontendHelpers::generateSubcategoryName($catName),
                                'id'=>$data->id,
                                'page'=>0,
                                'categoryid'=>$catId,
                                ];
                                $link=FrontendLinkGenerator::linkStoryView($data->id, $url_params, "full");
                                return Html::a($data->id, $link, ['class'=>'btn btn-primary btn-xs', 'target'=>'_blank'])." ($data->id)";
                            }
                        ],
                        [
                            'attribute'=>'title',
                        ],
                        [
                            'attribute'=>'filter_country',
                            'label'=>Yii::t('app','Country'),
                            'format'=>'html',
                            'value'=>function($data)
                            {
                                $return=NULL;
                                foreach( $data->relationCountryStories as $value)
                                {
                                    $return.=$value->relationCountry->name."<br>";
                                }
                                return $return;
                            },
                            'filter'=>Html::activeDropDownList($searchModel,'filter_country', $dropDownCountriesStoryDepending, ['class'=>'form-control', 'prompt'=>''])
                        ],
                        [
                            'label'=>Yii::t('app','Category'),
                            'attribute'=>'filter_category',
                            'format'=>'raw',
                            'value'=>function($data)
                            {

                                return Category::listCategories($data);

                            },
                           'filter'=>Html::activeDropDownList($searchModel, "filter_category", $dropDownListCategories, ['class'=>'form-control', 'prompt'=>''] )
                        ],
                        [
                            'attribute'=>'link',
                            'format'=>'raw',
                            'value'=>function($data)
                            {
                                return
                                Html::a($data->seo_title, $data->link, ['class'=>'btn btn-sm btn-primary btn_link_to_story', 'target'=>'_blank']);
                            }
                        ],

                        [    'attribute'=>'filter_author',
                            'label'=>Yii::t('app','Author'),
                            'value'=>function($data)
                            {
                                return $data->relationUser->name;
                            }
                        ],

                        [
                            'attribute'=>'date_created',
                            'value'=>function($data) use ($Formatter)
                            {
                                //return $Formatter->asDateTime($data->date_created);
                                return $data->date_created;
                            },
                            'filter'=>Html::activeTextInput ($searchModel, 'date_created',  ['class'=>'datepicker form-control'] )
                        ],
                        [
                            'attribute'=>'date_published',
                            'value'=>function($data) use ($Formatter)
                            {
                                if(empty($data->date_published))
                                    return "-";
                                else
                                    return $data->date_published;
                                    //return $Formatter->asDateTime($data->date_published);
                            },
                            'visible'=>Helpers::columnVisible([User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_SENIOREDITOR, User::ROLE_MARKETER]),
                            'filter'=>Html::activeTextInput ($searchModel, 'date_published',  ['class'=>'datepicker form-control'] )
                        ],
                        /*[
                            'label'=>Yii::t('app','Media'),
                            'format'=>'raw',
                            'value'=>function($data)
                            {
                                $image=$video=NULL;

                                if($data->image!=NULL)
                                    $image=LinkGenerator::linkImage($data->id);

                                if($data->video!=NULL)
                                    $video=LinkGenerator::linkVideo($data->id);

                                return $image." ".$video;
                            },
                            'filter'=>Html::activeDropDownList($searchModel, "filter_media", Story::dropDownFilterMedia(), ['class'=>'form-control'] )
                        ],*/
                        [
                            'label'=>Yii::t('app','Type'),
                            'format'=>'raw',
                            'value'=>function($data)
                            {
                                $type=NULL;

                                if($data->type==Story::TYPE_IMAGE)
                                {
                                    $type.=Yii::t("app", "Image");
                                }
                                elseif($data->type==Story::TYPE_VIDEO)
                                {
                                    $type.=Yii::t("app", "Video");
                                }
                                elseif($data->type==Story::TYPE_CLIPKIT)
                                {
                                    $type.=Yii::t("app", "Clipkit");
                                }

                                return $type;
                            }
                        ],
                        [
                            'label'=>'',
                            'format'=>'raw',
                            'value'=>function($data)
                            {
                                return
                                Html::button('<i class="fa fa-play"></i>', ['class'=>'btn btn-success btn-xs one_row_publish m-b-10', 'data-storyid'=>$data->id, 'data-formaction'=>'publish' ])."&nbsp;".
                                Html::button('<i class="fa fa-pause"></i>', ['class'=>'btn btn-danger btn-xs one_row_publish  m-b-10', 'data-storyid'=>$data->id, 'data-formaction'=>'unpublish' ])."&nbsp;".
                               // Html::button('<i class="fa fa-calendar"></i>', ['class'=>'btn btn-primary btn-xs show_schedule_btn', 'data-storyid'=>$data->id, 'data-formaction'=>'schedule' ])."&nbsp;".

                                //"<div class='hide_schedule_btn_".$data->id."' style='display:none'>".
                               // Html::submitButton ('<i class="fa fa-calendar"></i>&nbsp;'.Yii::t('app', 'Schedule'), ['class'=>'btn btn-minw btn-primary one_row_publish',  'data-storyid'=>$data->id, 'data-formaction'=>'schedule-publish'] ).
                                //Html::activeTextInput(new Story, 'date_published', ['class'=>'datetimepicker date_published form-control-static']).
                                "</div>";
                            },
                            'visible'=>visibleSubmitElements($action)
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'buttons'=>
                            [
                                'view' => function ($url, $model, $key)
                                {
                                    return '<a href="javascript:;" title="View" onClick="storyPreview('.$model->id.')"><span class="glyphicon glyphicon-eye-open"></span></a>';
                                },
                            ]
                        ],
                  ],
              ]); ?>
              <p>
              <b style="color:red">Use little green (play) button in last column near each story to publish it</b>
              <?php
              if(visibleSubmitElements($action)==true)
              {
                /*echo "<div class='m-b-20'>";
                echo Html::submitButton ('<i class="fa fa-play"></i>&nbsp;'.Yii::t('app', 'Publish'), $options = ['formaction'=>Url::to(['publish']), 'class'=>'btn btn-minw btn-success' ]);
                echo "</div>";*/

                echo "<div class='m-b-20'>";
                echo Html::submitButton ('<i class="fa fa-pause"></i>&nbsp;'.Yii::t('app', 'Unpublish'), $options = ['formaction'=>Url::to(['unpublish']), 'class'=>'btn btn-minw btn-danger'] );
                echo "</div>";

                echo "<div>";
                echo Html::submitButton ('<i class="fa fa-calendar"></i>&nbsp;'.Yii::t('app', 'Schedule'), $options = ['formaction'=>Url::to(['schedule-publish']), 'class'=>'btn btn-minw btn-primary'] );
                echo "\t".Html::activeTextInput(new Story, 'date_published', ['class'=>'datetimepicker date_published form-control-static']);
                echo "<br>
                        <b>Server Time is:</b> UTC<br>
                        <b>Server Time is:</b> <br>".date("Y-m-d H:i:s");
                echo "</div>";
              }
              ?>
              <?= Html::endForm() ?>
              </p>
          </div>
      </div>
    </div>
    <!-- END Header BG Table -->
  </div>
</div>
