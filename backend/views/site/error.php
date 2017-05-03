<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>

<div class="row">
  <div class="col-sm-6 col-sm-offset-3">
    <!-- Error Titles -->
    <h1 class="font-w300 text-smooth animated rollIn">
      <?= Html::encode($this->title) ?>
    </h1>
    <h2 class="h3 font-w300 push-50 animated fadeInUp">
      <?= nl2br(Html::encode($message)) ?>
    </h2>
    <!-- END Error Titles -->

  </div>
</div>
