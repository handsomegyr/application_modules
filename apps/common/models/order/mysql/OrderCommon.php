<?php
namespace App\Common\Models\Order\Mysql;

use App\Common\Models\Base\Mysql\Base;

class OrderCommon extends Base
{

    /**
     * 订单-订单扩展信息表管理
     * This model is mapped to the table iorder_common
     */
    public function getSource()
    {
        return 'iorder_common';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        // $data['is_smtp'] = $this->changeToBoolean($data['is_smtp']);
        // $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        
        return $data;
    }
}