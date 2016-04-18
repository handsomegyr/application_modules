<?php
namespace App\Common\Models\Mysql\Goods;

use App\Common\Models\Mysql\Base;

class Brand extends Base
{

    /**
     * 商品品牌表管理
     * This model is mapped to the table igoods_brand
     */
    public function getSource()
    {
        return 'igoods_brand';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}