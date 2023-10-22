<?php

declare(strict_types=1);

namespace TestApp\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = [
        'plugin.ApiTokenAuthenticator.Users',
    ];

    public function testIndex(): void
    {
        $this->configRequest([
            'headers' => ['Token' => 'token-1']
        ]);
        $this->get('/users.json');
        $this->assertResponseOk();
        $this->assertHeader('Content-Type', 'application/json');
        $users =  $this->viewVariable('users');
        $this->assertEquals('token-1', $users->toArray()[0]->token);
    }

    // public function testLogin(): void
    // {
    //     $this->post('/users/login.json', ['email' => 'rrd', 'password' => 'webmania']);
    //     $this->assertResponseOk();
    //     $this->assertHeader('Content-Type', 'application/json');
    //     $user =  $this->viewVariable('user');
    //     $this->assertEquals('token-1', $user->token);
    // }
}
