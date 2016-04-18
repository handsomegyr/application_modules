<?php
namespace App\Common\Models\Mysql\Member;

use App\Common\Models\Mysql\Base;

class Consignee extends Base
{

    /**
     * 会员-收货人管理
     * This model is mapped to the table imember_consignee
     */
    public function getSource()
    {
        return 'imember_consignee';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_default'] = $this->changeToBoolean($data['is_default']);
        return $data;
    }
}