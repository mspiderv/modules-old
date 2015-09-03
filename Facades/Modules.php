<?php

namespace Vitlabs\Modules\Facades;

use Illuminate\Support\Facades\Facade;

class Modules extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'Vitlabs\Modules\Contracts\ModulesManagerContract';
    }

}
