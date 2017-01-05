<?php
namespace App\Common\Models\Payment;

use App\Common\Models\Base\Base;

class Payment extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Payment\Mysql\Payment());
    }
}