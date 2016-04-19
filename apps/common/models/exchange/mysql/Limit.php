<?php
namespace App\Common\Models\Exchange\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Limit extends Base
{

    /**
     * 兑换-限制
     * This model is mapped to the table iexchange_limit
     */
    public function getSource()
    {
        return 'iexchange_limit';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        
        return $data;
    }
}