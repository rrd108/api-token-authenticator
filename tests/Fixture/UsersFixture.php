<?php

namespace ApiTokenAuthenticator\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public $records = [
        [
            'id' => 1,
            'email' => 'rrd',
            'password' => 'webmania',
            'token' => 'token-1',
            'created' => '2023-05-30 18:00:00',
            'modified' => '2023-05-30 18:00:00',
        ],
    ];
}
