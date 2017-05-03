<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/bg/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/bg/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/bg/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/bg/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/bg/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>