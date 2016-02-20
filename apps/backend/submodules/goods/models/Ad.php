<?php
namespace Webcms\Backend\Models\Goods;

class Ad extends \Webcms\Common\Models\Goods\Ad
{
    
    use\Webcms\Backend\Models\Base;

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