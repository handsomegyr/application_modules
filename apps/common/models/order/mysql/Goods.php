<?php
namespace App\Common\Models\Order\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Goods extends Base
{

    /**
     * 订单-商品表管理
     * This model is mapped to the table iorder_goods
     */
    public function getSource()
    {
        return 'iorder_goods';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        // $data['is_smtp'] = $this->changeToBoolean($data['is_smtp']);
        // $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        $data['consignee_info'] = $this->changeToArray($data['consignee_info']);
        $data['delivery_info'] = $this->changeToArray($data['delivery_info']);
        return $data;
    }
}