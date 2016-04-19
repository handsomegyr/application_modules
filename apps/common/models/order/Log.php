<?php
namespace App\Common\Models\Order;

use App\Common\Models\Base\Base;

class Log extends Base
{

    const ROLE_BUYER = 'buyer'; // 买家
    const ROLE_SELLER = 'seller'; // 商家
    const ROLE_SYSTEM = 'system'; // 系统
    const ROLE_ADMIN = 'admin'; // 管理员
    function __construct()
    {
        $this->setModel(new \App\Common\Models\Order\Mysql\Log());
    }
}