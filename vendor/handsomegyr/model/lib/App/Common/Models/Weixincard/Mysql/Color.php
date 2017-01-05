<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Color extends Base
{

    /**
     * 微信卡券-颜色
     * This model is mapped to the table iweixincard_color
     */
    public function getSource()
    {
        return 'iweixincard_color';
    }
}