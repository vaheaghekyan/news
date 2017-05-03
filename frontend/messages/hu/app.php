<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/hu/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/hu/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/hu/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/hu/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/hu/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>