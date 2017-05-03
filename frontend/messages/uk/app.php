<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/uk/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/uk/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/uk/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/uk/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/uk/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>