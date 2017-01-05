<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Logo extends Base
{

    /**
     * 微信卡券-商户logo
     * This model is mapped to the table iweixincard_logo
     */
    public function getSource()
    {
        return 'iweixincard_logo';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_uploaded'] = $this->changeToBoolean($data['is_uploaded']);
        return $data;
    }
}