<?php

namespace Vitlabs\Modules\Contracts;

use Vitlabs\Modules\Contracts\ModuleContract;

interface ModulesManagerContract extends ModulesRepositoryContract {

    // Get repository (ModulesRepositoryContract)
    function getRepository();

    // Install modules
    function installAll();
    function install($moduleName);
    function installModule(ModuleContract $module);

    // Uninstall modules
    function uninstallAll();
    function uninstall($moduleName);
    function uninstallModule(ModuleContract $module);

    // Remove modules
    function removeAll();
    function removeInstalled();
    function removeUninstalled();
    function remove($moduleName);
    function removeModule(ModuleContract $module);

}