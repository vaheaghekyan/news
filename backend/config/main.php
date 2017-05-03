<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log', 'backend\components\OnLoad'],
    'language'=> 'en',
    //'sourceLanguage'=>'en', //if this is enable, yii will not translate messages -.-
    'modules' =>
    [
        'api' =>
        [
            'class' => 'backend\modules\api\Api',
        ],
        'settings' =>
        [
            'class' => 'backend\modules\settings\Settings',
        ],
        'preroll' =>
        [
            'class' => 'backend\modules\preroll\Preroll',
        ],
    ],    
    'components' =>
    [
        'i18n' =>
        [
            'translations' =>
            [
                'app*' =>
                [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    //'sourceLanguage' => 'en',
                    /*'fileMap' => [
                        'app' => 'app.php',
                        //'app/error' => 'error.php',
                    ], */
                ],
            ],
        ],
       /* 'user' =>
        [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],  */
        'log' =>
        [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        /*'request' =>
        [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'tn-lqdv7YM6V8I2TLhIv2Gk5Uw0dD_yd',
        ],*/
        'user' =>
         [
            'identityClass' => 'backend\models\User',
            'enableAutoLogin' => true,
            //'authTimeout' => 86400,
            //'enableSession' => true,
        ],
        //Session
       /* 'session' =>
        [
              'class' => 'yii\web\Session',
              'timeout' => 86400, //here set session timeout
        ], */
        'errorHandler' =>
         [
            'errorAction' => 'site/error',
        ],
        'urlManager' =>
        [
            'class' => 'backend\components\ZUrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<language:[\w+]{2}>/<controller>/<action>/<id:\d+>/<title>' => '<controller>/<action>',
                '<language:[\w+]{2}>/<controller>/<id:\d+>/<title>'  => '<controller>/index',
                '<language:[\w+]{2}>/<controller>/<action>/<id:\d+>' => '<controller>/<action>',
                '<language:[\w+]{2}>/<controller>/<action>'          => '<controller>/<action>',
                '<language:[\w+]{2}>/<controller>'                   => '<controller>',
                '<language:[\w+]{2}>/'                               =>'admin/index',
                '<language:[\w+]{2}>/<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
                '<language:[\w+]{2}>/<module:\w+>/<controller:\w+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
                '<controller>/<action>'                              => '<controller>/<action>'

            ],
        ],
         'assetManager' =>
        [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js'=>[]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js'=>[]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],

            ],
        ],
        
    ],
    'params' => $params,
];
