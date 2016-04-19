<?php
namespace App\Common\Models\Invitation\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Rule extends Base
{

    /**
     * 邀请-规则
     * This model is mapped to the table iinvitation_rule
     */
    public function getSource()
    {
        return 'iinvitation_rule';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);        
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);        
        return $data;
    }
}