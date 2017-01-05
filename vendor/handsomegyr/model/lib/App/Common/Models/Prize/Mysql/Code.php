<?php
namespace App\Common\Models\Prize\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Code extends Base
{

    /**
     * 奖品-券码
     * This model is mapped to the table iprize_code
     */
    public function getSource()
    {
        return 'iprize_code';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_used'] = $this->changeToBoolean($data['is_used']);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        return $data;
    }
}