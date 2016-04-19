<?php
namespace App\Common\Models\Order;

use App\Common\Models\Base\Base;

class Order extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Order\Mysql\Order());
    }
}