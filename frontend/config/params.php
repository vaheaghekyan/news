<?php
//\Yii::$app->params['thumbnail'];
return [
    //'adminEmail' => 'admin@example.com',

    //cookies
    'frontend_language_code_cookie'=>'lngcd',//for example: en, hr...
    'frontend_language_id_cookie'=>'lngid', //for example: 7
    'frontend_edition_country_id_cookie' => 'edtid', //for example:2 => 2 is United States in "countries"
    'frontend_timezone_cookie' => 'tmz', //for example:2 => 2 is United States in "countries"
    'frontend_user_id_cookie' => 'usrd', //user id, randomly generated

    //used to check which item in left sidebar should be checked
    //'frontend_parent_category_id_cookie' => 'frontend_category_id', //for example:5 => 5 is BUSINESS
    //'frontend_level_one_category_id_cookie' => 'frontend_subcategory_id', //for example:1 => 1 is Trending
];
