<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/uz/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/uz/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/uz/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/uz/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/uz/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>