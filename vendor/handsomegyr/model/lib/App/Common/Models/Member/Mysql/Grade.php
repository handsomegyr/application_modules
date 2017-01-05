<?php
namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Grade extends Base
{

    /**
     * 会员-会员等级管理
     * This model is mapped to the table imember_grade
     */
    public function getSource()
    {
        return 'imember_grade';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}