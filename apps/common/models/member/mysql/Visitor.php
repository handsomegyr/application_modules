<?php
namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Visitor extends Base
{

    /**
     * 会员-访客管理
     * This model is mapped to the table imember_visitor
     */
    public function getSource()
    {
        return 'imember_visitor';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['browser_time'] = $this->changeToMongoDate($data['browser_time']);
        return $data;
    }
}