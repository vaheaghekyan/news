<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/hr/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/hr/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/hr/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/hr/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/hr/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>