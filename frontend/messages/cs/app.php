<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/cs/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/cs/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/cs/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/cs/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/cs/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>