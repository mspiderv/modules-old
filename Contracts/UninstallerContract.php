<?php

namespace Vitlabs\Modules\Contracts;

interface UninstallerContract {

    static function uninstall(ModuleContract $module, ModulesManagerContract $modules);

}