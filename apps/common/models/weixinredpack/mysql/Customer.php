<?php
namespace Webcms\Common\Models\Mysql\Weixinredpack;

use Webcms\Common\Models\Mysql\Base;

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