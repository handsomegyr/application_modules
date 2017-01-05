<?php
namespace App\Common\Models\Site\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Banner extends Base
{

    /**
     * 网站-广告位表管理
     * This model is mapped to the table isite_banner
     */
    public function getSource()
    {
        return 'isite_banner';
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