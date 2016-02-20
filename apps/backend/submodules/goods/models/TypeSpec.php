<?php
namespace Webcms\Backend\Models\Goods;

class TypeSpec extends \Webcms\Common\Models\Goods\TypeSpec
{
    
    use\Webcms\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'sort' => 1,
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }
}