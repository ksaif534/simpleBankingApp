<?php
require_once __DIR__.'/../../vendor/autoload.php';

return [
    'type' => [
        'database'  => [
            'mysql'     => [
                'dsn'       => 'mysql:host=localhost;dbname=simple_banking_app;port=3306',
                'username'  => 'sk123',
                'password'  => 'Sk123&*('
            ]
        ],
        'file'      => [
            'users'         => dirname(__DIR__,2).'/src/files/users.txt',
            'transactions'  => dirname(__DIR__,2).'/src/files/transactions.txt',
            'admin-cli'     => dirname(__DIR__,2).'/src/files/admin-cli.txt'
        ]
    ]
];

?>