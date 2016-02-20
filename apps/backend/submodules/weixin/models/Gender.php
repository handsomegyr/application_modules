<?php
namespace Webcms\Backend\Models\Weixin;

class Gender extends \Webcms\Common\Models\Weixin\Gender
{
    
    use \Webcms\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'value' => - 1
        );
        return $sort;
    }

    /**
     * 获取全部回复类型
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