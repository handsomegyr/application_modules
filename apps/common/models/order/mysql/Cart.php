<?php
namespace App\Common\Models\Order\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Cart extends Base
{

    /**
     * 订单-购物车表管理
     * This model is mapped to the table iorder_cart
     */
    public function getSource()
    {
        return 'iorder_cart';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        // $data['is_smtp'] = $this->changeToBoolean($data['is_smtp']);
        // $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        
        return $data;
    }
}