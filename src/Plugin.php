<?php

namespace ApiTokenAuthenticator;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Core\BasePlugin;
use Cake\Http\MiddlewareQueue;
use Cake\Console\CommandCollection;
use Authentication\AuthenticationService;
use Cake\Core\PluginApplicationInterface;
use Psr\Http\Message\ServerRequestInterface;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Authentication\AuthenticationServiceProviderInterface;

class Plugin extends BasePlugin implements AuthenticationServiceProviderInterface
{
    // TODO disable CSRF middleware - now it is manually in Application.php
    public function middleware(MiddlewareQueue $middleware): MiddlewareQueue
    {
        // Add middleware here.
        $middleware = parent::middleware($middleware)
            // if we want to use Authorization plugin along with this, than Authentication middleware should be BEFORE the Authorization middleware
            ->insertAfter(
                'Cake\Http\Middleware\BodyParserMiddleware',
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

        $options = Configure::read('ApiTokenAuthenticator');

        $fields = [
            IdentifierInterface::CREDENTIAL_USERNAME => $options['fields']['username'],
            IdentifierInterface::CREDENTIAL_PASSWORD => $options['fields']['password']
        ];

        $service->loadAuthenticator('Authentication.Token', [
            'header' => $options['header'],
        ]);
        $service->loadIdentifier('Authentication.Token');

        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => Router::url([
                'prefix' => false,
                'plugin' => null,
                'controller' => $options['login']['controller'],
                'action' => $options['login']['action'],
            ]),
        ]);

        if ($options['passwordHasher'] == 'default') {
            $service->loadIdentifier('Authentication.Password', compact('fields'));
        }
        if (is_array($options['passwordHasher'])) {
            $service->loadIdentifier(
                'Authentication.Password',
                [
                    'fields' => $fields,
                    'passwordHasher' => $options['passwordHasher']
                ]
            );
        }

        return $service;
    }
}
