<?php
namespace Webcms\Common\Models\Order;

use Webcms\Common\Models\Base;

class Pay extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Order\Pay());
    }
}