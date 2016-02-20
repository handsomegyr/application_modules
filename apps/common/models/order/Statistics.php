<?php
namespace Webcms\Common\Models\Order;

use Webcms\Common\Models\Base;

class Statistics extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Order\Statistics());
    }
}