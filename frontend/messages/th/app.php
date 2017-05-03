<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/th/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/th/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/th/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/th/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/th/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>