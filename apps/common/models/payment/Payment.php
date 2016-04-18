<?php
namespace App\Common\Models\Payment;

use App\Common\Models\Base;

class Payment extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Payment\Payment());
    }
}