<?php

namespace TurFramework\Core\Router;

use Error;
use Closure;
use TypeError;
use ErrorException;
use InvalidArgumentException;

use TurFramework\Core\Facades\Request;
use TurFramework\Core\Exceptions\BadMethodCallException;
use TurFramework\Core\Facades\Cache;

class Router
{

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    /**
     * The Request object used to handle HTTP requests.
     *
     * @var Request
     */
    private Request $request;

    /**
     * route key
     *
     * @var array
     */
    public  $route;

    /**
     * An array to store route files loaded for caching.
     *
     * @var array
     */
    private $routesFiles = [];
    /**
     * 
     *
     * @var string
     */
    private $requestMethod;


    /**
     * A look-up table of routes by their names.
     *
     * @var \Illuminate\Routing\Route[]
     */
    protected $nameList = [];
    /**
     * 
     *
     * @var string
     */
    private $path;

    /**
     * The currently active controller.
     *
     * @var mixed
     */
    public  $controller;

    /**
     * An array containing registered routes.
     *
     * @var array
     */
    public  $routes = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Resolve the current request to find and handle the appropriate route.
     * 
     */
    public function resolve()
    {
        $this->path = $this->request->getPath();
        $this->requestMethod = $this->request->getMethod();

        //  match the incoming URL to the defined routes 
        $route = $this->matchRoute($this->path);


        $this->handleRoute($route);
    }

    /**
     * Create a route group 
     *
     * @param callable $callback
     *
     * @return $this
     */
    public  function group(callable $callback)
    {

        $callback();

        return $this;
    }

    /**
     * Create a new instance of the Route class and set the current controller.
     *
     * @param string $controller the name of the controller
     *
     * @return $this
     */
    public function controller(string $controller)
    {
        $this->controller = $controller;
        return  $this;
    }

    /**
     * Register a GET route with the specified route and associated callback.
     *
     * @param string $route the URL pattern for the route
     * @param string|array|Closure $callable the callback function or controller action for the route
     *
     * @return $this
     */
    public function get($route, $callable)
    {


        return $this->addRoute(self::METHOD_GET, $route, $callable);
    }

    /**
     * Register a POST route with the specified route and associated callback.
     *
     * @param string  route the URL pattern for the route
     * @param string|array|Closure $callable the callback function or controller action for the route
     *
     * @return  $this;
     */
    public  function post($route,  $callable)
    {

        return  $this->addRoute(self::METHOD_POST, $route, $callable);
    }

    /**
     * Register a Delete route with the specified route and associated callback.
     *
     * @param string  route the URL pattern for the route
     * @param string|array|Closure $callable the callback function or controller action for the route
     *
     * @return  $this;
     */
    public  function delete($route,  $callable)
    {
        return $this->addRoute(self::METHOD_DELETE, $route, $callable);
    }

    /**
     * Add or change the route name.
     *
     * @param  string  $name
     * 
     * @return  $this;
     */
    public  function name(string $routeName)
    {

        $this->setRouteName($routeName);

        return $this;
    }

    /**
     * Add or change the route name.
     *
     * @param  string  $name
     * 
     * @return array|null
     */
    public function getByName($routeName): array|null
    {
        return $this->nameList[$routeName] ?? null;
    }

    private function setRouteName(string $routeName)
    {
        return $this->routes[$this->route]['name'] = $routeName;
    }
    /**
     * Retrieve the route handler associated with the given method and path.
     *
     * @param string $method The HTTP method of the route (e.g., GET, POST).
     * @param string $path The URL path of the route.
     * @return mixed|false The route handler associated with the route or false if not found.
     */
    public function matchRoute($path)
    {
        $handler = null;


        foreach ($this->routes as $route => $routeDetails) {

            // Replace route parameters with regex patterns to match dynamic values
            $pattern = preg_replace_callback('/\{(\w+)\}/', function ($matches) {

                return '(?P<' . $matches[1] . '>[^/]+)';
            }, $route);

            $pattern = str_replace('/', '\/', $pattern);

            $pattern = '/^' . $pattern . '$/';

            // Check if the requested path matches the route pattern
            if (preg_match($pattern, $path, $matches)) {

                $handler = $routeDetails;

                // Store route parameters and their values
                $handler['parameters'] = array_intersect_key($matches, array_flip($handler['parameters']));

                break;
            }
        }


        return $handler;
    }



    /**
     * Add a route to the internal routes collection for a specific HTTP method.
     *
     * @param string $method The HTTP method (GET, POST, etc.) for the route.
     * @param string $route The URL pattern for the route.
     * @param string|array|Closure $callable The callback function or controller action for the route.
     * @return  $this;
     */
    private function addRoute($method, $route, $callable, $name = null)
    {

        $this->route = $route;

        $this->routes[$route] = $this->createNewRoute($method, $route, $this->getCallable($callable), $name);

        return  $this;
    }
    /**
     * Creates a new route array based on the provided method, route, and callable.
     *
     * @param string $method   HTTP method (e.g., GET, POST, etc.).
     * @param string $route    URI pattern for the route.
     * @param array  $callable Array containing controller and action information.
     *
     * @return array Returns an array representing the new route.
     */
    private function createNewRoute($method, $route, $callable, $name = null)
    {
        return  [
            'uri' => $route,
            'method' => $method,
            'controller' => $callable['controller'],
            'action' =>  $callable['action'],
            'parameters' => $this->extractParametersFromRoute($route),
            'name' => $name,
        ];
    }

    /**
     * Extracts parameters from the provided route URI pattern.
     *
     * @param string $route URI pattern for the route.
     *
     * @return array Returns an array containing the extracted parameters.
     */
    private  function extractParametersFromRoute($route)
    {
        $parameters = [];
        $routeParts = explode('/', $route);

        foreach ($routeParts as $part) {
            // Checks if the part of the route is a parameter placeholder in the form of {param}
            if (strpos($part, '{') === 0 && strpos($part, '}') === strlen($part) - 1) {
                $parameters[] = substr($part, 1, -1); // Extracts the parameter name without braces
            }
        }

        return  $parameters; // Returns an array of extracted parameters
    }

    /**
     * Determines the callable format based on the input and returns it as an array.
     *
     * @param mixed $callable Callable associated with the route.
     *
     * @return array|null Returns an array containing controller and action, or null if the format is not recognized.
     */
    private  function getCallable($callable)
    {
        if (!is_null($this->controller) && is_string($callable)) {
            return ['controller' =>  $this->controller, 'action' =>  $callable];
        }

        if (is_array($callable)) {
            return ['controller' =>  $callable[0], 'action' =>  $callable[1]];
        }

        if (is_callable($callable)) {
            return ['controller' => null, 'action' => $callable];
        }
    }
    /**
     * Handle the resolved action (callable or controller method) based on the route.
     *
     * @param mixed $action The action associated with the resolved route.
     * @return void
     *
     * @throws RouteNotFoundException If no action is found for the route.
     * @throws ControllerNotFoundException If the specified controller class does not exist.
     * @throws \BadMethodCallException If the controller method does not exist.
     */
    private function handleRoute($route)
    {

        // Check if the route method is not allowed
        if ($this->isMethodNotAllowedForRoute($route)) {
            throw new MethodNotAllowedHttpException($this->requestMethod, $this->path, $route['method']);
        }

        // Check if no action is associated with the route, throw RouteNotFoundException.
        if (is_null($route)) {
            throw new RouteNotFoundException();
        }

        // If the action is a callable function, execute it
        if (is_callable($route['action'])) {
            $this->invokeControllerMethod($route['action'], $route['parameters']);
            return;
        }

        // Extract controller class and method from the route
        $controllerClass = $route['controller'];
        $controllerMethod = $route['action'];


        // Check if the controller class exists
        if ($this->isControllerNotExists($controllerClass)) {
            throw new ControllerNotFoundException("Target class [$controllerClass] does not exist");
        }

        $controller = new $controllerClass();

        // Check if the method exists in the controller
        if ($this->isMethodNotExistsInController($controller, $controllerMethod)) {
            throw new \BadMethodCallException("Method  $controllerClass::$controllerMethod  does not exist!");
        }

        // Invoke the controller method
        $this->invokeControllerMethod([$controller, $controllerMethod], $route['parameters']);
    }

    private function invokeControllerMethod($callable, $parameters)
    {

        return  call_user_func_array($callable,  [$this->request, ...$parameters]);
    }
    // Method to check if the requested method matches the route method
    private function isMethodNotAllowedForRoute($route)
    {

        if (!is_null($route)  && $route['method'] !== $this->requestMethod) {
            return true;
        }

        return false;
    }

    /**
     * Get all registered routes.
     *
     * @return array All registered routes.
     */
    public function getRoutes()
    {

        return $this->routes;
    }
    /**
     * Get all registered routes by names.
     *
     * @return array All registered routes.
     */
    public function getNameList()
    {
        return $this->nameList;
    }



    /**
     * Loads route files from the 'app/routes' directory.
     * Throws an exception if no route files are found.
     */
    public function loadRoutesFiles()
    {
        $routesFiles = get_files_in_directory('app/routes');

        if (empty($routesFiles)) {
            throw new RouteNotFoundException('No route files found');
        }

        foreach ($routesFiles as $routeFile) {
            require_once $routeFile;
        }
    }


    /**
     * Loads routes.
     * If cached file exists, loads from cache, otherwise loads route files and creates a cache file.
     * @return $this
     */
    public function loadRotues()
    {
        $routesCacheFile = base_path('bootstrap/cache/routes.php');

        if (file_exists($routesCacheFile)) {
            $this->routes =  Cache::loadCachedFile($routesCacheFile);
        } else {
            $this->loadRoutesFiles();

            // After loading, create a cache file for routes
            Cache::cacheFile($routesCacheFile, $this->routes);
        }

        $this->loadRoutesByNames();
        return $this;
    }

    /**
     * Loads routes by their names into a separate list.
     */
    private function loadRoutesByNames()
    {
        foreach ($this->routes as $route => $routeDetails) {
            if (!is_null($routeDetails['name'])) {
                $this->nameList[$routeDetails['name']] = $routeDetails;
            }
        };
    }

    /**
     * Checks if the controller class doesn't exist.
     * @param string|null $controllerClass Name of the controller class.
     * @return bool
     */
    private function isControllerNotExists($controllerClass)
    {
        return !is_null($controllerClass) && !class_exists($controllerClass);
    }

    /**
     * Checks if the method doesn't exist in the controller.
     * @param object $controller Controller object.
     * @param string $methodName Name of the method.
     * @return bool
     */
    private function isMethodNotExistsInController($controller, $methodName)
    {
        return !method_exists($controller, $methodName);
    }
}
