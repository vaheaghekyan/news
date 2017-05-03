<?php

$general=include "general.php";
$countries=include "countries.php";
$categories=include "categories.php";
$languages=include "lang.php";

return array_merge($general, $countries, $categories, $languages);

?>