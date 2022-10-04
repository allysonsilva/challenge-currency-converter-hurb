<?php

namespace Support\Domain;

use ReflectionClass;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

abstract class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (file_exists($migrationsPath = $this->componentPath('Database' . DIRECTORY_SEPARATOR . 'Migrations'))) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    /**
     * Retrieves the component's base path from the domain context,
     * so that related resources can be loaded/manipulated.
     *
     * @param string|null $directory
     *
     * @return string
     */
    protected function componentPath(?string $directory = null): string
    {
        $realPath = realpath(dirname((new ReflectionClass($this))->getFileName()) . '/../');

        // @codeCoverageIgnoreStart
        if (empty($directory)) {
            return $realPath;
        }
        // @codeCoverageIgnoreEnd

        return $realPath . DIRECTORY_SEPARATOR . $directory;
    }
}
