<?php
namespace App\Common\Models\Weixinredpack\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Customer extends Base
{

    /**
     * 微信红包-客户信息
     * This model is mapped to the table iweixinredpack_customer
     */
    public function getSource()
    {
        return 'iweixinredpack_customer';
    }

    
}