<?php
namespace App\Common\Models\Prize\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Prize extends Base
{

    /**
     * 奖品-奖品
     * This model is mapped to the table iprize_prize
     */
    public function getSource()
    {
        return 'iprize_prize';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        
        $data['is_virtual'] = $this->changeToBoolean($data['is_virtual']);
        $data['is_need_virtual_code'] = $this->changeToBoolean($data['is_need_virtual_code']);
        $data['is_valid'] = $this->changeToBoolean($data['is_valid']);
        return $data;
    }
}