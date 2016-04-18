<?php
namespace App\Common\Models\Mysql\Weixin;

use App\Common\Models\Mysql\Base;

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