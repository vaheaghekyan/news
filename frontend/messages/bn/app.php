<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/bn/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/bn/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/bn/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/bn/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/bn/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>