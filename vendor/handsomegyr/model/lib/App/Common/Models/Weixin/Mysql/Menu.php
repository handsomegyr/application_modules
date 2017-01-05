<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Menu extends Base
{

    /**
     * 微信自定义菜单管理
     * This model is mapped to the table iweixin_menu
     */
    public function getSource()
    {
        return 'iweixin_menu';
    }
}