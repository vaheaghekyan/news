<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<script>
$(document).ready(function()
{
    var data_id, order_change_custom;
    $(".js_order_cat_tab").click(function()
    {
        //data-id so you know what group of elements you need to watch
        data_id=$(this).data("id");
        //take group of elements where you have to check for duplicates
        order_change_custom=$('.js_order_change_'+data_id);
        preventDuplicates(order_change_custom, data_id);
    });

    //this is default, parent category order change
    data_id=0;
    order_change_custom=$('.js_order_change_'+data_id);
    preventDuplicates(order_change_custom, data_id);
});

 //----------------PREVENTING DUPLICATED VALUES FOR ORDERING PARENT CATEGORY------------------------
 //for example: you have TOP STORIES=1, BUSINESS=5. When you change BUSINESS to 1, TOP STORIES will automatically be changed to 1
function preventDuplicates(order_change_custom, data_id)
{
    var previous_value, new_value;
    //when you click or focus on number input take its value (old value)
    order_change_custom.bind("focus click", function()
    {
        previous_value=$(this).val();
    });

    //when you change value to some input
    order_change_custom.change(function()
    {
        //get new value of element being changed
        new_value=$(this).val();

        //replace values, find old element that has this current value that is being entered and put old value of element being edited
        //make red background for changed element and then fade to white
        $('.js_order_change_'+data_id+'[value="'+new_value+'"]')
        .attr("value", previous_value)
        .val(previous_value)
        .css("background-color","red")
        .animate({backgroundColor:"white"},500);

        //change value of element being changed
        $(this).attr("value", new_value);

    });
}
</script>

<!--order parent category  -->
<?= Html::beginForm(Url::to(['/category/change-order']), 'post'); ?>
<div class="row order_category"  style="display:none">
    <div class="col-md-12">
        <div class="alert alert-warning"><?= Yii::t('app', 'Order index unique')?> </div>
        <!-- Block Tabs Default Style -->
        <div class="block">
            <ul class="nav nav-tabs" data-toggle="tabs">
                <li class="active">
                    <a href="#order-parent" class="js_order_cat_tab"  data-id="0"><?= Yii::t('app', 'Order parent') ?> </a>
                </li>
                <?php  foreach ( $categories as $category_key=>$category_value ) : ?>
                <li>
                    <a href="#<?= $category_value->id ?>" class="js_order_cat_tab" data-id="<?=$category_value->id?>">
                    <?= Yii::t('app', $category_value->name) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="block-content tab-content">
                <div class="tab-pane active" id="order-parent">
                    <h4 class="font-w300 push-15">
                        <!-- listing paretn categories for ordering -->
                     <?php  foreach ( $categories as $category_key=>$category_value ) : ?>
                         <?= Html::input('number', "parent_order_by[$category_value->id]", $category_value->order_by,
                         ['min'=>1, 'max'=>count($categories), 'class'=>"m-b-20 js_order_change_0", 'required'=>'required'] )?>
                         <?= Yii::t('app', $category_value->name)?>
                        <br>
                      <?php endforeach; ?>
                    </h4>
                </div>

                <?php  foreach ( $categories as $category_key=>$category_value ) : ?>
                    <div class="tab-pane" id="<?= $category_value->id ?>">
                     <!-- listing child ategories for ordering -->
                    <?php foreach ( $category_value->relationCategoriesLevelOne as $category_level_one_key=>$category_level_one_value): ?>
                        <h4 class="font-w300 push-15">
                        <?= Html::input('number', "level_one_order_by[$category_value->id][$category_level_one_value->id]", $category_level_one_value->order_by,
                            ['min'=>1, 'max'=>count($category_value->relationCategoriesLevelOne), 'class'=>"m-b-20 js_order_change_$category_value->id", 'required'=>'required'] )?>
                        <?= $category_level_one_value->name ?>
                        </h4>
                    <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="padding:10px">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class'=>'btn btn-minw btn-success', 'name'=>'submit_order']) ?>
            </div>
        </div>
        <!-- END Block Tabs Default Style -->
    </div>
</div>
 <?=  Html::endForm(); ?>