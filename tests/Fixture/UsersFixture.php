<?php

namespace ApiTokenAuthenticator\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'email' => 'rrd',
            'password' => 'webmania',
            'token' => 'token-1',
            'created' => '2023-10-21 12:12:12',
            'modified' => '2023-10-21 12:12:12',
        ],
    ];
}
