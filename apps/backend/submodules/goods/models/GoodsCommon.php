<?php
namespace Webcms\Backend\Models\Goods;

class GoodsCommon extends \Webcms\Common\Models\Goods\GoodsCommon
{
    
    use \Webcms\Backend\Models\Base;

    /**
     * 获取所有属性列表
     *
     * @return array
     */
    public function getAll(array $ids=array())
    {
        $query = $this->getQuery();
        if (! empty($ids)) {
            $query['_id'] = array(
                '$in' => array_values($ids)
            );
        }
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['_id']] = $item['name'];
        }
        return $list;
    }
}