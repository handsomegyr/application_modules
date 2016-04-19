<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class AttributeValue extends Base
{

    /**
     * 商品属性值表管理
     * This model is mapped to the table igoods_attribute_value
     */
    public function getSource()
    {
        return 'igoods_attribute_value';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}