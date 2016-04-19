<?php
namespace App\Common\Models\System;

use App\Common\Models\Base\Base;

class User extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\System\Mysql\User());
    }
}
