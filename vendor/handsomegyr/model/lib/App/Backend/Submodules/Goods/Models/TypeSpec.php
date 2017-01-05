<?php
namespace App\Backend\Submodules\Goods\Models;

class TypeSpec extends \App\Common\Models\Goods\TypeSpec
{
    
    use\App\Backend\Models\Base;

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