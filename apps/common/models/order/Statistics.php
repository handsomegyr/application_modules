<?php
namespace App\Common\Models\Order;

use App\Common\Models\Base\Base;

class Statistics extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Order\Mysql\Statistics());
    }
}