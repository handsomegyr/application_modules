<?php
namespace App\Common\Models\Order;

use App\Common\Models\Base;

class Cart extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Order\Cart());
    }
}