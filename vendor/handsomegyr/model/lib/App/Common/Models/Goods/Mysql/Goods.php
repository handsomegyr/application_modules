<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Goods extends Base
{

    /**
     * 商品表管理
     * This model is mapped to the table igoods_goods
     */
    public function getSource()
    {
        return 'igoods_goods';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['vat'] = $this->changeToBoolean($data['vat']);
        $data['commend'] = $this->changeToBoolean($data['commend']);
        $data['is_virtual'] = $this->changeToBoolean($data['is_virtual']);
        $data['virtual_invalid_refund'] = $this->changeToBoolean($data['virtual_invalid_refund']);
        $data['is_fcode'] = $this->changeToBoolean($data['is_fcode']);
        $data['is_appoint'] = $this->changeToBoolean($data['is_appoint']);
        $data['is_presell'] = $this->changeToBoolean($data['is_presell']);
        $data['have_gift'] = $this->changeToBoolean($data['have_gift']);
        $data['is_own_shop'] = $this->changeToBoolean($data['is_own_shop']);
        $data['is_hot'] = $this->changeToBoolean($data['is_hot']);
        $data['is_new'] = $this->changeToBoolean($data['is_new']);
        $data['order_goods_list'] = $this->changeToArray($data['order_goods_list']);
        return $data;
    }
}