<?php
namespace App\Common\Models\Mysql\Member;

use App\Common\Models\Mysql\Base;

class Report extends Base
{

    /**
     * 会员-举报管理
     * This model is mapped to the table imember_report
     */
    public function getSource()
    {
        return 'imember_report';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['report_time'] = $this->changeToMongoDate($data['report_time']);
        return $data;
    }
}