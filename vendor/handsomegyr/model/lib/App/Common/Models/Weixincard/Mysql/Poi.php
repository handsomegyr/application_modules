<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Poi extends Base
{

    /**
     * 微信卡券-POI门店
     * This model is mapped to the table iweixincard_poi
     */
    public function getSource()
    {
        return 'iweixincard_poi';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_uploaded'] = $this->changeToBoolean($data['is_uploaded']);
        return $data;
    }
}