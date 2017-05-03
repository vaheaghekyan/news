<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Story;
use backend\models\CountryStory;
use backend\models\Country;
use yii\helpers\Url;

use backend\models\Language;
use backend\models\Category;
use backend\models\CategoriesLevelOne;


$CategoriesLevelOne = new CategoriesLevelOne;
$Category = new Category;
$Country = new Country;

if($model->isNewRecord)
{
    $selectedCheckbox_category_tmp=$Category->always_checked; //always check "Trending"
    $selectedCheckbox_country_tmp=$Country->always_checked;//always check "International"
}
//update, check checbox with saved values
else
{
    //with the help of ArrayHelper create array so you can add that in checkboxlist, key and value are the same, even though 2nd parameter of checkboxList takes array of values to select checbox, keys are not important
    $selectedCheckbox_category_tmp=ArrayHelper::map($selectedCheckbox_category, 'category_id', 'category_id');
    $selectedCheckbox_country_tmp=ArrayHelper::map($selectedCheckbox_countries, 'country_id', 'country_id');
}

$role= Yii::$app->user->getIdentity()->role;
$action = Yii::$app->controller->action->id;
?>



<style>
.label-cstm {
	display:block;
	text-align:left;
	font-size:12px;
}
.published-row
{
    height:50px;
}
.auto-save-alert
{
    max-width:150px;
    max-height:100px;
    position:fixed;
    right: 10px;
    bottom: 10px;
    display:none;
    z-index:1000;
}
</style>

<script>
//on submit, check if at least only one checkbox is checked
function onSubmitForm()
{
    //if user didn't check any category, put a warning
    if($(".container_select_category input[type=checkbox]:checked").length==0)
    {
        sweetAlert(":(","<?= Yii::t('app', 'Select at least one category') ?>", "error");
        return false;
    }

    if($(".container_select_country input[type=checkbox]:checked").length==0)
    {
        sweetAlert(":(","<?= Yii::t('app', 'Select at least one country') ?>", "error");
        return false;
    }
}

$(document).ready(function()
{
    $(".count_title").characterCounter({
      limit: '<?= Story::COUNT_TITLE ?>'
    });

    $(".count_description").characterCounter({
      limit: '<?= Story::COUNT_DESCRIPTION ?>'
    });

    $(".marketer-tools").click(function()
    {
        $("#marketer-tools-area").toggle();
    });

});


//auto save story
var autoSaveStory=setInterval(function(){ autoSave(); }, 20000); //20 seconds

function autoSave()
{
    var autoSaveUrl="<?=Url::to(["/story/auto-save"])?>";
    var postData=$("#create-event-form").serializeArray();
    postData[postData.length] = { name: "ID", value: <?=($model->isNewRecord) ? -1 : $model->id?> };
    //console.log(postData);
    $.ajax(
    {
        url :autoSaveUrl,
        type: "POST",
        data : postData,
        dataType : "JSON",
        success:function(data, textStatus, jqXHR)
        {
            if(data.return_data==true)
                $(".auto-save-alert").fadeIn(3000).fadeOut(2000);


        },
    });
}
</script>

<div class="alert alert-success auto-save-alert">
<b>Auto Saved!</b>
</div>

<?php
if($action=="create")
    $form_action=Url::to(['/story/create']);
else if($action=="update")
    $form_action=Url::to(['/story/update', 'id'=>$model->id]);

$form = ActiveForm::begin([
    'id' => 'create-event-form',
    'action'=>$form_action,
    'options' => ['enctype' => 'multipart/form-data', 'onSubmit'=>'return onSubmitForm()'],
    /*'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],*/
]);
/* @var $model \app\models\Story */
?>

<!-- STORY INFO -->
<?php require "_form/snippet.php"; ?>
<hr>
<!-- MEDIA -->
<?php
if($model->scenario==Story::SCENARIO_IMAGE_STORY)
{
    require "_form/image.php";
}
elseif($model->scenario==Story::SCENARIO_VIDEO_STORY)
{
    require "_form/image.php";
    require "_form/video.php";
}
elseif($model->scenario==Story::SCENARIO_CLIPKIT_STORY)
{
    require "_form/clipkit.php";
}
elseif($model->scenario==Story::SCENARIO_3RD_PARTY_VIDEO_STORY)
{
    require "_form/3rd_party_video.php";
}
else
    require "_form/image.php";

?>

<?php require "_form/admin_marketer_area.php"; ?>

<div class="col-sm-12"><hr></div>

<!--CATEGORY -->
<?php require "_form/categories.php"; ?>
<?php require "_form/finish_preview.php"; ?>

<!--COUNTRIES-->
<?php require "_form/countries.php"; ?>

<?php ActiveForm::end(); ?>
<?php

    $this->registerCssFile(Yii::$app->request->baseUrl.'/css/cropper.min.css');
    $this->registerJsFile('/js/modal.bootstrap.js');
    $this->registerJsFile('/js/cropper.min.js');
    $this->registerJsFile('/js/expander.js');
    $this->registerJsFile('/js/jquery.field-size.js');
?>
<script>
    $(function(){
        fieldSizeModule.init();
        $(".expander").expander();
    });
</script>
