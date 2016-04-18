<?php
namespace App\Common\Models\Mysql\Weixin;

use App\Common\Models\Mysql\Base;

class Gender extends Base
{

    /**
     * 微信性别管理
     * This model is mapped to the table iweixin_gender
     */
    public function getSource()
    {
        return 'iweixin_gender';
    }
}