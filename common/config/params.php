<?php
//\Yii::$app->params['thumbnail'];
return [
    'adminEmail' => 'dario@born2invest.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    //cache time
    '7_day_cache'=>604800,
    '1_day_cache'=>86400,
    '5_day_cache'=>432000,
    '6_hours_cache'=>21600,
    '8_hours_cache'=>28800,
    '12_hours_cache'=>43200,
    '14_day_cache'=>1209600,

    //whitelist IPs
    'local_ip'=>['127.0.0.1','::1'],
];
