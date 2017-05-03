<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
       'cache' =>
       [
            'class' => 'yii\caching\FileCache',
            /*'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211,
                    'weight' => 100,
                ],
                /*[
                    'host' => 'server2',
                    'port' => 11211,
                    'weight' => 50,
                ],*/
            //],
        ],

        'urlManager' =>
        [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => false,
            'rules' =>
            [
                //seo url for story  on story/view
                //for backend when there is "language" param
                '<controller:\w+>/<seo_url:.*>/<type:\w+>/<name:[\w\-]+>/<id:\d+>/<categoryid:\d+>/<page:\d+>/<language:[\w+]{2}>'=>'<controller>/view',
                '<controller:\w+>/<seo_url:.*>/<type:\w+>/<name:[\w\-]+>/<id:\d+>/<categoryid:\d+>/<page:\d+>'=>'<controller>/view',
            ],
        ],
    ],
];
