<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Callbackurls extends Base
{

    /**
     * 微信回调地址安全域名
     * This model is mapped to the table iweixin_callbackurls
     */
    public function getSource()
    {
        return 'iweixin_callbackurls';
    }
}