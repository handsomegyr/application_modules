<?php
namespace App\Common\Models\Payment\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Notify extends Base
{

    /**
     * 支付-支付通知表管理
     * This model is mapped to the table ipayment_notify
     */
    public function getSource()
    {
        return 'ipayment_notify';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['notify_time'] = $this->changeToMongoDate($data['notify_time']);
        return $data;
    }
}