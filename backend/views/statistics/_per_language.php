<div class="col-lg-4 matchheight">
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
            <h3 class="block-title"><?= Yii::t('app', 'News per language')?></h3>
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