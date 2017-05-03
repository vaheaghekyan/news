<?php
use yii\web\View;
use backend\models\Story;
use backend\models\Category;
use backend\models\User;
use yii\helpers\Url;

$this->title="Home";

$this->registerCssFile('https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
$this->registerCssFile('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
$this->registerCssFile(Yii::$app->request->baseUrl.'/css/dataTables.bootstrap.css');
$this->registerCssFile(Yii::$app->request->baseUrl.'/css/datepicker.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/modal.bootstrap.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.dataTables.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap-datepicker.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/dataTables.bootstrap.min.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/dashboard.js', array('position'  => View::POS_END));

?>
       
<div id="title" class="section group pright ptop">
    <div class="col span_5_of_5">
        <h1>Dashboard</h1>
        Edit, delete, publish or schedule stories.
    </div>
</div>
<div id="dashboardfilter" class="section group pright">
    <div class="col span_1_of_6">
        <select id="filter-statuses">
            <option value="<?=Story::STATUS_PENDING?>">Pending</option>
            <option value="<?=Story::STATUS_APPROVED?>">Scheduled</option>
            <option value="<?=Story::STATUS_UNPUBLISHED?>">Unpublished</option>
        </select>
    </div>
    <div class="col span_1_of_6">
        <select id="filter-categories">
            <option value="">Category</option>
            <?php
                $categories = Category::getParents();
                foreach ( $categories as $category ) : ?>

                    <option value="<?=$category->id?>"><?=$category->name?></option>

            <?php endforeach; ?>
        </select>
    </div>
    <div class="col span_1_of_6">
        <select id="filter-subcategories">
            <option>Subcategory</option>
            <?php
            $categories = Category::getChildren();
            foreach ( $categories as $category ) : ?>

                <option value="<?=$category->id?>"><?=$category->name?></option>

            <?php endforeach; ?>
        </select>
    </div>
    <div class="col span_1_of_6">
        <select id="filter-authors">
            <option>Author</option>
            <?php $authors = User::getAuthors(); foreach ( $authors as $author ) :?>
            <option value="<?=$author->id?>"><?=$author->name?></option>
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
        <input type="button" value="Reset" id="reset-filter-btn">
    </div>
</div>
<div id="whitebox" class="pright pending-grid">
    <div class="section group pright">
        <div class="col span_9_of_9">
            <h1>Pending</h1>
            <span id="number-of-pending-stories"><?=Story::getCount( Story::STATUS_PENDING )?></span> stories
        </div>
    </div>
    <table id="dashboard-table" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>Title</th>
            <th>Category</th>
            <th>External Link</th>
            <th>Author</th>
            <th>Media</th>
            <th>Date</th>
            <th>Actions </th>
        </tr>
        </thead>
    </table>

</div>
<div id="whitebox" class="pright approved-grid">
    <div class="section group pright ptop">
        <div class="col span_9_of_9">
            <h1>Scheduled</h1>
            <span id="number-of-approved-stories"><?=Story::getCountScheduled()?></span>  stories
        </div>
    </div>
    <table id="dashboard-table-approved" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>Title</th>
            <th>Category</th>
            <th>External Link</th>
            <th>Author</th>
            <th>Media</th>
            <th>Date</th>
            <th>Actions </th>
        </tr>
        </thead>

    </table>
</div>


<div id="whitebox" class="pright unpublished-grid">
    <div class="section group pright ptop">
        <div class="col span_9_of_9">
            <h1>Unpublished</h1>
            <span id="number-of-unpublished-stories"><?=Story::getCountUnpublished()?></span> stories
        </div>
    </div>
    <table id="dashboard-table-unpublished" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>Title</th>
            <th>Category</th>
            <th>External Link</th>
            <th>Author</th>
            <th>Media</th>
            <th>Date</th>
            <th>Actions </th>
        </tr>
        </thead>

    </table>
</div>

<script>
    $(function(){
        dashboardModule.init({
            statusPending       : '<?=Story::STATUS_PENDING?>',
            statusApproved      : '<?=Story::STATUS_APPROVED?>',
            statusUnpublished   : '<?=Story::STATUS_UNPUBLISHED?>',
            subcategories       : <?=json_encode(Category::getSubcategories())?>,
            storyEditUrl        : '<?=Url::to(['story/update'])?>',
            storyUnpublishUrl   : '<?=Url::to(['story/unpublish'])?>',
            storyDeleteUrl      : '<?=Url::to(['story/delete'])?>',
            storyApproveUrl     : '<?=Url::to(['story/approve'])?>',
            storyPublishUrl     : '<?=Url::to(['story/publish'])?>',
            storySchedulePublishUrl : '<?=Url::to(['story/schedule-publish'])?>'
        });
    });
</script>


