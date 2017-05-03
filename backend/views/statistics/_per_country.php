<div class="col-lg-4 matchheight">
    <!-- Email Center Widget -->
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
            <h3 class="block-title"><?= Yii::t('app', 'News per country')?></h3>
        </div>
        <div class="block-content">
            <div class="list-group">
            <?php  foreach($per_country as $value): ?>
            <a class="list-group-item" href="javascript:void(0)">
                <span class="badge"><?= $value->countNumberOfStories ?></span>
                <i class="fa fa-fw fa-inbox push-5-r"></i> <?= $value->name ?>
            </a>
            <?php endforeach; ?>
            </div>

        </div>
    </div>
    <!-- END Email Center Widget -->
</div>