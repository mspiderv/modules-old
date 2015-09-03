<?php

namespace Vitlabs\Modules\Contracts;

interface ModulesRepositoryContract {

    // Module directories
    function addDirs($dirs = []);

    function addDir($dir);

    function hasDir($dir);

    function getDirs();

    /**
     * Find all modules in set directories.
     * @return array of Vitlabs\Modules\Contracts\ModuleContract
     */
    function getModules();

    function getModule($moduleName);

    function getInstalledModules();

    function getUninstalledModules();

    function hasModule($module);

    /**
     * Reload and recache modules.
     * @return $this
     */
    function reload();

}