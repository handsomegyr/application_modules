<?php
namespace App\Backend\Submodules\Weixincard\Models;

class CustomFieldType extends \App\Common\Models\Weixincard\CustomFieldType
{
    
    use \App\Backend\Models\Base;

    /**
     * 获取全部
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
            $list[$item['type']] = $item['name'];
        }
        return $list;
    }
}