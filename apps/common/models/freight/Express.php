<?php
namespace App\Common\Models\Freight;

use App\Common\Models\Base\Base;

class Express extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Freight\Mysql\Express());
    }
}