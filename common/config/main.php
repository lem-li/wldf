<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['log'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info','error'],
                    'categories' => ['yii\*'],
                ],
//                'db' => [
//                    'class' => 'yii\log\DbTarget',
//                    'levels' => ['error', 'warning'],
//                ]
            ],
        ]
    ],
];
