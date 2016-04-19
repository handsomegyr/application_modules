<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class AttrIndex extends Base
{

    /**
     * 商品与属性对应表管理
     * This model is mapped to the table igoods_attr_index
     */
    public function getSource()
    {
        return 'igoods_attr_index';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}