<?php
use backend\models\User;
use backend\models\Story;
use backend\models\SponsoredLevelTwo;
use backend\models\SponsoredStory;
use yii\helpers\Html;
?>

<?php if ($role == User::ROLE_SUPERADMIN || $role == User::ROLE_ADMIN) : ?>
    <div class="row">
       <div class="col-sm-12">
            <div class="alert alert-danger">
                <h3 class="font-w300 push-15"><?= Yii::t('app', 'Admin tools')?></h3>
                <p>
                <?= $form->field($model, 'user_id', [
                   'template' => "{input}\n{hint}\n{error}",
                   'options'   => [
                   ]
                   ])->dropDownList(User::dropDownListAuthors(), array('required'=>'required', 'class'=>'form-control', 'id'=>'user_dependent' )); ?>
                </p>
            </div>
       </div>
   </div>
   <?php endif; ?>


   <?php //!!!!!!!!!!!!!!!!!!!!!!!!!!MARKETER AREA!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!?>
   <?php if ($role == User::ROLE_MARKETER) : ?>
   <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-minw btn-primary btn-block marketer-tools" type="button"><?= Yii::t('app', 'Marketer tools')?></button>
        </div>
    </div>
    <?php endif; ?>

   <?php if ($role == User::ROLE_MARKETER) : ?>
    <?php
    if($model->sponsored_story==1)
        $sponsoredStoryType=$model->relationSponsoredStories->sponsored_type;
    else
        $sponsoredStoryType=NULL;

    if($model->isNewRecord)
        $disabled=false;
    else
        $disabled=true;
    ?>
   <script>
   $(document).ready(function()
   {
        var sponsored_hide_div=$(".sponsored_div");
        var investor_acquisition_div=$(".investor_acquisition_div");
        var investor_acquisition=$(".investor_acquisition");
        var is_it_sponsored=$(".is_it_sponsored");
        var sponsored_story=<?php echo ($model->sponsored_story==NULL) ? 0 : $model->sponsored_story?>;
        //if this is sponsored story show divs
        if(sponsored_story==1)
        {
            //if this is sponsored sow div
            sponsored_hide_div.show();
            //show div for sponosred investor aqcuisition
            if(investor_acquisition.val()==<?=SponsoredStory::SPONSORED_TYPE_IA?>)
                investor_acquisition_div.show();
        }

        is_it_sponsored.change(function()
        {

            if($(this).val()==1)
                sponsored_hide_div.show();
            else
                sponsored_hide_div.hide();
        });

        investor_acquisition.change(function()
        {
            if($(this).val()==<?=SponsoredStory::SPONSORED_TYPE_IA?>)
                investor_acquisition_div.show();
            else
                investor_acquisition_div.hide();

        });
   });
   </script>
   <div class="row" id="marketer-tools-area" style="display:none;">
       <div class="col-sm-12">
            <div class="content-mini content-mini-full bg-primary-lighter">
                <h3 class="font-w300 push-15"><?= Yii::t('app', 'Marketer tools')?></h3>
                <p class="alert alert-danger">
                    <?= Yii::t('app', 'Sponsored story') ?>
                    <?= Html::dropDownList ('sponsored', $model->sponsored_story==1 ? 1 : 0, [0=>Yii::t('app', 'No'), 1=>Yii::t('app', 'Yes')], ['class'=>'form-control is_it_sponsored', 'disabled'=>$disabled] ) ?>
                </p>
                <div class="sponsored_div" style="display:none">
                    <p>
                        <?= Yii::t('app', 'Sponsored story') ?>
                        <?= Html::dropDownList ("sponsored_type", $sponsoredStoryType, SponsoredLevelTwo::sponsoredStoryType(), ['class'=>'form-control investor_acquisition', 'disabled'=>$disabled] )?>
                    </p>

                    <div class="investor_acquisition_div" style="display:none">
                        <p>
                            <?= Yii::t('app', 'Company name') ?>
                            <?= Html::activeTextInput($SponsoredLevelTwo, "company_name", ['class'=>'form-control']) ?>
                        </p>
                        <p>
                            <?= Yii::t('app', 'Title') ?>
                            <?= Html::activeTextInput($SponsoredLevelTwo, "title", ['class'=>'form-control']) ?>
                        </p>
                        <p>
                            <?= Yii::t('app', 'Stock quote') ?>
                            <?= Html::activeTextInput($SponsoredLevelTwo, "stock_quote", ['class'=>'form-control']) ?>
                        </p>
                        <p>
                            <?= Yii::t('app', 'Logo') ?>
                            <?= Html::fileInput ("logo", $value = null, $options = [] ) ?>
                            <?php if(!$SponsoredLevelTwo->isNewRecord)
                            {
                                echo '<img src="'.Story::getFullDirectoryToImageVideo($SponsoredLevelTwo->date_created, Story::PATH_IMAGE, $SponsoredLevelTwo->logo, false).'" class="img-responsive" style="max-width:250px">';
                            }
                            ?>
                        </p>
                        <hr>
                        <h2>Level 2</h2>
                        <p>
                            <?= Yii::t('app', 'Image') ?>
                            <?= Html::fileInput ("image_file", $value = null, $options = [] ) ?>
                            <?php if(!$SponsoredLevelTwo->isNewRecord)
                            {
                                echo '<img src="'.Story::getFullDirectoryToImageVideo($SponsoredLevelTwo->date_created, Story::PATH_IMAGE, $SponsoredLevelTwo->image_file, false).'" class="img-responsive" style="max-width:250px">';
                            }
                            ?>
                        </p>
                        <p>
                            <?= Yii::t('app', 'Caption') ?>
                            <?= Html::activeTextInput($SponsoredLevelTwo, "caption", ['class'=>'form-control']) ?>
                        </p>
                        <p><?php echo Yii::t("app", "Image position") ?><br>
                        <p><?php echo Html::activeRadioList ( $SponsoredLevelTwo, "image_position",
                        [SponsoredLevelTwo::IMAGE_POS_1=>Yii::t("app", "After paragraph")." 1",
                        SponsoredLevelTwo::IMAGE_POS_2=>Yii::t("app", "After paragraph")." 2",
                        SponsoredLevelTwo::IMAGE_POS_3=>Yii::t("app", "After paragraph")." 3"],
                        ['separator'=>'<br>']) ?>
                        </p>
                        <p>
                            <?= Yii::t('app', 'Paragraph') ?> 1
                            <?= Html::activeTextarea($SponsoredLevelTwo, "paragraph_one", ['class'=>'form-control', 'rows'=>"10"]) ?>
                        </p>
                        <p><?php //echo Html::activeRadio ( $SponsoredLevelTwo, "image_position", $options = ['uncheck'=>SponsoredLevelTwo::IMAGE_POS_2] ) ?> </p>
                        <p>
                            <?= Yii::t('app', 'Paragraph') ?> 2
                            <?= Html::activeTextarea($SponsoredLevelTwo, "paragraph_two", ['class'=>'form-control', 'rows'=>"10"]) ?>
                        </p>
                        <p><?php //echo Html::activeRadio( $SponsoredLevelTwo, "image_position", $options = ['uncheck'=>SponsoredLevelTwo::IMAGE_POS_3] ) ?>    </p>
                        <p>
                            <?= Yii::t('app', 'Paragraph') ?> 3
                            <?= Html::activeTextarea($SponsoredLevelTwo, "paragraph_three", ['class'=>'form-control', 'rows'=>"10"]) ?>
                        </p>
                        <h2>Level 3</h2>
                        <p>
                            Wufoo code
                            <?= Html::activeTextarea($SponsoredLevelTwo, "wufoo_code", ['class'=>'form-control', 'rows'=>"10"]) ?>
                        </p>
                    </div>
                </div>
            </div>
       </div>
   </div>
   <?php endif; ?>