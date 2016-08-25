<?php
namespace App\Backend\Submodules\Weixin\Models;

class ConditionalMenuMatchRule extends \App\Common\Models\Weixin\ConditionalMenuMatchRule
{
    
    use \App\Backend\Models\Base;

    /**
     * 获取全部匹配规则
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
            $list[$item['_id']] = $item['matchrule_name'];
        }
        return $list;
    }
}
