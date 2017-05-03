<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/ms/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/ms/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/ms/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/ms/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/ms/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>