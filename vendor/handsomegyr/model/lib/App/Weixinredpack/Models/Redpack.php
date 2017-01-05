<?php
namespace App\Weixinredpack\Models;

class Redpack extends \App\Common\Models\Weixinredpack\Redpack
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 根据code获取信息
     *
     * @param string $code            
     * @return array
     */
    public function getInfoByCode($code)
    {
        $query = array(
            'code' => (string) $code
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 获取列表信息
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $item) {
                $list[$item['code']] = $item;
            }
        }
        return $list;
    }

    /**
     * 根据date获取信息
     *
     * @param string $now            
     * @return array
     */
    public function getInfo4Today($now)
    {
        $query = array(
            'start_time' => array(
                '$lte' => $now
            ),
            'end_time' => array(
                '$gte' => $now
            )
        );
        $info = $this->findOne($query);
        return $info;
    }
}