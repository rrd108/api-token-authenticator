<?php

declare(strict_types=1);

namespace TestApp;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;
use Cake\Core\ContainerInterface;
use Cake\Routing\Route\DashedRoute;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

class Application extends BaseApplication
{

    public function routes(RouteBuilder $routes): void
    {
        $routes->setRouteClass(DashedRoute::class);
        $routes->scope('/', function (RouteBuilder $builder): void {
            $builder->setExtensions(['json']);
            $builder->resources('Users');
            $builder->fallbacks();
        });
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue->add(new RoutingMiddleware($this))
            ->add(new BodyParserMiddleware());;
    }

    public function services(ContainerInterface $container): void
    {
    }
}
