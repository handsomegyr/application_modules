<?php
namespace App\Backend\Submodules\Vote\Models;

class LimitCategory extends \App\Common\Models\Vote\LimitCategory
{
    
    use\App\Backend\Models\Base;

    /**
     * 获取所有列表
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
            $list[$item['category']] = $item['name'];
        }
        return $list;
    }
}