<?php
namespace App\Common\Models\Mysql\Freight;

use App\Common\Models\Mysql\Base;

class Express extends Base
{

    /**
     * 运价-快递公司管理
     * This model is mapped to the table ifreight_express
     */
    public function getSource()
    {
        return 'ifreight_express';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['state'] = $this->changeToBoolean($data['state']);
        $data['is_order'] = $this->changeToBoolean($data['is_order']);
        $data['zt_state'] = $this->changeToBoolean($data['zt_state']);
        return $data;
    }
}