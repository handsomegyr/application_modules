<?php
namespace App\Common\Models\Weixinredpack\Mysql;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{

    /**
     * 微信红包-红包用户管理
     * This model is mapped to the table iweixinredpack_user
     */
    public function getSource()
    {
        return 'iweixinredpack_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}