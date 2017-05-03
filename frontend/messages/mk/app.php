<?php

$general=require "general.php";

$categories=require Yii::getAlias('@backend')."/messages/mk/categories.php";

$countries=require Yii::getAlias('@backend')."/messages/mk/countries.php";

$languages=require Yii::getAlias('@backend')."/messages/mk/lang.php";

$general_backend=require Yii::getAlias('@backend')."/messages/mk/general.php";
$timestamp=require Yii::getAlias('@backend')."/messages/mk/timestamp.php";



return array_merge($general, $categories, $countries, $languages, $general_backend, $timestamp);

?>