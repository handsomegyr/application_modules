<?php
namespace App\Common\Models\Mysql\Weixin;

use App\Common\Models\Mysql\Base;

class Page extends Base
{

    /**
     * 微信自定义页面
     * This model is mapped to the table iweixin_page
     */
    public function getSource()
    {
        return 'iweixin_page';
    }
}