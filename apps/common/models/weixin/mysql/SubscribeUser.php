<?php
namespace App\Common\Models\Mysql\Weixin;

use App\Common\Models\Mysql\Base;

class SubscribeUser extends Base
{

    /**
     * 微信关注用户
     * This model is mapped to the table iweixin_subscribe_user
     */
    public function getSource()
    {
        return 'iweixin_subscribe_user';
    }
}