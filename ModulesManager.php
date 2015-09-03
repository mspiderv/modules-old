<?php

namespace Vitlabs\Modules;

use Vitlabs\Modules\Contracts\ModulesManagerContract;
use Vitlabs\Modules\Contracts\ModuleContract;

class ModulesManager implements ModulesManagerContract {

    protected $repository = null;

    public function __construct()
    {
        // Get repository instance
        $this->repository = app('Vitlabs\Modules\Contracts\ModulesRepositoryContract');
    }

    // Get repository (ModulesRepositoryContract)
    public function getRepository()
    {
        return $this->repository;
    }

    // Install modules
    public function installAll()
    {
        foreach ($this->repository->getUninstalledModules() as $module)
        {
            $this->installModule($module);
        }

        return $this;
    }

    public function install($moduleName)
    {
        $module = $this->repository->getModule($moduleName);

        if ($module === null)
        {
            return false;
        }

        return $this->installModule($module);
    }

    public function installModule(ModuleContract $module)
    {
        $installer = $module->getInstaller();

        if ($installer !== null)
        {
            call_user_func([$installer, 'install'], $module, $this);
        }

        $module->setInstalled(true);

        return $module->save();
    }

    // Uninstall modules
    public function uninstallAll()
    {
        foreach ($this->repository->getInstalledModules() as $module)
        {
            $this->uninstallModule($module);
        }

        return $this;
    }

    public function uninstall($moduleName)
    {
        $module = $this->repository->getModule($moduleName);

        if ($module === null)
        {
            return false;
        }

        return $this->uninstallModule($module);
    }

    public function uninstallModule(ModuleContract $module)
    {
        $uninstaller = $module->getUninstaller();

        if ($uninstaller !== null)
        {
            call_user_func([$uninstaller, 'uninstall'], $module, $this);
        }

        $module->setInstalled(false);

        return $module->save();
    }

    // Remove modules
    public function removeAll()
    {
        foreach ($this->repository->getModules() as $module)
        {
            $this->removeModule($module);
        }

        return $this;
    }

    public function removeInstalled()
    {
        foreach ($this->repository->getInstalledModules() as $module)
        {
            $this->removeModule($module);
        }

        return $this;
    }

    public function removeUninstalled()
    {
        foreach ($this->repository->getUninstalledModules() as $module)
        {
            $this->removeModule($module);
        }

        return $this;
    }

    public function remove($moduleName)
    {
        $module = $this->repository->getModule($moduleName);

        if ($module === null)
        {
            return false;
        }

        return $this->removeModule($module);
    }

    public function removeModule(ModuleContract $module)
    {
        // First, uninstall the module
        $this->uninstallModule($module);

        // TODO
    }

    /* Repository methods */

    // Module directories
    public function addDirs($dirs = [])
    {
        return $this->repository->addDirs($dirs = []);
    }

    public function addDir($dir)
    {
        return $this->repository->addDir($dir);
    }

    public function hasDir($dir)
    {
        return $this->repository->hasDir($dir);
    }

    public function getDirs()
    {
        return $this->repository->getDirs();
    }

    /**
     * Find all modules in set directories.
     * @return array of Vitlabs\Modules\Contracts\ModuleContract
     */
    public function getModules()
    {
        return $this->repository->getModules();
    }

    public function getModule($moduleName)
    {
        return $this->repository->getModule($moduleName);
    }

    public function getInstalledModules()
    {
        return $this->repository->getInstalledModules();
    }

    public function getUninstalledModules()
    {
        return $this->repository->getUninstalledModules();
    }

    public function hasModule($module)
    {
        return $this->repository->hasModule($module);
    }

    /**
     * Reload and recache modules.
     * @return $this
     */
    public function reload()
    {
        return $this->repository->reload();
    }

}