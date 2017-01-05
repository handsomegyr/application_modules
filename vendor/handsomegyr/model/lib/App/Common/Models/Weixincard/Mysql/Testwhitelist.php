<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Testwhitelist extends Base
{

    /**
     * 微信卡券-设置测试用户白名单
     * This model is mapped to the table iweixincard_testwhitelist
     */
    public function getSource()
    {
        return 'iweixincard_testwhitelist';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_set'] = $this->changeToBoolean($data['is_set']);
        return $data;
    }
}