<?php
use backend\models\Story;
/* @var $story \backend\models\Story */
$this->title="Preview"; 
?>
<img src="<?=$story->getImage(Story::THUMB_WIDTH, Story::THUMB_HEIGHT, $story)?>?t=<?=time()?>">
<div class="description">
    <h2><?=$story->title?></h2>
    <?=$story->description?>
</div>
