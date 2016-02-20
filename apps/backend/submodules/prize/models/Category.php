<?php
namespace Webcms\Backend\Models\Prize;

class Category extends \Webcms\Common\Models\Prize\Category
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
            $list[$item['code']] = $item['name'];
        }
        return $list;
    }
}