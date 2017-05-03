<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/ro/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/ro/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/ro/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/ro/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/ro/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>