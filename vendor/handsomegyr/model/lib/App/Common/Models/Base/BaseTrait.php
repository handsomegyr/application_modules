<?php
namespace App\Common\Models\Base;

trait BaseTrait
{

    /**
     * 根据ID获取信息
     *
     * @param string $id            
     * @return array
     */
    public function getInfoById($id)
    {
        $query = array(
            '_id' => $id
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据ID列表获取列表信息
     *
     * @param string $ids            
     * @return array
     */
    public function getListByIds(array $ids, $key = '_id')
    {
        if (empty($ids)) {
            return array();
        }
        $query = array(
            '_id' => array(
                '$in' => array_values($ids)
            )
        );
        $list = $this->findAll($query);
        $ret = array();
        if (! empty($list)) {
            foreach ($list as $item) {
                if (empty($item[$key])) {
                    throw new \Exception("{$key}不存在");
                }
                $ret[$item[$key]] = $item;
            }
        }
        return $ret;
    }
}

?>