<?php
namespace Webcms\Weixinredpack\Models;

class Reissue extends \Webcms\Common\Models\Weixinredpack\Reissue
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
     * 记录数据
     *
     * @param array $redpack           
     */
    public function record($logid,array $redpack)
    {
        $data =array();
        $data['logid'] = $logid;
        $data['redpack'] = $redpack;        
        return $this->insert($data);
    }

}