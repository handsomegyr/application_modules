<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class MenuType extends Base
{

    /**
     * 微信菜单类型
     * This model is mapped to the table iweixin_menu_type
     */
    public function getSource()
    {
        return 'iweixin_menu_type';
    }
}