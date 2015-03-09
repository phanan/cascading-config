<?php namespace PhanAn\CascadingConfig;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CascadingConfigServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.local' => config_path('../config.local'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $env_config_path = realpath(config_path('../config.' . app('env')));
        var_dump($env_config_path);

        if (!file_exists($env_config_path) || !is_dir($env_config_path)) {
            // Nothing to do here
            var_dump("$env_config_path not exists.");
            return;
        }

        $config = app('config');

        foreach (Finder::create()->files()->name('*.php')->in($env_config_path) as $file)
        {
            // Run through all PHP files in the current environment's config directory.
            // With each file, check if there's a current config key with the name.
            // If there's not, initialize it as an empty array.
            // Then, use array_replace_recursive() to merge the environment config values 
            // into the base values.
            
            $key_name = $this->getConfigurationNesting($env_config_path, $file) . basename($file->getRealPath(), '.php');

            $old_values = $config->get($key_name) ?: [];
            $new_values = require $file->getRealPath();

            // Replace any matching values in the old config with the new ones.
            $config->set($key_name, array_replace_recursive($old_values, $new_values));
        }
    }

    /**
     * Get the configuration file nesting path.
     * This method is shamelessly copied from Illuminate\Foundation\Boostrap\LoadConfiguration.php
     *
     * @param  \Symfony\Component\Finder\SplFileInfo  $file
     * @return string
     */
    private function getConfigurationNesting($env_config_path, SplFileInfo $file)
    {
        $directory = dirname($file->getRealPath());

        if ($tree = trim(str_replace($env_config_path, '', $directory), DIRECTORY_SEPARATOR))
        {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree) . '.';
        }

        return $tree;
    }

}