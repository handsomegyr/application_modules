<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Ad extends Base
{

    /**
     * 商品广告位表管理
     * This model is mapped to the table igoods_ad
     */
    public function getSource()
    {
        return 'igoods_ad';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_show'] = $this->changeToBoolean($data['is_show']);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        return $data;
    }
}