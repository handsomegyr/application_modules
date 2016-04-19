<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class TypeBrand extends Base
{

    /**
     * 商品类型与品牌对应表管理
     * This model is mapped to the table igoods_type_brand
     */
    public function getSource()
    {
        return 'igoods_type_brand';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}