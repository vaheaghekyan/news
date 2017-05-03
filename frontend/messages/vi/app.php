<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/vi/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/vi/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/vi/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/vi/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/vi/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>