<?php
namespace Webcms\Common\Models\Payment;

use Webcms\Common\Models\Base;

class Notify extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Payment\Notify());
    }
}