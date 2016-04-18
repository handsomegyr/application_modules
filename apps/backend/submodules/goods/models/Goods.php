<?php
namespace App\Backend\Submodules\Goods\Models;

class Goods extends \App\Common\Models\Goods\Goods
{
    
    use\App\Backend\Models\Base;

    /**
     * 获取所有属性列表
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