<?php
namespace App\Common\Models\Payment\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 支付-支付日志表管理
     * This model is mapped to the table ipayment_log
     */
    public function getSource()
    {
        return 'ipayment_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}