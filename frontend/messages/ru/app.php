<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/ru/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/ru/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/ru/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/ru/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/ru/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>