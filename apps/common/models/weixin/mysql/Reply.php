<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Reply extends Base
{

    /**
     * 微信自动回复设定
     * This model is mapped to the table iweixin_reply
     */
    public function getSource()
    {
        return 'iweixin_reply';
    }
}