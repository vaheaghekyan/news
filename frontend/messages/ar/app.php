<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/ar/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/ar/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/ar/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/ar/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/ar/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>