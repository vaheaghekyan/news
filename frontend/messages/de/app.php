<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/de/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/de/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/de/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/de/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/de/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend,$timestamp);

?>