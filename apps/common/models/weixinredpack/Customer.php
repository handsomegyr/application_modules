<?php
namespace App\Common\Models\Weixinredpack;

use App\Common\Models\Base\Base;

class Customer extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixinredpack\Mysql\Customer());
    }
}