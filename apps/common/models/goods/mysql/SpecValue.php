<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class SpecValue extends Base
{

    /**
     * 商品规格值表管理
     * This model is mapped to the table igoods_spec_value
     */
    public function getSource()
    {
        return 'igoods_spec_value';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}