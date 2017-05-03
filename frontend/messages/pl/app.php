<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/pl/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/pl/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/pl/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/pl/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/pl/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>