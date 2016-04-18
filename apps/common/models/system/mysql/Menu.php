<?php
namespace App\Common\Models\Mysql\System;

use App\Common\Models\Mysql\Base;

class Menu extends Base
{

    /**
     * This model is mapped to the table menu
     */
    public function getSource()
    {
        return 'menu';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_show'] = $this->changeToBoolean($data['is_show']);
        return $data;
    }
}