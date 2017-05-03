<?php
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\User;
$this->title=Yii::t('app', 'Categories');

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery-ui.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/category.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/modal.bootstrap.js', array('position'  => View::POS_END));

$role=Yii::$app->user->getIdentity()->role;
?>
<script>
$(document).ready(function()
{
    /*
    * show form for adding njew category when you click on button "Create new categories"
    */
    $("#add-new-category").click(function()
    {
        $(".add_new_cat").toggle();
    });

    $("#order-category").click(function()
    {
        $(".order_category").toggle();
    });

});
</script>
<h1><?= Yii::t('app', 'Categories') ?></h1>

<?php if ($role == User::ROLE_ADMIN || $role == User::ROLE_SUPERADMIN || $role == User::ROLE_SENIOREDITOR ) : ?>
<a href="javascript:void(0)" id="order-category" class="btn btn-minw btn-primary"><?= Yii::t('app', 'Order category') ?></a>
<a href="javascript:void(0)" id="add-new-category" class="btn btn-minw btn-primary"><?= Yii::t('app', 'Create new categories') ?></a>
<br><br>

<?php include "_add_new_category.php" ?>
<?php include "_order_category.php" ?>
<?php endif; ?>

<br><br>
<div class="row">
    <?php  foreach ( $categories as $category_key=>$category_value ) : ?>
    <div class="col-sm-6 col-lg-4 matchheight">
        <div class="block block-themed">
            <div class="block-header bg-primary-dark">
                <ul class="block-options">
                    <li></li>
                </ul>
                <h3 class="block-title parent-category">
                <?= Yii::t('app', $category_value->name) ?>
                <?php //echo count($category->relationCategoriesLevelOne->relationCategoryStories);  ?>
                 <?php
                   /* if ( Yii::$app->user->getIdentity()->role == User::ROLE_ADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SENIOREDITOR ) : ?>
                    <a class="edit-action label label-primary" data-id="<?=$category->id?>"><?= Yii::t('app', 'Edit') ?></a>
                    <a class="delete-action label label-danger" data-id="<?=$category->id?>"><?= Yii::t('app', 'Delete') ?></a>
                <?php endif; */?>
                </h3>
            </div>               <?php $S=new \backend\models\search\StorySearch; ?>
            <div class="block-content">
                <p>
                    <table id="countries" class="table table-striped">
                        <tr>
                            <td width="60%"><strong><?= Yii::t('app', 'Subcategory') ?></strong></td>
                            <td width="20%" class="center"><strong><?= Yii::t('app', 'Stories') ?></strong> </td>
                            <td width="20%" class="center"><strong><?= Yii::t('app', 'Actions') ?></strong></td>
                        </tr>
                        <?php
                        foreach ( $category_value->relationCategoriesLevelOne as $category_level_one_key=>$category_level_one_value):
                        $numOfStories=(isset($numberOfStories[$category_level_one_value->id])) ? $numberOfStories[$category_level_one_value->id] : 0;
                        ?>
                        <tr _id="<?php echo $category_level_one_value->id ?>">
                            <td width="60%" class="category-name"><?= Yii::t('app', $category_level_one_value->name)?></td>
                            <td width="20%" class="center">
                                <a href="<?=Url::to(['/story/published', "StorySearch[filter_category]"=>$category_level_one_value->id])?>" target="_blank">
                                    <span class="number-stories badge badge-info"><?php echo $numOfStories ?></span>
                                </a>
                            </td>
                            <td width="20%" class="center">
                                <?php if ( ($role == User::ROLE_SUPERADMIN) && $numOfStories==0) : ?>
                                <button class="btn btn-xs btn-default delete-sub-action" type="button" data-toggle="tooltip" title="" data-original-title="Remove Client"><i class="fa fa-times"></i></button>
                                <?php endif; ?>
                                <?php if ( ($role == User::ROLE_SUPERADMIN)) : ?>
                                <!--<button class="btn btn-xs btn-default edit-sub-action" type="button" data-toggle="tooltip" title="" data-original-title="Edit Client"><i class="fa fa-pencil"></i></button>-->
                                <?php endif; ?>

                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php if ($role == User::ROLE_ADMIN ||  $role == User::ROLE_SUPERADMIN || $role == User::ROLE_SENIOREDITOR ) : ?>

                    <a class="add-subcategory-action btn btn-sm btn-primary" data-categoryid="<?php echo $category_value->id ?>"><?= Yii::t('app', 'Create new subcategory')?></a>

                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
     <?php endforeach; ?>

</div>

<script>

    $(function(){

        categoryModule.init({
            isOrdering  : false <?php /*echo ( ( Yii::$app->user->getIdentity()->role == User::ROLE_ADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN || Yii::$app->user->getIdentity()->role == User::ROLE_SENIOREDITOR ) ? 'true' : 'false' )*/ ?>,
            createUrl   : '<?=Url::to(['category/create'])?>',
            createSubUrl: '<?=Url::to(['category/create-sub-category'])?>',
            updateUrl   : '<?=Url::to(['category/update'])?>',
            orderUrl    : '<?=Url::to(['category/order'])?>',
            deleteUrl   : '<?=Url::to(['category/delete'])?>',
            deleteSubUrl: '<?=Url::to(['category/delete-subcategory'])?>'
        });

    });

</script>