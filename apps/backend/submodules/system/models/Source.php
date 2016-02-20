<?php
namespace Webcms\Backend\Models\System;

class Source extends \Webcms\Common\Models\System\Source
{
    use \Webcms\Backend\Models\Base;

    /**
     * 获取全部来源类型
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
            $list[$item['value']] = $item['key'];
        }
        return $list;
    }
}