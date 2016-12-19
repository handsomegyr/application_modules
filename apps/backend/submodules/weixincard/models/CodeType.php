<?php
namespace App\Backend\Submodules\Weixincard\Models;

class CodeType extends \App\Common\Models\Weixincard\CodeType
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
            $list[$item['code_type']] = $item['name'];
        }
        return $list;
    }
}