<?php
use yii\web\View;
$this->title = $model->title;
?>

<style>
.page {
width: 100%;
padding-bottom: 0;
padding-top: 118px;
}
.page_margin_top {
    margin-top: 0;
}
.header_container {
    padding-bottom: 20px;
}

#iframe {
    width: 100%;
}

</style>

<iframe id="iframe" src="<?= $model->link ?>"></iframe>

<script>
$( "#iframe" ).height($(window).innerHeight()-135);
</script>