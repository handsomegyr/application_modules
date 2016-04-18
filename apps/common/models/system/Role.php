<?php
namespace App\Common\Models\System;

use App\Common\Models\Base;

class Role extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\System\Role());
    }
}