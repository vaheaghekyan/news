<?php
use backend\models\User;
use backend\models\Language;
use yii\caching\DbDependency;

$languages=Language::userRelatedLanguages();
?>
<!-- Side Overlay-->

<aside id="side-overlay">
  <!-- Side Overlay Scroll Container -->
  <div id="side-overlay-scroll">
    <!-- Side Header -->
    <div class="side-header side-content">
      <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
      <button class="btn btn-default pull-right" type="button" data-toggle="layout" data-action="side_overlay_close"> <i class="fa fa-times"></i> </button>
      <span> <img class="img-avatar img-avatar32" src="/img/logo.png" alt=""> <span class="font-w600 push-10-l">
      <?=Yii::$app->user->getIdentity()->name?>
      (<?=Yii::$app->user->getIdentity()->role?>)</span> </span> </div>
    <!-- END Side Header -->

    <!-- Side Content -->
    <div class="side-content remove-padding-t">
      <!-- Notifications -->
      <?php if(count($languages)>1): ?>
      <div class="block pull-r-l">
        <div class="block-header bg-gray-lighter">
          <ul class="block-options">
            <li>
              <!--<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>-->
            </li>
            <li>
              <button type="button" data-toggle="block-option" data-action="content_toggle"></button>
            </li>
          </ul>
          <h3 class="block-title">
            <?= Yii::t('app', 'Change language') ?>
          </h3>
        </div>
        <div class="block-content">
          <!-- Activity List -->
          <select name="language" class="form-control">
            <?php foreach ( $languages as $language ) : ?>
            <option value="<?=$language->code?>" <?=($currentLanguage == $language->code ? "selected" : "")?> >
            <?=$language->name?>
            </option>
            <?php endforeach; ?>
          </select>

          <!-- END Activity List -->
        </div>
      </div>
      <?php endif;?>

      <div class="block pull-r-l visible-xs">
        <div class="block-header bg-gray-lighter">
          <ul class="block-options">
            <li>
              <!--<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>-->
            </li>
            <li>
              <button type="button" data-toggle="block-option" data-action="content_toggle"></button>
            </li>
          </ul>
          <h3 class="block-title">
            <?= Yii::t('app', 'Time') ?>
          </h3>
        </div>
        <div class="block-content">
          <span class="servertime"></span>
        </div>
      </div>
      <!-- END Notifications -->

      <!-- START Mixpanel -->
      <div class="block pull-r-l">
        <div class="block-header bg-gray-lighter">
          <ul class="block-options">
            <li>
              <!--<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>-->
            </li>
            <li>
              <button type="button" data-toggle="block-option" data-action="content_toggle"></button>
            </li>
          </ul>
          <h3 class="block-title">
            Mixpanel (Sponsored)
          </h3>
        </div>
        <div class="block-content text-center">
          <a href="https://mixpanel.com/f/partner"><img src="//cdn.mxpnl.com/site_media/images/partner/badge_light.png" alt="Mobile Analytics" /></a>
        </div>
      </div>
      <!-- END Mixpanel -->

      <!-- Online Friends -->
      <?php  /*
        <div class="block pull-r-l">
        <div class="block-header bg-gray-lighter">
          <ul class="block-options">
            <li>
              <button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
            </li>
            <li>
              <button type="button" data-toggle="block-option" data-action="content_toggle"></button>
            </li>
          </ul>
          <h3 class="block-title">Online Friends</h3>
        </div>
        <div class="block-content block-content-full">
          <!-- Users Navigation -->
          <ul class="nav-users">
            <li> <a href="base_pages_profile.html"> <img class="img-avatar" src="assets/img/avatars/avatar3.jpg" alt=""> <i class="fa fa-circle text-success"></i> Ashley Welch
              <div class="font-w400 text-muted"><small>Copywriter</small></div>
              </a> </li>
            <li> <a href="base_pages_profile.html"> <img class="img-avatar" src="assets/img/avatars/avatar9.jpg" alt=""> <i class="fa fa-circle text-success"></i> Adam Hall
              <div class="font-w400 text-muted"><small>Web Developer</small></div>
              </a> </li>
            <li> <a href="base_pages_profile.html"> <img class="img-avatar" src="assets/img/avatars/avatar3.jpg" alt=""> <i class="fa fa-circle text-success"></i> Denise Watson
              <div class="font-w400 text-muted"><small>Web Designer</small></div>
              </a> </li>
            <li> <a href="base_pages_profile.html"> <img class="img-avatar" src="assets/img/avatars/avatar7.jpg" alt=""> <i class="fa fa-circle text-warning"></i> Julia Cole
              <div class="font-w400 text-muted"><small>Photographer</small></div>
              </a> </li>
            <li> <a href="base_pages_profile.html"> <img class="img-avatar" src="assets/img/avatars/avatar15.jpg" alt=""> <i class="fa fa-circle text-warning"></i> Ryan Hall
              <div class="font-w400 text-muted"><small>Graphic Designer</small></div>
              </a> </li>
          </ul>
          <!-- END Users Navigation -->
        </div>
        </div>
        */
        ?>
      <!-- END Online Friends -->

    </div>
    <!-- END Side Content -->
  </div>
  <!-- END Side Overlay Scroll Container -->
</aside>
<!-- END Side Overlay -->

