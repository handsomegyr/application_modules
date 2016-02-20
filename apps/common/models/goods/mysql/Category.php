<?php
namespace Webcms\Common\Models\Mysql\Goods;

use Webcms\Common\Models\Mysql\Base;

class Category extends Base
{

    /**
     * 商品分类表管理
     * This model is mapped to the table igoods_category
     */
    public function getSource()
    {
        return 'igoods_category';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}