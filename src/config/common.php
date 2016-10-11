<?php
$envConf = \app\Environment::get();

return [
    'id' => 'neroblu',
    'language' => 'ja',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'runtimePath' => dirname(dirname(__DIR__)) . '/runtime',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'fileCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => __DIR__ . '/../../runtime/cache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $envConf['db']['host'] . ';dbname=' . $envConf['db']['database'],
            'username' => $envConf['db']['username'],
            'password' => $envConf['db']['password'],
            'enableSchemaCache' => YII_DEBUG ? false : true,
            'schemaCache' => 'fileCache',
            'charset' => 'utf8',

        ],
        'sessionDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $envConf['sessionDb']['host'] . ';dbname=' . $envConf['sessionDb']['database'],
            'username' => $envConf['sessionDb']['username'],
            'password' => $envConf['sessionDb']['password'],
            'enableSchemaCache' => YII_DEBUG ? false : true,
            'schemaCache' => 'fileCache',
            'charset' => 'utf8',

        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            'db' => 'sessionDb',
            'sessionTable' => 'session',
        ],
    ],
    'params' => [
        'appHost' => $envConf['appHost'],
        'supportTo' => $envConf['supportTo'],
        'batchTo' => $envConf['batchTo'],
    ],
];
