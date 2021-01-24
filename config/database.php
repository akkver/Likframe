<?php declare(strict_types=1);


return [
    'default' => 'mysql_one',
    'connections' => [
        'mysql_one' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'dbname' => 'crm_user',
            'username' => 'root',
            'password' => '1234',
            'prefix' => '',
            'options' => []
        ]
    ]
];
