<?php
namespace App\Backend\Submodules\Goods\Models;

class Ad extends \App\Common\Models\Goods\Ad
{
    
    use\App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'show_order' => 1
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