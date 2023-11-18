<?php

namespace TurFramework\Core\Router;

use TurFramework\Core\Http\Request;
use TurFramework\Core\Http\Response;

class Route
{
    private Response $response;
    private Request $request;

    public static $routes = [];
    public $routesFiles = [];
    protected static $currentAction = '';

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public static function addRoute($method, $route, $callable)
    {
        self::$routes[$method][$route] = $callable;
    }

    /**
     * get.
     *
     * @param string route
     * @param array|callable callable
     *
     * @return void
     */
    public static function get(string $route, array|callable $callable)
    {
        self::addRoute(Request::METHOD_GET, $route, $callable);
    }

    /**
     * post.
     *
     * @param string route
     * @param array|callable callable
     *
     * @return void
     */
    public static function post(string $route, array|callable $callable)
    {
        self::addRoute(Request::METHOD_POST, $route, $callable);
    }

    /**
     * reslove.
     *
     * @return void
     */
    public function reslove()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        $callable = self::$routes[$method][$path] ?? false;

        $this->handleAction($callable);
    }

    /**
     * handleAction.
     *
     * @param mixed action
     *
     * @return void
     */
    private function handleAction($action)
    {
        if (!$action) {
            throw new RouteNotFoundException();
        }

        if (is_callable($action)) {
            call_user_func_array($action, []);
        }

        if (is_array($action)) {
            $controllerClass = $action[0];
            $controllerMethod = $action[1];

            if (!class_exists($controllerClass)) {
                throw new ControllerNotFoundException("Target class [$controllerClass] does not exist");
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $controllerMethod)) {
                throw new \BadMethodCallException("Method  $controllerClass::$controllerMethod  does not exist!");
            }

            call_user_func_array([$controller, $controllerMethod], []);
        }
    }
}