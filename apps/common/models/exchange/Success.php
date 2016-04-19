<?php
namespace App\Common\Models\Exchange;

use App\Common\Models\Base\Base;

class Success extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Exchange\Mysql\Success());
    }
}