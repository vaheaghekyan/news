<?php
/**
 * Created by PhpStorm.
 * User: Oleksii
 * Date: 15.06.2015
 * Time: 12:08
 */
use yii\web\View;
use backend\models\Story;
use backend\models\Category;
use backend\models\User;
use yii\helpers\Url;

$this->title="All stories";
$this->registerCssFile(Yii::$app->request->baseUrl.'/css/bootstrap.min.css');
$this->registerCssFile('https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
$this->registerCssFile('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
$this->registerCssFile(Yii::$app->request->baseUrl.'/css/dataTables.bootstrap.css');
$this->registerCssFile(Yii::$app->request->baseUrl.'/css/datepicker.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/modal.bootstrap.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.dataTables.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap-datepicker.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/dataTables.bootstrap.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/story.js', array('position'  => View::POS_END));
?>


<div id="title" class="section group pright ptop">
    <div class="col span_5_of_5">
        <h1>Stories</h1>
        Find and edit current stories or create <a href="<?=Url::toRoute(['story/create'])?>">new stories</a>.
    </div>
</div>
<div id="dashboardfilter" class="section group pright">
    <div class="col span_1_of_6">
        <select id="filter-categories">
            <option value="">Category</option>
            <?php
            $categories = Category::getParents();
            foreach ( $categories as $cat ) : ?>

                <option value="<?=$cat->id?>" <?=( $category && $category->id == $cat->id  ? "selected" : "" )?>><?=$cat->name?></option>

            <?php endforeach; ?>
        </select>
    </div>
    <div class="col span_1_of_6">
        <select id="filter-subcategories">

            <option>Subcategory</option>
            <?php
            $categories = Category::getChildren();
            foreach ( $categories as $cat ) : ?>

                <option value="<?=$cat->id?>" <?=( $subCategory && $subCategory->id == $cat->id  ? "selected" : "" )?>><?=$cat->name?></option>

            <?php endforeach; ?>
        </select>
    </div>
    <div class="col span_1_of_6">
        <select id="filter-countries">
            <option>Country</option>
            <?php
                $countries = \backend\models\Country::getAllByLanguage();
                foreach ( $countries as $cat ) :
            ?>
                    <option value="<?=$cat->id?>" <?=( $country && $country->id == $cat->id  ? "selected" : "" )?>><?=$cat->name?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col span_1_of_6">
        <select id="filter-authors">
            <option>Author</option>
            <?php $authors = User::getAuthors(); foreach ( $authors as $entity ) :?>
                <option value="<?=$entity->id?>" <?=( $author && $author->id == $entity->id  ? "selected" : "" )?>><?=$entity->name?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col span_1_of_6">
        <input type="text" id="filter-keywords" placeholder="Keywords" />
    </div>
    <div class="col span_1_of_7">
        <input type="button" value="Filter" id="filter-btn">
    </div>
    <div class="col span_1_of_7">
        <input type="button" value="Reset"  id="reset-filter-btn">
    </div>
</div>
<div id="whitebox" class="pright">
    <div class="section group pright">
        <div class="col span_9_of_9">
            <h1>Results</h1>
            <span id="number-of-stories"><?=Story::getCount( Story::STATUS_PUBLISHED )?></span> stories
        </div>
    </div>
    <table id="story-table" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>Title</th>
            <th>Category</th>
            <th>External Link</th>
            <th>Author</th>
            <th>Media</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
    </table>
</div>
<script>
    $(function(){
        storyModule.init({
            subcategories       : <?=json_encode(Category::getSubcategories())?>,
            storyEditUrl        : '<?=Url::to(['story/update'])?>',
            storyDeleteUrl      : '<?=Url::to(['story/delete'])?>',
            storyUnpublishUrl   : '<?=Url::to(['story/unpublish'])?>',
            statusPublished     : '<?=Story::STATUS_PUBLISHED?>',
            applyFilter         : <?=(( $category && $subCategory || $country  || $author) ? 'true' : 'false')?>
        });
    });
</script>