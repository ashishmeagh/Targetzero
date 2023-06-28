<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'defaultRoute' => 'app-case',
    'components' => [
        'assetManager' => [      // To clear the css cache files and in assetmanager.php file -> $appendTimestamp set to true 
            'appendTimestamp' => true,
            ],
            'formatter' => [
                    'dateFormat' => 'dd.MM.yyyy',
                    'datetimeFormat' => 'd-M-Y H:i:s',
                    'timeFormat' => 'H:i:s',

                    'locale' => 'en-US', //your language locale
                    'defaultTimeZone' => 'America/Chicago', // time zone
               ],
            'request'      => [
                'enableCookieValidation' => TRUE,
                'enableCsrfValidation'   => TRUE,
                'cookieValidationKey'    => 'whiting_turner',
                // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
                'cookieValidationKey' => '4ifyEZJm4t_zOQrNvpGBdtsTBmFCrBP7',
            ],
            'response' => [
                'on beforeSend' => function($event) {
                    $event->sender->headers->add('X-Frame-Options', 'DENY');
                },
            ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,            
            'loginUrl' => ['userlogin/index'],
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
        // 'mailer'       => [
                
        //         'class' => \yii\symfonymailer\Mailer::class,
        //         //'useFileTransport' => FALSE,
        //         'transport'        => [
        //             'scheme'      => 'smtps',
        //             'host'       => 'us-smtp-o365-outbound-1.mimecast.com',
        //             'username'   => 'no-reply@whiting-turner.com',
        //             'password'   => 'Target@2020',
        //             'port'       => '465',
        //             //'encryption' => 'tls',
        //             'dsn' => 'native://default',
                    
        //         ],
        //         //'viewPath' => '@common/mail',
        //         // send all mails to a file by default. You have to set
        //         // 'useFileTransport' to false and configure transport
        //         // for the mailer to send real emails.
        //         'useFileTransport' => false,
        //     ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            //'useFileTransport' => false,
            'transport' => [
                'dsn' => 'smtp://no-reply@whiting-turner.com:Target@2020@us-smtp-o365-outbound-1.mimecast.com:25',
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
        'assetManager' => [
                'bundles'   => [
                    'yii\web\JqueryAsset'                => ['sourcePath'=>null, 'js'=>[], 'css'=>[]],
                    'yii\bootstrap\BootstrapPluginAsset' => ['sourcePath'=>null, 'js'=>[], 'css'=>[]],
                    'yii\bootstrap\BootstrapAsset'       => ['sourcePath'=>null, 'js'=>[], 'css'=>[]],
                ],
                'forceCopy' => FALSE,
        ],
        'db' => $db,
        'urlManager'   => [
            'class'           => 'yii\web\UrlManager',
            'showScriptName'  => FALSE,
            'enablePrettyUrl' => TRUE,
            'rules'           => array(
                '/' => 'app-case/index',
                '<controller:\w+/?>' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
            ),
    ],
        'saml' => [
            'class' => 'asasmoyo\yii2saml\Saml',
            'configFileName' => '@app/config/saml.php', // OneLogin_Saml config file (Optional)
            
        ]


    ],
    'params' => $params,
];
 
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
