<?php
namespace App\Backend\Submodules\Weixincard\Models;

class Testwhitelist extends \App\Common\Models\Weixincard\Testwhitelist
{
    
    use \App\Backend\Models\Base;

    /**
     * 获取列表信息
     *
     * @param string $pid            
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $list = $this->findAll($query, $sort);
        return $list;
    }

    /**
     * 更新是否设置信息
     *
     * @param array $ids            
     * @param boolean $is_set            
     */
    public function updateIsset(array $ids, $is_set = true)
    {
        $query = array();
        $query['_id'] = array(
            '$in' => $ids
        );
        $data = array();
        $data['is_set'] = $is_set;
        $this->update($query, array(
            '$set' => $data
        ));
    }
}