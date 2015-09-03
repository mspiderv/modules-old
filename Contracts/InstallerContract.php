<?php

namespace Vitlabs\Modules\Contracts;

interface InstallerContract {

    static function install(ModuleContract $module, ModulesManagerContract $modules);

}