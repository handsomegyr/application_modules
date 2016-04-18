<?php
namespace App\Goods\Models;

class Browse extends \App\Common\Models\Goods\Brand
{

    /**
     * 根据某种条件获取分页列表
     *
     * @param array $query            
     * @param array $sort            
     * @param array $fields            
     * @return array
     */
    public function getList(array $query = array(), array $sort = array('browse_time'=>-1), array $fields = array())
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $list = $this->findAll($query, $sort, $fields);
            $cache->save($key, $list, 60 * 60); // 一个小时
        }
        return $list;
    }

    /**
     * 根据某种条件获取分页列表
     *
     * @param array $query            
     * @param array $sort            
     * @param array $fields            
     * @return array
     */
    public function getPageList($page = 1, $limit = 10, array $query = array(), array $sort = array(), array $fields = array())
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields, $page, $limit);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $list = $this->find($query, $sort, $fields, ($page - 1) * $limit, $limit);
            $cache->save($key, $list, 60 * 60); // 一个小时
        }
        return $list;
    }

    /**
     * 获取某个人的浏览商品记录列表
     */
    public function getListByMemberId($member_id = 0, $page = 1, $limit = 10)
    {
        $goods_ids = array();
        // 如果会员ID存在，则读取数据库浏览商品记录
        if (! empty($member_id)) {
            $query = array();
            $query['member_id'] = $member_id;
            $list = $this->getPageList($page, $limit, $query, array(
                'browse_time' => - 1
            ), array());
            if (! empty($list)) {
                foreach ($list as $item) {
                    $goods_ids[] = $item['goods_id'];
                }
            }
        } else {
            // 从cookie中查询浏览过的商品记录
        }
        
        // if (! empty($goods_ids)) {
        // $modelGoods = new Goods();
        // $goodsList = $modelGoods->getListByIds($goods_ids);
        // }
        
        return $list;
    }
}