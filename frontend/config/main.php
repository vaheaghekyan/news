<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'frontend\components\OnLoad'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'story/index',
    'components' =>
    [
        'i18n' =>
        [
            'translations' =>
            [
                'app*' =>
                [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@frontend/messages',
                    //'sourceLanguage' => 'en',
                    /*'fileMap' => [
                        'app' => 'app.php',
                        //'app/error' => 'error.php',
                    ], */
                ],
            ],
        ],
        'urlManager' =>
        [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => false,
            'rules' =>
            [
                //seo url for story  on story/view
                //'<controller:\w+>/<seo_url:.*>/<type:\w+>/<name:[\w\-]+>/<id:\d+>/<categoryid:\d+>/<page:\d+>'=>'<controller>/view',

                //for example: story/index/category/top-stories/6
                '<controller:\w+>/<categoryid:\d+>/<page:\d+>/<type:\w+>/<name:[\w\-]+>'=>'<controller>/index',
                '<controller:\w+>/<categoryid:\d+>/<type:\w+>/<name:[\w\-]+>'=>'<controller>/index',

                //default  rules
                //'<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                //'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                '<controller:\w>'=>'<controller>/view',
            ],
        ],
        'user' => [
            'identityClass' => 'backend\models\User',
            'enableAutoLogin' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
