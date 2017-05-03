<?php
/*
HOW TO USE
<?= $this->render('/_alert'); ?>

*/
?>
<?php if(Yii::$app->session->getAllFlashes()):?>
<div class="row">
  <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
	  <?php if (in_array($type, ['success', 'danger', 'warning', 'info'])): ?>

    <div class="col-sm-12 ">
        <!-- Success Alert -->
        <div class="alert alert-<?= $type ?> alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
            <h3 class="font-w300 push-15"><?= strtoupper($type) ?></h3>
             <?php
                //for example I'm using this when I'm setting multiple flashes on auctions when I upload pics (addFlash())
                if(is_array($message))
                {
                    foreach($message as $value)
                    {
                        echo "<strong>".$value."</strong><br>";
                    }
                }
                else
                {
                    echo "<strong>".$message."</strong>";
                }
                ?>
        </div>
        <!-- END Success Alert -->
    </div>
    <?php endif ?>
    <?php endforeach ?>
</div>
<?php endif; ?>
