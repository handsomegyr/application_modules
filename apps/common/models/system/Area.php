<?php
namespace App\Common\Models\System;

use App\Common\Models\Base;

class Area extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\System\Mysql\Area());
    }
}