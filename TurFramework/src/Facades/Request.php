<?php

namespace TurFramework\Facades;



/**
 * @method static bool is(string $url)
 * @method static mixed|null get(string $key, $default = null)
 * @method static bool has(string $key)
 * @method static bool isMethod(string $method)
 * @method static string|null previousUrl()
 * @method static array all()
 * @method static bool isPost()
 * @method static bool isGet()
 * @method static string getMethod()
 * @method static string|null previousUrlWithQuery()
 * @method static string fullUrlWithQuery()
 * @method static string fullUrl() 
 * @method static string getPath()
 * @method static array getValidMethods() 
 * @see \TurFramework\Http\HttpRequest
 */
class Request extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}
