<?php
declare(strict_types=1);

namespace ApiTokenAuthenticator;

use ApiTokenAuthenticator\Authentication\Authenticator\ProvisoryTokenAuthenticator;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\AbstractIdentifier;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

class ApiTokenAuthenticatorPlugin extends BasePlugin implements AuthenticationServiceProviderInterface
{
    // TODO disable CSRF middleware - now it is manually in Application.php

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        // Add console commands here.
        $commands = parent::console($commands);

        return $commands;
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        // Add constants, load configuration defaults.
        // By default will load `config/bootstrap.php` in the plugin.
        parent::bootstrap($app);
    }

    /**
     * @inheritDoc
     */
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

        // Both authenticator and identifier should use the same field mapping
        // The 'username' key maps to the database column name (e.g., 'email')
        $fields = [
            AbstractIdentifier::CREDENTIAL_USERNAME => $options['fields']['username'],
            AbstractIdentifier::CREDENTIAL_PASSWORD => $options['fields']['password'],
        ];

        // ProvisoryTokenAuthenticator with Token identifier
        $tokenIdentifier = [
            'Authentication.Token' => [],
        ];
        $service->loadAuthenticator(
            ProvisoryTokenAuthenticator::class,
            [
                'header' => $options['header'],
                'identifier' => $tokenIdentifier,
            ]
        );

        // Build identifier config for Form authenticator
        $passwordIdentifierConfig = [
            'fields' => $fields,
        ];
        if (is_array($options['passwordHasher'])) {
            $passwordIdentifierConfig['passwordHasher'] = $options['passwordHasher'];
        }
        $passwordIdentifier = [
            'Authentication.Password' => $passwordIdentifierConfig,
        ];

        $service->loadAuthenticator(
            'Authentication.Form',
            [
                'fields' => $fields,
                'identifier' => $passwordIdentifier,
                'loginUrl' => Router::url(
                    [
                        'prefix' => false,
                        'plugin' => null,
                        'controller' => $options['login']['controller'],
                        'action' => $options['login']['action'],
                        '_ext' => $options['login']['_ext'],
                    ]
                ),
            ]
        );

        return $service;
    }
}
