<?php
namespace Webcms\Backend\Models\Goods;

class SpecValue extends \Webcms\Common\Models\Goods\SpecValue
{
    
    use\Webcms\Backend\Models\Base;

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