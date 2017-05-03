<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/el/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/el/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/el/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/el/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/el/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>