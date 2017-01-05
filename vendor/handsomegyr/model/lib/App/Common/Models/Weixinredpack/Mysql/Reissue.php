<?php
namespace App\Common\Models\Weixinredpack\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Reissue extends Base
{

    /**
     * 微信红包-补发日志
     * This model is mapped to the table iweixinredpack_reissue
     */
    public function getSource()
    {
        return 'iweixinredpack_reissue';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['redpack'] = $this->changeToArray($data['redpack']);
        return $data;
    }
}
