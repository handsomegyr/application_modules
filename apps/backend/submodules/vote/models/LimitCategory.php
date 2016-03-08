<?php
namespace Webcms\Backend\Models\Vote;

class LimitCategory extends \Webcms\Common\Models\Vote\LimitCategory
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
            $list[$item['category']] = $item['name'];
        }
        return $list;
    }
}