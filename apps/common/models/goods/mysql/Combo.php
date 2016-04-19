<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Combo extends Base
{

    /**
     * 商品推荐组合表管理
     * This model is mapped to the table igoods_combo
     */
    public function getSource()
    {
        return 'igoods_combo';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}