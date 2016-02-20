<?php
namespace Webcms\Common\Models\Order;

use Webcms\Common\Models\Base;

class Order extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Order\Order());
    }
}