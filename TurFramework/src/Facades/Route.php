<?php

namespace TurFramework\Facades;


/**
 * @method static \TurFramework\Router\Router get(string $uri, string|array|Closure $action = null)
 * @method static \TurFramework\Router\Router post(string $uri, array|string|callable|null $action = null)
 * @method static \TurFramework\Router\Router delete(string $uri, array|string|callable|null $action = null)
 * @method static \TurFramework\Router\Router controller($controller) 
 * @method static \TurFramework\Router\Router group($routes) 
 * @method static \TurFramework\Router\Router name(string $name) 
 * @method \TurFramework\Router\Router middleware(array|string $middleware)
 * @see \TurFramework\Router\Router
 */
class Route extends Facade
{


    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}