<?php
namespace App\Common\Models\Exchange\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 兑换-日志
     * This model is mapped to the table iexchange_log
     */
    public function getSource()
    {
        return 'iexchange_log';
    }
}