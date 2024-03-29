<?php

namespace TurFramework\Router;

use TurFramework\Router\Exceptions\RouteException;

class MiddlewareResolver
{

    public static function handle($globalMiddleware, $routeMiddleware, $route, $request)
    {
        $instance = new self();


        $instance->resolveGlobalMiddleware($globalMiddleware, $request);

        if (!is_null($route['middleware'])) {
            $instance->resolveRouteMiddleware($route, $routeMiddleware, $request);
        }
    }
    private function resolveRouteMiddleware($route, $routeMiddleware, $request)
    {
        $params = [];

        foreach ($route['middleware'] as  $middleware) {

            if (str_contains($middleware, ':')) {
                [$middleware, $params] = explode(':', $middleware);
            }

            if (!isset($routeMiddleware[$middleware])) {
                throw RouteException::targetClassDoesNotExist($middleware);
            }


            $middlewareClass = app()->make($routeMiddleware[$middleware]);

            if (!method_exists($middlewareClass, 'handle')) {
                throw RouteException::methodDoesNotExist($routeMiddleware[$middleware], 'handle');
            }

            $middlewareClass->handle($request, $params);
        }
    }
    private function resolveGlobalMiddleware($globalMiddleware, $request)
    {
        foreach ($globalMiddleware as $value) {

            $middlewareClass = app()->make($value);
            if (!method_exists($middlewareClass, 'handle')) {
                throw RouteException::methodDoesNotExist($value, 'handle');
            }

            $middlewareClass->handle($request);
        }
    }
}
