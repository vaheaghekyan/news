<?php
use yii\helpers\Url;
use backend\components\LinkGenerator;
use backend\models\Story;
$this->title="Born2Invest";
?>

<div class="content bg-image overflow-hidden" style="background-image: url('/img/b2i_cover.jpg');">
    <div class="push-50-t push-15">
        <h1 class="h2 text-white animated zoomIn"><?= Yii::t('app', 'Dashboard') ?></h1>
        <h2 class="h5 text-white-op animated zoomIn"><?= Yii::t('app', 'Welcome') ?> <?= Yii::$app->user->getIdentity()->name ?></h2>
    </div>
</div>

<br>

<div class="row">
    <div class="col-sm-12">
        <?php if(empty($storiesNoImgVid)): ?>
        <div class="alert alert-info">
            <h3 class="font-w300 push-15">Information</h3>
            <p><?=Yii::t('app', 'Your stories are fine')?></p>
        </div>
        <a href="<?=Url::to(['/story/create'])?>" class="btn btn-lg btn-success btn-block"><i class="fa fa-plus-circle"></i> <?=Yii::t('app', 'Create Story')?></a>
        <?php else: ?>
        <div class="alert alert-danger">
            <h3 class="font-w300 push-15">Error</h3>
            <p><?=Yii::t('app', 'Your stories have error')?></p>
        <?php foreach($storiesNoImgVid as $story): ?>
            <p>
            <a class="btn btn-minw btn-danger btn-block" href="<?=Url::to(['/story/update','id'=>$story->id])?>">
                <?php echo ($story->type==Story::TYPE_IMAGE) ? '<i class="fa fa-image"></i>' : '<i class="fa fa-video-camera"></i>';   ?>
                <?=$story->title?>
            </a>
            </p>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php /*<div class="col-lg-4">
        <!-- Email Center Widget -->
        <div class="block block-bordered">
            <div class="block-header">
                <ul class="block-options">
                    <li>
                        <button type="button" data-toggle="block-option" data-action="fullscreen_toggle"><i class="si si-size-fullscreen"></i></button>
                    </li>
                    <!--<li>
                        <button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
                    </li> -->
                    <li>
                        <button type="button" data-toggle="block-option" data-action="content_toggle"><i class="si si-arrow-up"></i></button>
                    </li>
                </ul>
                <h3 class="block-title"><?= Yii::t('app', 'Stories') ?></h3>
            </div>
            <div class="block-content">
                <div class="pull-r-l pull-t push">
                    <table class="block-table text-center bg-gray-lighter border-b">
                        <tbody>
                            <tr>
                                <td class="border-r" style="width: 50%;">
                                    <div class="h1 font-w700"><?= $story_report["total"] ?></div>
                                    <div class="h5 text-muted text-uppercase push-5-t"><?= Yii::t('app', 'Stories') ?></div>
                                </td>
                                <td>
                                    <div class="push-30 push-30-t">
                                        <?= LinkGenerator::linkStoryCreate('<i class="fa fa-newspaper-o fa-3x text-black-op"></i><i class="si si-plus text-black-op"></i>')?>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="list-group">
                    <?= LinkGenerator::linkStoryUnpublished(
                        '<span class="badge">'.$story_report["unpublished"].'</span><i class="fa fa-eye-slash push-5-r"></i>'.Yii::t('app', 'Unpublished'),
                        ['class'=>'list-group-item']
                    ); ?>

                    <?= LinkGenerator::linkStoryPublished(
                        '<span class="badge">'.$story_report["published"].'</span><i class="fa fa-eye push-5-r"></i>'.Yii::t('app', 'Published'),
                        ['class'=>'list-group-item']
                    ); ?>

                    <?= LinkGenerator::linkStoryPending(
                        '<span class="badge">'.$story_report["pending"].'</span><i class="fa fa-question push-5-r"></i>'.Yii::t('app', 'Pending'),
                        ['class'=>'list-group-item']
                    ); ?>      

                </div>
            </div>
        </div>
        <!-- END Email Center Widget -->
    </div>

    <div class="col-lg-4">
        <!-- Notifications Widget -->
        <div class="block block-bordered">
            <div class="block-header">
                <ul class="block-options">
                    <li>
                        <button type="button" data-toggle="block-option" data-action="fullscreen_toggle"><i class="si si-size-fullscreen"></i></button>
                    </li>
                    <li>
                        <button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
                    </li>
                    <li>
                        <button type="button" data-toggle="block-option" data-action="content_toggle"><i class="si si-arrow-up"></i></button>
                    </li>
                </ul>
                <h3 class="block-title">Notifications</h3>
            </div>
            <div class="block-content">
                <div class="pull-r-l pull-t push">
                    <table class="block-table text-center bg-gray-lighter border-b">
                        <tbody>
                            <tr>
                                <td class="border-r" style="width: 50%;">
                                    <div class="h1 font-w700">3</div>
                                    <div class="h5 text-muted text-uppercase push-5-t">New Notifications</div>
                                </td>
                                <td>
                                    <div class="push-30 push-30-t">
                                        <i class="si si-directions fa-3x text-black-op"></i>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p><i class="fa fa-check"></i> The <a class="alert-link" href="javascript:void(0)">App</a> was updated successfully!</p>
                </div>
                <div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p><i class="fa fa-info-circle"></i> Just an information <a class="alert-link" href="javascript:void(0)">message</a>!</p>
                </div>
                <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p><i class="fa fa-warning"></i> Please pay <a class="alert-link" href="javascript:void(0)">attention</a>!</p>
                </div>
            </div>
        </div>
        <!-- END Notifications Widget -->
    </div>
    <div class="col-lg-4">
        <!-- Friends Widget -->
        <div class="block block-bordered">
            <div class="block-header">
                <ul class="block-options">
                    <li>
                        <button type="button" data-toggle="block-option" data-action="fullscreen_toggle"><i class="si si-size-fullscreen"></i></button>
                    </li>
                    <li>
                        <button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
                    </li>
                    <li>
                        <button type="button" data-toggle="block-option" data-action="content_toggle"><i class="si si-arrow-up"></i></button>
                    </li>
                </ul>
                <h3 class="block-title">Friends</h3>
            </div>
            <div class="block-content">
                <div class="pull-r-l pull-t push">
                    <table class="block-table text-center bg-gray-lighter border-b">
                        <tbody>
                            <tr>
                                <td class="border-r" style="width: 50%;">
                                    <div class="h1 font-w700">3</div>
                                    <div class="h5 text-muted text-uppercase push-5-t">New Friends</div>
                                </td>
                                <td>
                                    <div class="push-30 push-30-t">
                                        <i class="si si-users fa-3x text-black-op"></i>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <ul class="nav-users push">
                    <li>
                        <a href="base_pages_profile.html">
                            <img class="img-avatar" src="assets/img/avatars/avatar7.jpg" alt="">
                            <i class="fa fa-circle text-success"></i> Amanda Powell
                            <div class="font-w400 text-muted"><small>Web Designer</small></div>
                        </a>
                    </li>
                    <li>
                        <a href="base_pages_profile.html">
                            <img class="img-avatar" src="assets/img/avatars/avatar16.jpg" alt="">
                            <i class="fa fa-circle text-success"></i> Craig Stone
                            <div class="font-w400 text-muted"><small>Graphic Designer</small></div>
                        </a>
                    </li>
                    <li>
                        <a href="base_pages_profile.html">
                            <img class="img-avatar" src="assets/img/avatars/avatar6.jpg" alt="">
                            <i class="fa fa-circle text-success"></i> Linda Moore
                            <div class="font-w400 text-muted"><small>Photographer</small></div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- END Friends Widget -->
    </div>  */ ?>
</div>
