<?php
namespace Webcms\Common\Models\Mysql\Goods;

use Webcms\Common\Models\Mysql\Base;

class Attribute extends Base
{

    /**
     * 商品属性表管理
     * This model is mapped to the table igoods_attribute
     */
    public function getSource()
    {
        return 'igoods_attribute';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}