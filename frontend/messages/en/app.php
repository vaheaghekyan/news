<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/en/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/en/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/en/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/en/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/en/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend,$timestamp);

?>