<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/sv/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/sv/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/sv/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/sv/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/sv/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>