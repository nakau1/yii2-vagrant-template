<?php
//    'dsn' => 'mysql:host=demo-daifuku.c5ssjo3sunyf.ap-northeast-1.rds.amazonaws.com;dbname=daifuku',

return [
    // 環境モード
    'mode' => 'demo',
    // DB
    'db' => [
        'host' => 'demo-daifuku.c5ssjo3sunyf.ap-northeast-1.rds.amazonaws.com',
        'database' => 'daifuku',
        'username' => 'daifuku',
        'password' => 'dorubakodorubako',
    ],
    'sessionDb' => [
        'host' => 'demo-daifuku.c5ssjo3sunyf.ap-northeast-1.rds.amazonaws.com',
        'database' => 'session',
        'username' => 'daifuku',
        'password' => 'dorubakodorubako',
    ],
    'appHost' => 'pollet.vagrant.net',
    'supportTo' => 'ueda@tech-vein.com',
    'batchTo' => 'ueda@tech-vein.com',
];