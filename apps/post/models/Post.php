<?php
namespace App\Post\Models;

class Post extends \App\Common\Models\Post\Post
{

    public function getDefaultSort()
    {
        $sort = array();
        $sort['post_time'] = - 1;
        return $sort;
    }

    public function getPostCountByBuyerId($buyer_id)
    {
        $query = array();
        $query['buyer_id'] = $buyer_id;
        // $query['state'] = array(
        // '$ne' => self::STATE_NONE
        // );
        $query['state'] = self::STATE2;
        return $this->count($query);
    }

    public function getUnPostCountByBuyerId($buyer_id)
    {
        $query = array();
        $query['buyer_id'] = $buyer_id;
        $query['state'] = self::STATE_NONE;
        return $this->count($query);
    }

    /**
     * 分页获取帖子列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getPageList($page = 1, $limit = 10, array $otherConditions = array(), array $sort = array(), array $fields = array())
    {
        $query = array();
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    /**
     * 分页获取某个买家的帖子列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getPageListByBuyerId($buyer_id, $page = 1, $limit = 10, array $otherConditions = array(), array $sort = array())
    {
        $query = array();
        $query['buyer_id'] = $buyer_id;
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    public function getInfoByBuyerIdAndGoodsId($buyer_id, $goods_id)
    {
        $query = array();
        $query['buyer_id'] = $buyer_id;
        $query['goods_id'] = $goods_id;
        return $this->findOne($query);
    }

    public function create($buyer_id, array $goodsInfo)
    {
        $data = array();
        $data['title'] = '';
        $data['content'] = '';
        $data['pic'] = '';
        $data['state'] = self::STATE_NONE;
        $data['fail_reason'] = "";
        $data['point'] = 0;
        $data['brand_id'] = $goodsInfo['brand_id'];
        $data['gc_id_1'] = $goodsInfo['gc_id_1'];
        $data['gc_id_2'] = $goodsInfo['gc_id_2'];
        $data['gc_id_3'] = $goodsInfo['gc_id_3'];
        $data['goods_commonid'] = $goodsInfo['goods_commonid'];
        $data['goods_id'] = $goodsInfo['_id'];
        $data['buyer_id'] = $buyer_id;
        $data['goods_info'] = json_encode($goodsInfo);
        $data['order_no'] = $goodsInfo['prize_order_goods_order_no'];
        $data['vote_num'] = 0;
        $data['reply_num'] = 0;
        $data['read_num'] = 0;
        return $this->insert($data);
    }

    public function insertPostSingle($id, $title, $content, $pic)
    {
        $query = array();
        $query['_id'] = $id;
        
        $data = array();
        $data['title'] = $title;
        $data['content'] = $content;
        $data['pic'] = $pic;
        $data['post_time'] = getCurrentTime();
        $data['state'] = self::STATE0;
        $data['fail_reason'] = "";
        $data['point'] = 0;
        return $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 增加投票数量
     *
     * @param string $post_id            
     * @param number $num            
     */
    public function incVoteNum($post_id, $num = 1)
    {
        $query = array();
        $query['_id'] = $post_id;
        $this->update($query, array(
            '$inc' => array(
                'vote_num' => $num
            )
        ));
    }

    /**
     * 增加评论数量
     *
     * @param string $post_id            
     * @param number $num            
     */
    public function incReplyNum($post_id, $num = 1)
    {
        $query = array();
        $query['_id'] = $post_id;
        $this->update($query, array(
            '$inc' => array(
                'reply_num' => $num
            )
        ));
    }

    /**
     * 增加阅读数量
     *
     * @param string $post_id            
     * @param number $num            
     */
    public function incReadNum($post_id, $num = 1)
    {
        $query = array();
        $query['_id'] = $post_id;
        $this->update($query, array(
            '$inc' => array(
                'read_num' => $num
            )
        ));
    }

    public function pass($id, $point, $is_recommend, $user_id, $user_name)
    {
        $query = array();
        $query['_id'] = $id;
        
        $data = array();
        $data['state'] = self::STATE2;
        $data['point'] = $point;
        $data['is_recommend'] = empty($is_recommend) ? false : true;
        $data['verify_time'] = getCurrentTime();
        $data['verify_user_id'] = $user_id;
        $data['verify_user_name'] = $user_name;
        
        return $this->update($query, array(
            '$set' => $data
        ));
    }

    public function unpass($id, $fail_reason, $user_id, $user_name)
    {
        $query = array();
        $query['_id'] = $id;
        
        $data = array();
        $data['state'] = self::STATE1;
        $data['fail_reason'] = $fail_reason;
        $data['verify_time'] = getCurrentTime();
        $data['verify_user_id'] = $user_id;
        $data['verify_user_name'] = $user_name;
        return $this->update($query, array(
            '$set' => $data
        ));
    }
}