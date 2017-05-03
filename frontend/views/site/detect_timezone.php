<?php
use yii\web\View;
use yii\helpers\Url;
$this->registerJsFile(Yii::$app->request->baseUrl.'/extra/js/jstz-1.0.4.min.js', array('position'  => View::POS_END));
?>

<h1 style="text-align:center">Loading...</h1>
<script>
$(document).ready(function()
{
    var tz = jstz.determine(); // Determines the time zone of the browser client
    var name = tz.name();
    if(name)
        window.location="<?= Url::to(["/site/detect-timezone"]) ?>?timezone="+name;  // Returns the name of the time zone eg "Europe/Berlin"
    else
        window.location="<?= Url::to(["/site/detect-timezone"]) ?>?timezone=UTC";  // Returns the name of the time zone eg "Europe/Berlin"
});
</script>
