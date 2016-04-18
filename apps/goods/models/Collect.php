<?php
namespace App\Goods\Models;

class Collect extends \App\Common\Models\Goods\Collect
{

    /**
     * 分页获取云购记录的商品列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @return array
     */
    public function getUserCollectlist($user_id, $page = 1, $limit = 9, array $otherConditions = array())
    {
        $query = array(
            'user_id' => $user_id
        );
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $sort = array(
            'collect_time' => 1
        );
        $ret = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        $list = array();
        if (! empty($ret['datas'])) {
            foreach ($ret['datas'] as $item) {
                $list[$item['goods_id']] = $item;
            }
        }
        return array(
            'datas' => $list,
            'total' => $ret['total']
        );
    }

    public function log($user_id, $goods_id, $num = 1)
    {
        $data = array();
        $data['user_id'] = $user_id;
        $data['goods_id'] = $goods_id;
        $data['num'] = $num;
        $data['collect_time'] = getCurrentTime();
        return $this->insert($data);
    }

    public function del($user_id, $goods_id)
    {
        $query = array();
        $query['user_id'] = $user_id;
        $query['goods_id'] = $goods_id;
        return $this->remove($query);
    }

    public function getInfoByUserIdAndGoodsId($user_id, $goods_id)
    {
        $query = array();
        $query['user_id'] = $user_id;
        $query['goods_id'] = $goods_id;
        $info = $this->findOne($query);
        return $info;
    }

    public function hasGoods($user_id)
    {
        $query = array();
        $query['user_id'] = $user_id;
        $info = $this->findOne($query);
        if (empty($info)) {
            return false;
        } else {
            return true;
        }
    }
}