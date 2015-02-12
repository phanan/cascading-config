<?php namespace PhanAn\CascadingConfig;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

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

        $envConfigPath = config_path() . '/../config.' . env('APP_ENV');
        $config = app('config');

        foreach (Finder::create()->files()->name('*.php')->in($envConfigPath) as $file)
        {
            // Run through all PHP files in the current environment's config directory.
            // With each file, check if there's a current config key with the name.
            // If there's not, initialize it as an empty array.
            // Then, use array_replace_recursive() to merge the environment config values 
            // into the base values.
            
            $key_name = basename($file->getRealPath(), '.php');

            $old_values = $config->get($key_name) ?: [];
            $new_values = require $file->getRealPath();

            // Replace any matching values in the old config with the new ones.
            $config->set($key_name, array_replace_recursive($old_values, $new_values));
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
    }

}