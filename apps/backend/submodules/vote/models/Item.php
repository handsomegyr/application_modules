<?php
namespace Webcms\Backend\Models\Vote;

class Item extends \Webcms\Common\Models\Vote\Item
{
    
    use \Webcms\Backend\Models\Base;

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
            $list[$item['_id']] = $item['name'];
        }
        return $list;
    }
}