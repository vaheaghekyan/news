<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/es/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/es/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/es/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/es/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/es/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>