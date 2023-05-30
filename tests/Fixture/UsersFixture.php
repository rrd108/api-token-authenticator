<?php

namespace ApiTokenAuthenticator\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    /**
     * Fields property
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'autoIncrement' => true, 'null' => false],
        'email' => ['type' => 'string', 'length' => 255, 'null' => false],
        'password' => ['type' => 'string', 'length' => 255, 'null' => false],
        'token' => ['type' => 'string', 'length' => 255, 'null' => false],
        'created' => 'datetime',
        'modified' => 'datetime',
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    /**
     * Records property
     *
     * @var array
     */
    public $records = [
        [
            'email' => 'rrd',
            'password' => 'webmania',
            'token' => 'token-1',
            'created' => '2023-05-30 18:00:00',
            'modified' => '2023-05-30 18:00:00',
        ],
    ];
}
