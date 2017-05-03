<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use backend\models\Language;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Translations');
$this->params['breadcrumbs'][] = $this->title;

$action=Yii::$app->controller->action->id;
if($action=="index")
{
    $submit_action=Url::to(['/settings/translation/update', 'type'=>$type]);
    $current_version="CMS";
}
else if($action=="index-web")
{
    $submit_action=Url::to(['/settings/translation/update-web', 'type'=>$type]);
    $current_version="Web App";
}
else if($action=="index-android")
{
    $submit_action=Url::to(['/settings/translation/update-android', 'type'=>$type]);
    $action_generate_file=Url::to(["/settings/translation/generate-android"]);
    $current_version="Android";
}
else if($action=="index-ios")
{
    $submit_action=Url::to(['/settings/translation/update-ios', 'type'=>$type]);
    $action_generate_file=Url::to(["/settings/translation/generate-ios"]);
    $current_version="iOS";
}  
?>

<div class="settings-social-networks-index row">
  <div class="col-sm-12">
    <div class="block">
      <div class="block-header">
        <h2><?= Html::encode($this->title )?> | <?= $current_language->name ?> | <?= $current_version ?></h2>
      </div>
      <div class="block-content m-b-20">
        <a href="<?= Url::to(['/settings/translation/index-android', 'type'=>'new'])?>" class="btn btn-minw btn-success col-sm-3">
            <i class="fa fa-android pull-left"></i>
            <?= Yii::t('app', 'Translate Android') ?>
        </a>
        <a href="<?= Url::to(['/settings/translation/index-ios', 'type'=>'new'])?>" class="btn btn-minw btn-success col-sm-3">
            <i class="fa fa-apple pull-left"></i>
            <?= Yii::t('app', 'Translate iOS') ?>
        </a>
        <a href="<?= Url::to(['/settings/translation/index', 'type'=>'new'])?>" class="btn btn-minw btn-success  col-sm-3">
            <i class="fa fa-cloud pull-left"></i>
            <?= Yii::t('app', 'Translate CMS') ?>
        </a>
        <a href="<?= Url::to(['/settings/translation/index-web', 'type'=>'new'])?>" class="btn btn-minw btn-success  col-sm-3">
            <i class="fa fa-cloud pull-left"></i>
            <?= Yii::t('app', 'Translate Web App') ?>
        </a>
        <div style="clear:both">&nbsp;</div>

        <?php if($type=="old"): ?>
        <a href="<?= Url::to(["/settings/translation/$action", 'type'=>'new'])?>" class="btn btn-minw btn-primary btn-block">
            <?= Yii::t('app', 'Edit new words') ?>
        </a>
        <?php endif; ?>
        <?php if($type=="new"): ?>
        <a href="<?= Url::to(["/settings/translation/$action", 'type'=>'old'])?>" class="btn btn-minw btn-danger btn-block">
            <?= Yii::t('app', 'Edit all words') ?>
        </a>
        <?php endif; ?>

        <?php if(Yii::$app->user->getId()==\Yii::$app->params['adminId'] && $action!="index" && $action!="index-web"): ?>
            <div class="content-mini content-mini-full bg-primary-light m-t-20" style="border:3px dashed blue;">
            <?= Html::beginForm($action_generate_file, "get") ?>
            <?= Html::submitButton(Yii::t('app', 'Generate Lang File'), ['class'=>'btn btn-minw btn-warning'])?>
            <?= Html::dropDownList("lang", null, Language::dropDownActiveLanguages(), ['class'=>'form-control', 'style'=>'max-width:200px; display:inline']) ?>
            <?= Html::endForm() ?>
            </div>
        <?php endif; ?>
        <div class="alert alert-info m-t-20">
            <h3 class="font-w300 push-15">Information</h3>
            <p><?= Yii::t('app', 'Save translations info')?></p>
        </div>

      </div>

      <ul class="nav nav-tabs" data-toggle="tabs">
        <?php
        $i=0;
        foreach($string as $file_name=>$file_array): //$file_name=categories.php
        $file_name_temp=explode(".", $file_name);
        ?>
            <li class="<?= $i==0 ? "active" : NULL ?>"> <a href="#<?= $file_name_temp[0] ?>"><?= strtoupper($file_name_temp[0]) ?></a> </li>
        <?php
        $i++;
        endforeach;
        ?>
      </ul>
      <?php echo Html::beginForm($submit_action, 'post'); ?>
      <div class="block-content tab-content">
        <?php
        $i=0;
        foreach($string as $file_name=>$file_array) //$file_name=categories.php
        {

            $file_name_temp=explode(".", $file_name);
            echo '<div class="tab-pane '.($i==0 ? "active" : NULL).'" id="'.$file_name_temp[0].'">';

            foreach($file_array as $string_key=>$string_value) //$string_key=TOP STORIES, $string_value=TOP PRIÈE
            {
                if(strlen($string_value)>300)
                {
                    echo '<div class="col-sm-12 col-md-12 col-lg-12">';
                    echo "<b>".$en_word[$string_key]."</b>";
                    echo Html::textArea ("translate[$file_name][$string_key]", $string_value, ['class'=>'form-control', 'required'=>'required', 'rows'=>10] );
                    echo '</div>';
                }
                else
                {
                    echo '<div class="col-sm-12 col-md-4 col-lg-3 matchheight">';
                    echo '
                    <div class="block block-themed block-rounded">
                        <div class="block-header bg-smooth-dark">
                            <h3 class="block-title" style="text-transform:none;">'.$en_word[$string_key].'</h3>
                        </div>
                        <div class="block-content">
                            '.Html::textInput ("translate[$file_name][$string_key]", $string_value, ['class'=>'form-control', 'required'=>'required'] ).'
                        </div>
                    </div>';

                    echo '</div>';
                }


            }
            echo '</div>';
            $i++;
        }

        ?>
      </div>
      <?php
        echo Html::submitButton(Yii::t('app','Submit'), ['class'=>'btn btn-minw btn-danger', "name"=>"submit_translation", 'style'=>'position:fixed; bottom:40px; right:40px;']);
        echo Html::endForm();
        ?>
    </div>
  </div>
</div>
