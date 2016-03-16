<?php
namespace Webcms\Common\Models\Mysql\Weixinredpack;

use Webcms\Common\Models\Mysql\Base;

class User extends Base
{

    /**
     * 微信红包-领取用户
     * This model is mapped to the table iweixinredpack_user
     */
    public function getSource()
    {
        return 'iweixinredpack_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['redpacklogs'] = $this->changeToArray($data['redpacklogs']);
        return $data;
    }
}