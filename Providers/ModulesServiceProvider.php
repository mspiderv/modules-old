<?php

namespace Vitlabs\Modules\Providers;

use Exception;
use Modules;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Boot modules
        foreach (Modules::getInstalledModules() as $module)
        {
            // Register module service providers
            $providers = $module->getProviders();

            if (is_array($providers))
            {
                foreach($providers as $provider)
                {
                    $this->app->register($provider);
                }
            }

            // Require files
            $files = $module->getFiles();

            if (is_array($files))
            {
                foreach ($files as $file)
                {
                    require_once $file;
                }
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind ModulesRepositoryContract implementation
        $this->app->bind('Vitlabs\Modules\Contracts\ModulesRepositoryContract', 'Vitlabs\Modules\NativeModulesRepository');

        // Bind ModulesManagerContract implementation
        $this->app->bind('Vitlabs\Modules\Contracts\ModulesManagerContract', 'Vitlabs\Modules\ModulesManager', true);

        // Bind ModuleContract implementation
        $this->app->bind('Vitlabs\Modules\Contracts\ModuleContract', 'Vitlabs\Modules\Module');
    }
}
