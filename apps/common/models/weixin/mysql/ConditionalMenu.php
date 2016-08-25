<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ConditionalMenu extends Base
{

    /**
     * 微信个性化菜单管理
     * This model is mapped to the table iweixin_menu_conditional
     */
    public function getSource()
    {
        return 'iweixin_menu_conditional';
    }
}