<?php
namespace Webcms\Post\Models;

class Vote extends \Webcms\Common\Models\Post\Vote
{

    public function getDefaultSort()
    {
        $sort = array();
        $sort['vote_time'] = - 1;
        return $sort;
    }

    public function getInfoByPostIdAndUserId($post_id, $user_id)
    {
        $query = array();
        $query['post_id'] = $post_id;
        $query['user_id'] = $user_id;
        return $this->findOne($query);
    }

    public function log($post_id, $user_id, $num = 1)
    {
        $data = array();
        $data['post_id'] = $post_id;
        $data['user_id'] = $user_id;
        $data['vote_time'] = getCurrentTime();
        $data['num'] = $num;
        return $this->insert($data);
    }

    /**
     * 增加投票数量
     *
     * @param string $vote_id            
     * @param number $num            
     */
    public function incNum($vote_id, $num = 1)
    {
        $query = array();
        $query['_id'] = $vote_id;
        $this->update($query, array(
            '$inc' => array(
                'vote_num' => $num
            )
        ));
    }
}