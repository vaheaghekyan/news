<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/pt/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/pt/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/pt/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/pt/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/pt/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>