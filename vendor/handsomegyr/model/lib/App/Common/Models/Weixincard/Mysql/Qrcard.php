<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Qrcard extends Base
{

    /**
     * 微信卡券-卡券二维码
     * This model is mapped to the table iweixincard_qrcard
     */
    public function getSource()
    {
        return 'iweixincard_qrcard';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_unique_code'] = $this->changeToBoolean($data['is_unique_code']);
        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        $data['ticket_time'] = $this->changeToMongoDate($data['ticket_time']);
        return $data;
    }
}