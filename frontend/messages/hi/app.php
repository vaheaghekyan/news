<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/hi/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/hi/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/hi/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/hi/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/hi/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>