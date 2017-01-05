<?php
namespace App\Backend\Submodules\Prize\Models;

class Prize extends \App\Common\Models\Prize\Prize
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
            $list[$item['_id']] = $item['prize_name'];
        }
        return $list;
    }
}