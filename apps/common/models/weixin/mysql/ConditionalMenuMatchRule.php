<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ConditionalMenuMatchRule extends Base
{

    /**
     * 微信个性化菜单匹配规则管理
     * This model is mapped to the table iweixin_menu_conditional_matchrule
     */
    public function getSource()
    {
        return 'iweixin_menu_conditional_matchrule';
    }
}