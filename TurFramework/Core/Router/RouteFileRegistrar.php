<?php

namespace TurFramework\Core\Router;

use TurFramework\Core\Facades\Cache;
use TurFramework\Core\Router\Exceptions\RouteException;

class RouteFileRegistrar
{

    /**
     * The router instance.
     *
     * @var \TurFramework\Core\Router\Router
     */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    /**
     * Loads routes.
     * If cached file exists, loads from cache, otherwise loads route files and creates a cache file.
     * @return $routes
     */
    public function loadRotues()
    {
        if ($this->routesAreCached()) {
            $this->loadCachedRoutes();
        } else {

            $this->loadRoutesFiles();

            // After loading, create a cache file for routes
            // Cache::store($this->getCachedRoutesPath(), $this->router->routes->getRoutes());
        }
    }

    /**
     * Loads route files from the 'app/routes' directory.
     * Throws an exception if no route files are found.
     */
    protected function loadRoutesFiles()
    {

        if (empty($this->getRoutesFiles())) {
            throw RouteException::routeFilesNotFound();
        }


        foreach ($this->getRoutesFiles() as $routeFile) {
            require_once $routeFile;
        }
    }

    protected function getCachedRoutesPath()
    {
        return 'bootstrap/cache/routes.php';
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


    /**
     * Require the given routes file.
     *
     * @param  string  $routes
     * @return void
     */
    public function register($routeFile)
    {
        return require_once $routeFile;
    }
    protected function getRoutesFiles()
    {
        return get_files_in_directory('app/routes');
    }
}
