<?php
declare(strict_types=1);

namespace TestApp\Test\TestCase;

use ApiTokenAuthenticator\ApiTokenAuthenticatorPlugin;
use Cake\Core\Configure;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

class ApiTokenAuthenticatorPluginTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        
        // Set up a route for the login URL so Router::url() doesn't fail
        Router::createRouteBuilder('/')->scope('/', function (RouteBuilder $routes) {
            $routes->connect('/users/login', [
                'controller' => 'Users',
                'action' => 'login',
            ]);
        });
    }

    public function testGetAuthenticationService(): void
    {
        // Configure the plugin options
        Configure::write('ApiTokenAuthenticator', [
            'fields' => [
                'username' => 'email',
                'password' => 'password',
            ],
            'header' => 'Token',
            'passwordHasher' => [
                'className' => 'Default',
            ],
            'login' => [
                'controller' => 'Users',
                'action' => 'login',
                '_ext' => 'json',
            ],
        ]);

        $plugin = new ApiTokenAuthenticatorPlugin();
        $request = ServerRequestFactory::fromGlobals();
        
        // This should trigger the deprecation warnings for loadIdentifier()
        $service = $plugin->getAuthenticationService($request);
        
        $this->assertInstanceOf(
            'Authentication\AuthenticationServiceInterface',
            $service
        );
    }
}
