<?php

namespace Vitlabs\Modules;

use File;
use Illuminate\Filesystem\FileNotFoundException;
use Vitlabs\Modules\Contracts\ModulesRepositoryContract;
use Vitlabs\Modules\Exceptions\ModuleJSONException;

class NativeModulesRepository implements ModulesRepositoryContract {

    protected $dirs = [];
    protected $modules = null;
    protected $installedModules = null;
    protected $uninstalledModules = null;

    // Module directories
    public function addDirs($dirs = [])
    {
        // Add dirs
        foreach ($dirs as $dir)
        {
            $this->addDir($dir);
        }

        return $this;
    }

    public function addDir($dir)
    {
        // If we already got the dir, continue
        if ( ! $this->hasDir($dir))
        {
            // Add directory
            $this->dirs[] = $dir;
        }

        return $this;
    }

    public function hasDir($dir)
    {
        return array_search($dir, $this->dirs) !== false;
    }

    public function getDirs()
    {
        return $this->dirs;
    }

    /**
     * Find all modules in set directories.
     * @return array of Vitlabs\Modules\Contracts\ModuleContract
     */
    public function getModules()
    {
        $this->loadIfNeed();

        return $this->modules;
    }

    public function getModule($moduleName)
    {
        $this->loadIfNeed();

        return (isset($this->modules[$moduleName])) ? $this->modules[$moduleName] : null;
    }

    public function getInstalledModules()
    {
        $this->loadIfNeed();

        return $this->installedModules;
    }

    public function getUninstalledModules()
    {
        $this->loadIfNeed();

        return $this->uninstalledModules;
    }

    public function hasModule($module)
    {
        $this->loadIfNeed();

        return (isset($this->modules[$moduleName]));
    }

    /**
     * Reload and recache modules.
     * @return $this
     */
    public function reload()
    {
        // Clear arrays
        $this->modules = [];
        $this->installedModules = [];
        $this->uninstalledModules = [];

        // Find all modules
        $found = [];

        foreach ($this->dirs as $dir)
        {
            $found = array_merge($found, glob($dir . '/module.json'));
        }

        // Create modules
        foreach ($found as $pathToJSON)
        {
            // Create module
            $module = $this->createModule($pathToJSON);

            // Get module name
            $moduleName = $module->getName();

            // Add module to arrays
            $this->modules[$moduleName] = $module;

            if ($module->isInstalled())
            {
                $this->installedModules[$moduleName] = $module;
            }
            else
            {
                $this->uninstalledModules[$moduleName] = $module;
            }
        }
    }

    protected function loadIfNeed()
    {
        if ($this->modules == null)
        {
            $this->reload();
        }
    }

    // WTF ?
    protected function createModule($pathToJSON)
    {
        // Get JSON content
        try
        {
            $JSONContent = File::get($pathToJSON);
        }

        catch (FileNotFoundException $e)
        {
            throw new ModuleJSONException($pathToJSON, 0, $e);
        }

        // Decode JSON
        $data = json_decode($JSONContent);

        // Decoding successful ?
        if ($data === null)
        {
            throw new ModuleJSONException("File [$pathToJSON] cannot be decoded as JSON format.");
        }

        // Create module
        return app('Vitlabs\Modules\Contracts\ModuleContract', [dirname($pathToJSON), $data]);
    }

}