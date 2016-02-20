<?php
namespace Webcms\Common\Models\Mysql\Exchange;

use Webcms\Common\Models\Mysql\Base;

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