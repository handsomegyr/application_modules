<?php
namespace Webcms\Weixinredpack\Models;

class Redpack extends \Webcms\Common\Models\Weixinredpack\Redpack
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
     * 根据ID获取信息
     *
     * @param string $id            
     * @return array
     */
    public function getInfoById($id)
    {
        $query = array(
            '_id' => myMongoId($id)
        );
        $info = $this->findOne($query);
        return $info;
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
}