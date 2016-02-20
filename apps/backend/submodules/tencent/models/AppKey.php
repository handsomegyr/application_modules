<?php
namespace Webcms\Backend\Models\Tencent;

class AppKey extends \Webcms\Common\Models\Tencent\AppKey
{
    
    use\Webcms\Backend\Models\Base;

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