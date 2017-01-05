<?php
namespace App\Common\Models\Activity;

use App\Common\Models\Base\Base;

class ErrorLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Activity\Mysql\ErrorLog());
    }
}