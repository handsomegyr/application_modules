<?php
namespace Webcms\Common\Models\Mysql\Member;

use Webcms\Common\Models\Mysql\Base;

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