<?php
namespace Webcms\Common\Models\Mysql\Lottery;

use Webcms\Common\Models\Mysql\Base;

class Record extends Base
{

    /**
     * 抽奖原始记录
     * This model is mapped to the table ilottery_record
     */
    public function getSource()
    {
        return 'ilottery_record';
    }
}