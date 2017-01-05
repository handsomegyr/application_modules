<?php
namespace App\Common\Models\System\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Role extends Base
{

    /**
     * This model is mapped to the table role
     */
    public function getSource()
    {
        return 'role';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['menu_list'] = $this->changeToArray($data['menu_list']);
        $data['operation_list'] = $this->changeToArray($data['operation_list']);
        return $data;
    }
}