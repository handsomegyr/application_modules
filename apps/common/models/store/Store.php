<?php
namespace App\Common\Models\Store;

use App\Common\Models\Base;

class Store extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Store\Store());
    }
}