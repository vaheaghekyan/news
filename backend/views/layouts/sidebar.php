<?php
use  yii\widgets\Menu;
$role=Yii::$app->user->getIdentity()->role;
?>
<nav id="sidebar">
  <!-- Sidebar Scroll Container -->
  <div id="sidebar-scroll">
    <!-- Sidebar Content -->
    <!-- Adding .sidebar-mini-hide to an element will hide it when the sidebar is in mini mode -->
    <div class="sidebar-content">
      <!-- Side Header -->
      <div class="side-header side-content bg-white-op">
        <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
        <button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout" data-action="sidebar_close">
          <i class="fa fa-times"></i>
        </button>
        <!-- Themes functionality initialized in App() -> uiHandleTheme() -->

        <a class="h5 text-white" href="/index.php">
          <img src="/img/logo_small.png" class="img-responsive" alt="" />
        </a>
      </div>
      <!-- END Side Header -->
      <!-- Side Content -->
      <div class="side-content">
       <?php

       //if ($this->beginCache(\Yii::$app->params['backend_main_menu_cache'], ['variations' => [Yii::$app->language]]))
       //{
            require_once("menu.php");
            echo Menu::widget(
            [
                'items' => $mergeItems,
                'options'=> ['class'=>'nav-main'],
                'encodeLabels'=>false,
                'activateItems'=>true,
                'activateParents'=>true,
                'activeCssClass'=>'active open',

            ]);
            //$this->endCache();
        //}
        ?>

      </div>
      <!-- END Side Content -->
    </div>
    <!-- Sidebar Content -->
  </div>
  <!-- END Sidebar Scroll Container -->
</nav>
