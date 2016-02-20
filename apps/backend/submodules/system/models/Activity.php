<?php
namespace Webcms\Backend\Models\System;

class Activity extends \Webcms\Common\Models\System\Activity
{
    use\Webcms\Backend\Models\Base;

    /**
     * 获取所有活动列表
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