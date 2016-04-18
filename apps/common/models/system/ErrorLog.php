<?php
namespace App\Common\Models\System;

use App\Common\Models\Base;

class ErrorLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\System\ErrorLog());
    }
}