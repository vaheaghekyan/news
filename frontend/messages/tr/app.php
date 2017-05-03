<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/tr/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/tr/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/tr/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/tr/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/tr/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend,$timestamp);

?>