<?php
use backend\models\Category;
use common\components\Helpers as CommonHelpers;
use yii\helpers\Url;
use frontend\models\search\StorySearch;

$categories = Category::getParents();

//check if url consists specific word so you can activate parent menu
function activeParent($string)
{
    if(preg_match("~\b$string\b~",$_SERVER["REQUEST_URI"])) //match exact word
        return true;
    else
        return false;
}
?>
<script>
$(document).ready(function()
{
    //if subcategory is selected to show stories from, you have to activate parent category
    var active_menu_child = $(".active_menu_child");
    //find .submenu closest to child item and add class .selected
    if(active_menu_child.length)
        active_menu_child.closest(".submenu").addClass("selected");
});
</script>
<div class="menu_container clearfix">
   <nav>
      <ul class="sf-menu">
        <?php foreach($categories as $key=>$category): ?>
        <?php $category_name=strtolower(str_replace(" ", "-", $category->name)); ?>
        <?php $class=activeParent($category_name) ? "selected" : NULL;?>
        <li class="submenu <?= $class ?>">
            <a href="<?= Url::to(['/story/index','categoryid'=>$category->id, 'type'=>StorySearch::CATEGORY,'name'=>$category_name])?>" class="main_category_menu">
            <?= Yii::t('app', $category->name) ?>
            </a>
            <ul>
                <?php foreach($category->relationCategoriesLevelOne as $subcategory): ?>
                <?php $sub_category_name=strtolower(str_replace(" ", "-", $subcategory->name)); ?>
                <?php $class=activeParent($sub_category_name) ? "active_menu_child" : NULL;?>
                <li class="<?=$class?>">
                <a href="<?= Url::to(['/story/index', 'categoryid'=>$subcategory->id, 'type'=>StorySearch::SUBCATEGORY,'name'=>$sub_category_name])?>">
                <?= Yii::t('app', $subcategory->name) ?>
                </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endforeach; ?>

      </ul>
   </nav>

   <div class="mobile_menu_container">
      <a href="#" class="mobile-menu-switch">
      <span class="line"></span>
      <span class="line"></span>
      <span class="line"></span>
      </a>
      <div class="mobile-menu-divider"></div>
      <nav>
         <ul class="mobile-menu">

             <?php foreach($categories as $key=>$category): ?>
            <?php $category_name=strtolower(str_replace(" ", "-", $category->name)); ?>
            <?php $class=activeParent($category_name) ? "selected" : NULL;?>
            <li class="submenu <?= $class ?> selected">
                <span class="mobile-menu-cat">
                <?= Yii::t('app', $category->name) ?>
                </span>
                <ul>
                    <?php foreach($category->relationCategoriesLevelOne as $subcategory): ?>
                    <?php $sub_category_name=strtolower(str_replace(" ", "-", $subcategory->name)); ?>
                    <?php $class=activeParent($sub_category_name) ? "submenu-item-active" : NULL;?>
                    <li class="selected2">
                    <a href="<?= Url::to(['/story/index', 'categoryid'=>$subcategory->id, 'type'=>StorySearch::SUBCATEGORY,'name'=>$sub_category_name])?>">
                    <?= Yii::t('app', $subcategory->name) ?>
                    </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endforeach; ?>


         </ul>
      </nav>
   </div>
</div>


