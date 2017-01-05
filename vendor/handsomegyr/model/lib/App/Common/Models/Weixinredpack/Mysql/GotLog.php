<?php
namespace App\Common\Models\Weixinredpack\Mysql;

use App\Common\Models\Base\Mysql\Base;

class GotLog extends Base
{

    /**
     * 微信红包-发放记录
     * This model is mapped to the table iweixinredpack_got_log
     */
    public function getSource()
    {
        return 'iweixinredpack_got_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['isOK'] = $this->changeToBoolean($data['isOK']);
        $data['got_time'] = $this->changeToMongoDate($data['got_time']);
        $data['error_logs'] = $this->changeToArray($data['error_logs']);
        
        return $data;
    }
}