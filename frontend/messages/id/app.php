<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/id/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/id/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/id/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/id/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/id/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>