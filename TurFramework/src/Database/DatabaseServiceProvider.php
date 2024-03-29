<?php

namespace TurFramework\Database;

use TurFramework\Database\DatabaseManager;
use TurFramework\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->bind('database.manager', fn () => new DatabaseManager());

        Model::setDatabaseManager($this->app->make('database.manager'));
    }
}
