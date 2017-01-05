<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class CodeDeposit extends Base
{

    /**
     * 微信卡券-code导入
     * This model is mapped to the table iweixincard_code_deposit
     */
    public function getSource()
    {
        return 'iweixincard_code_deposit';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_deposited'] = $this->changeToBoolean($data['is_deposited']);
        $data['is_consumed'] = $this->changeToBoolean($data['is_consumed']);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        return $data;
    }
}