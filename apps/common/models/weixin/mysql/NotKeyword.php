<?php
namespace App\Common\Models\Mysql\Weixin;

use App\Common\Models\Mysql\Base;

class NotKeyword extends Base
{

    /**
     * 微信无法自动回复的问题列表
     * This model is mapped to the table iweixin_not_keyword
     */
    public function getSource()
    {
        return 'iweixin_not_keyword';
    }
}