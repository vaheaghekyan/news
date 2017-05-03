<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/sr/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/sr/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/sr/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/sr/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/sr/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>