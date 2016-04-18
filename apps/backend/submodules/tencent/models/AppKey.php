<?php
namespace App\Backend\Submodules\Tencent\Models;

class AppKey extends \App\Common\Models\Tencent\AppKey
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
            $list[$item['_id']] = $item['appName'];
        }
        return $list;
    }
}