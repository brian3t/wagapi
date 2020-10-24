<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');
Yii::setAlias('@runnerScript', dirname(__DIR__) . '/yii');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'name' => 'Live N Out',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/console.log',
                    'levels' => ['error', 'warning'],
//                    'levels' => ['error', 'warning', 'profile'],
//                    'categories'=>['yii\db\*']
                ],
            ],
        ],
        'db' => $db,
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'session' => [
            'class' => 'yii\web\Session'
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
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
    'controllerMap' => [
        /*'cron' => [
            'class' => 'mitalcoi\cronjobs\CronController',
            'cronJobs' =>[
                'test/example1' => [
                    'cron'      => '* * * * *',
                ],

            ],
        ],*/
    ],
    'modules' => [
        'user' =>  Da\User\Module::class,
    ]
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
