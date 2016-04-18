<?php
namespace App\Store\Models;

class Store extends \App\Common\Models\Store\Store
{
    
    // 店铺状态，0关闭，1开启，2审核中
    const STATE0 = 0; // 关闭
    const STATE1 = 1; // 开启
    const STATE2 = 2; // 审核中
    public function getDefaultQuery()
    {
        return array(
            'state' => self::STATE1 // 开启
                );
    }

    /**
     * 获取有效的店铺列表
     *
     * @param array $ids            
     * @return array
     */
    public function getActiveListByIds(array $ids)
    {
        $ret = array();
        if (! empty($ids)) {
            $query = array(
                '_id' => array(
                    '$in' => array_values($ids)
                )
            );
            $defaultQuery = $this->getDefaultQuery();
            $query = array_merge($query, $defaultQuery);
            $list = $this->findAll($query);
            if (! empty($list)) {
                foreach ($list as $item) {
                    $ret[$item['_id']] = $item;
                }
            }
        }
        return $ret;
    }
}