<?php
namespace App\Post\Models;

class Reply extends \App\Common\Models\Post\Reply
{

    public function getDefaultSort()
    {
        $sort = array();
        $sort['reply_time'] = - 1;
        return $sort;
    }

    /**
     * 分页获取某个帖子的评论列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getPageListByPostId($post_id, $page = 1, $limit = 10, array $otherConditions = array(), array $sort = array())
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
     * 分页获取某个帖子的评论列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getListByReplyId($reply_id, $page = 1, $limit = 10, array $otherConditions = array(), array $sort = array())
    {
        $query = array();
        $query['ref_reply_id'] = $reply_id;
        $query['is_del'] = false;
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
     * 分页获取某人的列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getReplyMsgPageByUserID($user_id, $page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query = array();
        $query1 = array(
            'user_id' => $user_id
        );
        $query2 = array(
            'to_user_id' => $user_id
        );
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    public function insertpostreply($post_id, $user_id, $user_name, $user_avatar, $user_register_by, $user_content, $to_user_id, $to_user_name, $to_user_avatar, $to_user_register_by, $to_user_content, $ref_reply_id, $floor, $ref_floor)
    {
        $data = array();
        $data['post_id'] = $post_id;
        
        $data['user_id'] = $user_id;
        $data['user_name'] = $user_name;
        $data['user_avatar'] = $user_avatar;
        $data['user_register_by'] = $user_register_by;
        $data['user_content'] = $user_content;
        
        $data['to_user_id'] = $to_user_id;
        $data['to_user_name'] = $to_user_name;
        $data['to_user_avatar'] = $to_user_avatar;
        $data['to_user_register_by'] = $to_user_register_by;
        
        $data['to_user_content'] = $to_user_content;
        $data['ref_reply_id'] = $ref_reply_id;
        $data['floor'] = $floor;
        $data['ref_floor'] = $ref_floor;
        $data['reply_time'] = getCurrentTime();
        $data['num'] = 0;
        $data['del_num'] = 0;
        $data['is_del'] = false;
        return $this->insert($data);
    }

    /**
     * 增加回复数量
     *
     * @param string $reply_id            
     * @param number $num            
     */
    public function incNum($reply_id, $num = 1)
    {
        $query = array();
        $query['_id'] = $reply_id;
        
        $this->update($query, array(
            '$inc' => array(
                'num' => $num
            )
        ));
        
        $option = array();
        $option['query'] = array(
            '_id' => $reply_id
        );
        $option['update'] = array(
            '$inc' => array(
                'num' => $num
            )
        );
        $rst = $this->findAndModify($option);
        if (empty($rst['ok'])) {
            throw new \Exception("增加回复数量的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("增加回复数量的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        return $rst['value'];
    }

    /**
     * 增加删除数量
     *
     * @param string $reply_id            
     * @param number $num            
     */
    public function incDelNum($reply_id, $num = 1)
    {
        $query = array();
        $query['_id'] = $reply_id;
        $this->update($query, array(
            '$inc' => array(
                'del_num' => $num
            )
        ));
    }

    /**
     * 设置删除标志
     *
     * @param string $reply_id            
     * @param boolean $is_del            
     */
    public function setIsDel($reply_id, $is_del = true)
    {
        $query = array();
        $query['_id'] = $reply_id;
        $this->update($query, array(
            '$set' => array(
                'is_del' => $is_del
            )
        ));
    }

    public function setDelByUserId($user_id, $is_del = true)
    {
        $query = array();
        $query1 = array(
            'user_id' => $user_id
        );
        $query2 = array(
            'to_user_id' => $user_id
        );
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        
        $this->update($query, array(
            '$set' => array(
                'is_del' => $is_del
            )
        ));
    }
}