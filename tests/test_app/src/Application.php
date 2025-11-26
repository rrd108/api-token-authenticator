<?php
declare(strict_types=1);

namespace TestApp;

use ApiTokenAuthenticator\ApiTokenAuthenticatorPlugin;
use Cake\Core\ContainerInterface;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

class Application extends BaseApplication
{
    public function routes(RouteBuilder $routes): void
    {
        $routes->setRouteClass(DashedRoute::class);
        $routes->scope(
            '/',
            function (RouteBuilder $builder): void {
                $builder->setExtensions(['json']);
                $builder->resources('Users');
                $builder->fallbacks();
            }
        );
    }

    public function bootstrap(): void
    {
        parent::bootstrap();
        $this->addPlugin(ApiTokenAuthenticatorPlugin::class);
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue->add(new RoutingMiddleware($this))
            ->add(new BodyParserMiddleware());

        // Add plugin middleware
        $middlewareQueue = $this->pluginMiddleware($middlewareQueue);

        return $middlewareQueue;
    }

    public function services(ContainerInterface $container): void
    {
    }
}
