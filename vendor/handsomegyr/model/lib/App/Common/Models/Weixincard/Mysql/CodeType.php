<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class CodeType extends Base
{

    /**
     * 微信卡券-code码展示类型
     * This model is mapped to the table iweixincard_code_type
     */
    public function getSource()
    {
        return 'iweixincard_code_type';
    }
}