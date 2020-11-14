<?php

use kartik\datecontrol\Module;

$params = require(__DIR__ . '/params.php');
// other settings
// format settings for displaying each date attribute (ICU format example)
$params['dateControlDisplay'] = [
    Module::FORMAT_DATE => 'MM-dd-yyyy',
    Module::FORMAT_TIME => 'hh:mm:ss a',
    Module::FORMAT_DATETIME => 'MM-dd-yyyy hh:mm:ss a',
];
// format settings for saving each date attribute (PHP format example)
$params['dateControlSave'] = [
    Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
    Module::FORMAT_TIME => 'php:H:i:s',
    Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
];
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'name' => 'NFL Fan Wager',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'H_dEJQboi7yyZVoQcwS8xfy6f_4W8-aB',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'someids@gmail.com',
                'password' => 'sTrapok02',
                'port' => 587,
                'encryption' => 'tls',
            ],
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/user' => 'baseuser/index',
            ],
        ],
        'consoleRunner' => [
            'class' => 'vova07\console\ConsoleRunner',
            'file' => '@my/path/to/yii' // or an absolute path to console file
        ],
        'formatter' => [
            'class' => 'usv\yii2helper\i18n\ViewFormatter',
            'nullDisplay' => '',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@Da/User/resources/views' => '@app/views/user'
                ]
            ]
        ]

    ],
    'modules' => [
        /*'user' => [
            'class' => 'dektrium\user\Module',
                    'on afterLogout' => function ($e) {
                        Yii::$app->getSession()->addFlash('success', 'You have logged out successfully');
                    },
            'enableFlashMessages' => true,
        ],*/
        'user' => [
            'class' => Da\User\Module::class,
            // ...other configs from here: [Configuration Options](installation/configuration-options.md), e.g.
             'administrators' => ['admin','ngxtri','windlinx@hotmail.com'], // this is required for accessing administrative actions
            // 'generatePasswords' => true,
            // 'switchIdentitySessionKey' => 'myown_usuario_admin_user_key',
            // docs: https://yii2-usuario.readthedocs.io/en/latest/installation/available-actions/
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'datecontrol' => [
            'class' => '\kartik\datecontrol\Module',
            // see settings on http://demos.krajee.com/datecontrol#module
            // format settings for saving each date attribute (PHP format example)
            // format settings for displaying each date attribute (ICU format example)
            'displaySettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => 'Y-m-d',
                \kartik\datecontrol\Module::FORMAT_TIME => 'HH:mm:ss a',
                \kartik\datecontrol\Module::FORMAT_DATETIME => 'dd-MM-yyyy HH:mm:ss a',
            ],

            // use ajax conversion for processing dates from display format to save format.
            'ajaxConversion' => true,

            'saveSettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => 'php:Y-m-d', // saves as unix timestamp
                \kartik\datecontrol\Module::FORMAT_TIME => 'php:H:i:s',
                \kartik\datecontrol\Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

        ],
        // If you use tree table
        'treemanager' => [
            'class' => '\kartik\tree\Module',
            // see settings on http://demos.krajee.com/tree-manager#module
        ],
    ],

    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    // $config['bootstrap'][] = 'debug';
    // $config['modules']['debug'] = [
    //     'class' => 'yii\debug\Module',
    // ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '10.0.*', '192.168.*', '12.22.200.*', '209.242.153.*', '12.35.128.*'],
    ];
}

return $config;
