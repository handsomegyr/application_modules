<?php
namespace App\Common\Models\Mysql\Goods;

use App\Common\Models\Mysql\Base;

class TypeSpec extends Base
{

    /**
     * 商品类型与规格对应表管理
     * This model is mapped to the table igoods_type_spec
     */
    public function getSource()
    {
        return 'igoods_type_spec';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}