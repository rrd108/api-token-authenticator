<?php

namespace ApiTokenAuthenticator;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

class Plugin extends BasePlugin implements AuthenticationServiceProviderInterface
{
    // TODO disable CSRF middleware - now it is manually in Application.php
    public function middleware(MiddlewareQueue $middleware): MiddlewareQueue
    {
        // Add middleware here.
        $middleware = parent::middleware($middleware)
            // if we want to use Authorization plugin along with this, than Authentication middleware should be BEFORE the Authorization middleware
          ->insertAfter(
              'Cake\Routing\Middleware\RoutingMiddleware',
              new AuthenticationMiddleware($this)
          );

        return $middleware;
    }

    public function console(CommandCollection $commands): CommandCollection
    {
        // Add console commands here.
        $commands = parent::console($commands);

        return $commands;
    }

    public function bootstrap(PluginApplicationInterface $app): void
    {
        // Add constants, load configuration defaults.
        // By default will load `config/bootstrap.php` in the plugin.
        parent::bootstrap($app);
    }

    public function routes($routes): void
    {
        // Add routes.
        // By default will load `config/routes.php` in the plugin.
        parent::routes($routes);
    }

    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();

        $fields = [
            IdentifierInterface::CREDENTIAL_USERNAME => 'email',
            IdentifierInterface::CREDENTIAL_PASSWORD => 'password'
        ];

        $service->loadAuthenticator('Authentication.Token', [
            'header' => 'Token',
        ]);
        $service->loadIdentifier('Authentication.Token');

        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => Router::url([
                'prefix' => false,
                'plugin' => null,
                'controller' => 'Users',
                'action' => 'login.json',
            ]),
        ]);
        $service->loadIdentifier(
            'Authentication.Password',
            [
            'fields' => $fields,
            'passwordHasher' => [
                'className' => 'Authentication.Fallback',
                'hashers' => [
                    'Authentication.Default',
                    [
                        'className' => 'Authentication.Legacy',
                        'hashType' => 'md5',
                        'salt' => false // turn off default usage of salt
                    ],
                ]
            ]
            ]
        );
        
        return $service;
    }
}
