<?php
namespace App\Common\Models\Mysql\Member;

use App\Common\Models\Mysql\Base;

class Friend extends Base
{

    /**
     * 会员-好友管理
     * This model is mapped to the table imember_friend
     */
    public function getSource()
    {
        return 'imember_friend';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['apply_time'] = $this->changeToMongoDate($data['apply_time']);
        $data['agree_time'] = $this->changeToMongoDate($data['agree_time']);
        return $data;
    }
}