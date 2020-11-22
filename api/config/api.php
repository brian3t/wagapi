<?php

$db = require(dirname(dirname(__DIR__)) . '/config/db.php');
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'name' => 'Sme',
    // Need to get one level up:
    'basePath' => dirname(dirname(__DIR__)),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            // Enable JSON Input:
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'log' => [
//            'traceLevel' => YII_DEBUG ? 3 : 0,
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    // Create API log in the standard log dir
                    // But in file 'api.log':
                    'logFile' => '@app/runtime/logs/api.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['v1/user', 'v1/game'],
                    'patterns' => ['PUT,PATCH {id}' => 'update', 'DELETE {id}' => 'delete', 'GET,HEAD {id}' => 'view', 'POST' => 'create', 'GET,HEAD' => 'index', '{id}' => 'options'
                        , '' => 'options'],
                    // 'extraPatterns' => [
                    // ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{page}' => '<page:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/order',
                    'extraPatterns' => [
                        'POST signup' => 'signup',
                        'POST signin' => 'signin',
                        'GET search_by_company' => 'search_by_company',
                    ],
                ],
                // [
                //     '<controller:(catalog|inventory|order)>_<action:(pull)>_v1' => 'v1/<controller>/<action>',
                //     // 'posts' => 'post/index',
                //     // 'post/<id:\d+>' => 'post/view',
                // ]
            ]
        ],
        'db' => $db,
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
    ],
    'modules' => [
        'user' => [
            'class' => Da\User\Module::class,
            // ...other configs from here: [Configuration Options](installation/configuration-options.md), e.g.
            'administrators' => ['admin', 'ngxtri', 'windlinx@hotmail.com'], // this is required for accessing administrative actions
            'controllerMap' => [
                'security' => [
                    'class' => 'app\controllers\user\SecurityController'
                    ,
                    'on afterLogout' => function ($e) {
                        Yii::$app->getSession()->addFlash('success', 'You have logged out successfully');
                    }
                ],
                'settings' => 'app\controllers\user\SettingsController'
            ],
            'enableFlashMessages' => false
            // 'modelMap' => [
            //     'User' => 'app\models\User',
            // ],
        ],
        'v1' => [
            'class' => 'app\api\modules\v1\Module',
            'basePath' => '@app/api/modules/v1',
        ],
    ],
    'params' => $params,
];

return $config;
