<?php

namespace PhanAn\CascadingConfig;

use Illuminate\Support\ServiceProvider;
use SplFileInfo as SysSplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CascadingConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.local' => $this->getConfigPath('../config.local'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $env = $this->app->environment();

        $envConfigPath = (new SysSplFileInfo(dirname($this->getConfigPath())."/config.$env"))->getRealPath();

        if (!file_exists($envConfigPath) ||  !is_dir($envConfigPath)) {
            // Nothing to do here
            return;
        }

        $config = $this->app->make('config');

        foreach (Finder::create()->files()->name('*.php')->in($envConfigPath) as $file) {
            // Run through all PHP files in the current environment's config directory.
            // With each file, check if there's a current config key with the name.
            // If there's not, initialize it as an empty array.
            // Then, use array_replace_recursive() to merge the environment config values
            // into the base values.

            $keyName = $this->getConfigurationNesting($envConfigPath, $file).basename($file->getRealPath(), '.php');

            $oldValues = $config->get($keyName) ?: [];
            $newValues = require $file->getRealPath();

            // Replace any matching values in the old config with the new ones.
            $config->set($keyName, array_replace_recursive($oldValues, $newValues));
        }
    }

    /**
     * Get the configuration file nesting path.
     * This method is shamelessly copied from \Illuminate\Foundation\Bootstrap\LoadConfiguration.php.
     *
     * @param string                                $envConfigPath
     * @param \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return string
     */
    protected function getConfigurationNesting($envConfigPath, SplFileInfo $file)
    {
        $directory = dirname($file->getRealPath());

        if ($tree = trim(str_replace($envConfigPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree).'.';
        }

        return $tree;
    }

    /**
     * Get the path to the config directory.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getConfigPath($path = null)
    {
        // Lumen >=5.1.6 exposes a getConfigurationPath() method.
        if ($this->isLumen()) {
            return $this->app->getConfigurationPath().$path;
        }

        // Laravel comes with a config_path() helper.
        if (function_exists('config_path')) {
            return config_path($path);
        }

        throw new \Exception('CascadingConfig error: Unsupported Laravel/Lumen version.');
    }

    /**
     * Check if the current application is a Lumen instance.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return is_a($this->app, 'Laravel\Lumen\Application');
    }
}
