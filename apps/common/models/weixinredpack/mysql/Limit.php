<?php
namespace Webcms\Common\Models\Mysql\Weixinredpack;

use Webcms\Common\Models\Mysql\Base;

class Limit extends Base
{

    /**
     * 微信红包-活动规则限制
     * This model is mapped to the table iweixinredpack_limit
     */
    public function getSource()
    {
        return 'iweixinredpack_limit';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        return $data;
    }
}