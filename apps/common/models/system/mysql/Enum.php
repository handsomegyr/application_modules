<?php
namespace App\Common\Models\System\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Enum extends Base
{

    /**
     * This model is mapped to the table enum
     */
    public function getSource()
    {
        return 'enum';
    }
    
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_show'] = $this->changeToBoolean($data['is_show']);
        return $data;
    }
}