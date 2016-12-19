<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Card extends Base
{

    /**
     * 微信卡券-卡券
     * This model is mapped to the table iweixincard_card
     */
    public function getSource()
    {
        return 'iweixincard_card';
    }
}