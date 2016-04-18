<?php
namespace App\Backend\Submodules\Goods\Models;

class Brand extends \App\Common\Models\Goods\Brand
{
    
    use\App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'sort' => 1
        // '_id' => - 1
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

    /**
     * 获取所有品牌列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['_id']] = $item['name'];
        }
        return $list;
    }
}