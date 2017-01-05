<?php
namespace App\Common\Models\Order\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Pay extends Base
{

    /**
     * 订单-支付表管理
     * This model is mapped to the table iorder_pay
     */
    public function getSource()
    {
        return 'iorder_pay';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['api_pay_state'] = $this->changeToBoolean($data['api_pay_state']);
        $data['process_state'] = $this->changeToBoolean($data['process_state']);
        return $data;
    }
}