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
use yii\grid\GridView;
use yii\helpers\Html;
use backend\components\Helpers;

$this->registerCssFile('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/modal.bootstrap.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/upload-file-field.js', array('position'  => View::POS_END));
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/user.js', array('position'  => View::POS_END));
$this->title=Yii::t('app', 'Users');

?>

<script>
var USER_ROLE         = "<?=(!Yii::$app->user->isGuest ? Yii::$app->user->getIdentity()->role : "GUEST")?>";
var ROLE_SUPERADMIN   = "<?=User::ROLE_SUPERADMIN?>";
var ROLE_ADMIN        = "<?=User::ROLE_ADMIN?>";
var ROLE_SENIOREDITOR = "<?=User::ROLE_SENIOREDITOR?>";
var ROLE_EDITOR       = "<?=User::ROLE_EDITOR?>";
var ROLE_MARKETER     = "<?=User::ROLE_MARKETER?>";
var USER_ID           = <?=Yii::$app->user->getId()?>;
</script>

<div class="row">
    <div class="col-sm-12 col-lg-12">
        <div class="block block-bordered">
            <div class="block-header bg-gray-lighter">
                <h3 class="block-title"><?= $this->title ?></h3>
            </div>
            <div class="block-content">
                <p>  <?php if ( Yii::$app->user->getIdentity()->role == User::ROLE_SUPERADMIN ) : ?>
                        <a href="javascript:void(0)" id="new-user" class="btn btn-minw btn-primary"><?= Yii::t('app', 'New')   ?></a>
                    <?php endif;?>
                </p>

            <p>
            <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions'=>['class'=>'table table-striped table-borderless table-header-bg'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    'name',
                    'email:email',
                   // 'password',
                    [
                        'attribute'=>'date',
                        'value'=>function($data)
                        {
                            return date("M d, Y", strtotime($data->date));
                        },
                        'filter'=>Html::activeTextInput ($searchModel, 'date',  ['class'=>'datepicker form-control'] )
                   ],
                    // 'status',
                    'role',
                    [
                        'label'=>Yii::t('app', 'Stories'),
                        'value'=>function($data)
                        {
                            //var_dump($data['cnt']);
                            return ($data->count_story_user);
                        }
                    ],
                    [
                        'label'=>Yii::t('app', 'Active'),
                        'format'=>'raw',
                        'value'=>function($data)
                        {
                            if($data->status==0)
                                return '<a href="'.Url::to(["/user/activate-user", 'id'=>$data->id]).'" data-method="post"><img src="/img/icons/cross.png" width="25" height="25"></a>';
                            else
                                return '<a href="'.Url::to(["/user/disactivate-user", 'id'=>$data->id]).'" data-method="post"><img src="/img/icons/tick.png" width="25" height="25"></a>';
                        },
                        'visible'=>Helpers::columnVisible([User::ROLE_SUPERADMIN, User::ROLE_ADMIN]),
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                        'buttons'=>[
                            'update' => function ($url, $model, $key)
                            {
                                return '<a href="javascript:;" class="edit_user_icon" data-userid="'.$model->id.'"><span class="glyphicon glyphicon-pencil " ></span></a>';
                            },
                        ],
                        'visible'=>Helpers::columnVisible([User::ROLE_SUPERADMIN]),
                    ],
                ],
            ]); ?>
            </p>
            </div>
            </div>
        </div>
    </div>
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