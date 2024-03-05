<?php

namespace TurFramework\Router;

use TurFramework\Facades\Cache;

class RouteFileRegistrar
{

    /**
     * The router instance.
     *
     * @var \TurFramework\Router\Router
     */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    /**
     * Require the given routes file.
     *
     * @param    $routes
     * @return void
     */
    public function register($routes)
    {
        require $routes;
    }


    public function load(array $routes)
    {
        $this->loadRoutes($routes);
    }

    public function loadAllRoutes($routesPath)
    {
        $this->loadRoutes($this->getRoutesFiles($routesPath));
    }


    private function registerRoutes($routes)
    {
        foreach ($routes as $key => $routeFile) {
            $this->register($routeFile);
        }
    }

    private function loadRoutes($routes)
    {

        if ($this->routesAreCached()) {
            $this->router->getRouteCollection()
                ->getRoutesFromCache($this->loadCachedRoutes());
        } else {

            $this->registerRoutes($routes);

            // Cache::store($this->getCachedRoutesPath(), $this->router->getRouteCollection()->getRoutes());
        }
    }
    protected function getCachedRoutesPath()
    {
        return 'cache/routes.php';
    }

    /**
     * Determine if the application routes are cached.
     *
     * @return bool
     */
    protected function routesAreCached()
    {
        return Cache::exists($this->getCachedRoutesPath());
    }
    /**
     * Load the cached routes for the application.
     *
     * @return void
     */
    protected function loadCachedRoutes()
    {

        return Cache::loadFile($this->getCachedRoutesPath());
    }

    protected function getRoutesFiles($routesPath)
    {
        return get_files_in_directory($routesPath);
    }
}
