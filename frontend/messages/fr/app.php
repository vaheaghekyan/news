<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/fr/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/fr/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/fr/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/fr/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/fr/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>