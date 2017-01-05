<?php
namespace App\Common\Models\Order\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 订单-日志表管理
     * This model is mapped to the table iorder_log
     */
    public function getSource()
    {
        return 'iorder_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        // $data['is_smtp'] = $this->changeToBoolean($data['is_smtp']);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}