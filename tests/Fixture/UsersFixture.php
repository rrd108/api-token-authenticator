<?php

namespace ApiTokenAuthenticator\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'email' => 'rrd',
            'password' => '$2y$10$ZVRmoo2amhyBLFqz/Qoo8OsqbGDXs95AdQM/gmLSJ2BghRSWdmRDS', // 123
            'token' => 'token-1',
            'created' => '2023-10-21 12:12:12',
            'modified' => '2023-10-21 12:12:12',
        ],
        [
            'id' => 2,
            'email' => 'rrdExpired',
            'password' => '$2y$10$ZVRmoo2amhyBLFqz/Qoo8OsqbGDXs95AdQM/gmLSJ2BghRSWdmRDS', // 123
            'token' => 'token-2',
            'token_expiration' => '2023-12-12 12:12:12',
            'created' => '2023-10-21 12:12:12',
            'modified' => '2023-10-21 12:12:12',
        ],
    ];
}
