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
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/upload-file-field.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/user.js', array('position'  => View::POS_END));
?>


<div id="title" class="section group pright ptop">
    <div class="col span_5_of_5">
        <h1>Users</h1>
        Find
        <?php if ( Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN ) : ?>
        , Edit, Delete or create <a href="javascript:void(0)" id="new-user">new</a>
        <?php endif;?>
        users.
    </div>
</div>
<div id="dashboardfilter" class="section group pright">
    <div class="col span_1_of_5">
        <select id="filter-authors">
            <option value="">Author</option>
            <?php $authors = User::getAuthors(); foreach ( $authors as $author ) :?>
                <option value="<?=$author->id?>"><?=$author->name?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col span_1_of_5">
        <select id="filter-roles">
            <option value="">Role</option>
            <option value="<?=User::ROLE_EDITOR?>">Editor</option>
            <option value="<?=User::ROLE_SENIOREDITOR?>">Senior Editor</option>
            <option value="<?=User::ROLE_ADMIN?>">Admin</option>
            <option value="<?=User::ROLE_SUPERADMIN?>">Super Admin</option>
        </select>
    </div>
    <div class="col span_1_of_5">
        <input type="text" id="filter-keywords" placeholder="Email" />
    </div>
    <div class="col span_1_of_5">
        <input type="button" value="Filter" id="filter-btn">
    </div>
    <div class="col span_1_of_5">
        <input type="button" value="Reset"  id="reset-filter-btn">
    </div>
</div>
<div id="whitebox" class="pright">
    <div class="section group pright">
        <div class="col span_9_of_9">
            <h1>Results</h1>
            <span id="number-of-users"></span> users
        </div>
    </div>
    <table id="users-table" class="table table-hover">
        <thead>
        <tr>
            <th></th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Role</th>
            <th>Stories</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        </thead>
    </table>

</div>
<script>
    $(function(){
        userModule.init({
            languages      : <?=json_encode($languages)?>,
            getUrl         : '<?=Url::to(['user/get'])?>',
            createUrl      : '<?=Url::to(['user/create'])?>',
            editUrl        : '<?=Url::to(['user/update'])?>',
            deleteUrl      : '<?=Url::to(['user/delete'])?>'
        });
    });
</script>