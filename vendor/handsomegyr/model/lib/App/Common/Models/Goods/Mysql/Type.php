<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{

    /**
     * 商品类型表管理
     * This model is mapped to the table igoods_type
     */
    public function getSource()
    {
        return 'igoods_type';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}