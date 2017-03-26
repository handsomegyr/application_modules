<?php
namespace App\Bargain\Models;

class Bargain extends \App\Common\Models\Bargain\Bargain
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'total_bargain_num' => - 1,
            'worth' => - 1,
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $now = new \MongoDate();
        $query = array(
            'is_closed' => false,
            'quantity' => array(
                '$gt' => 0
            ),
            'start_time' => array(
                '$lt' => $now
            ),
            'end_time' => array(
                '$gt' => $now
            )
        );
        return $query;
    }

    /**
     * 根据发起用户ID和砍价物编号和活动ID获取信息
     *
     * @param string $user_id            
     * @param string $bargain_code            
     * @param string $activity_id            
     * @return array
     */
    public function getLatestInfoByUserIdAndBargainCode($user_id, $bargain_code, $activity_id)
    {
        $query = array(
            'user_id' => $user_id,
            'code' => $bargain_code,
            'activity_id' => $activity_id
        );
        $query = array_merge($query, $this->getQuery());
        
        $sort = array(
            'launch_time' => - 1
        );
        $list = $this->find($query, $sort, 0, 1);
        if (empty($list['datas'])) {
            return null;
        } else {
            return $list['datas'][0];
        }
    }

    /**
     * 生成砍价物数据
     *
     * @param string $activity_id            
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param string $code            
     * @param string $name            
     * @param number $worth            
     * @param number $quantity            
     * @param number $bargain_from            
     * @param number $bargain_to            
     * @param number $worth_min            
     * @param number $bargain_max            
     * @param boolean $is_closed            
     * @param number $bargain_num_limit            
     * @param boolean $is_both_bargain            
     * @param number $bargain_period            
     * @param string $memo            
     */
    public function create($activity_id, $user_id, $user_name, $user_headimgurl, $code, $name, $worth, $quantity, $bargain_from, $bargain_to, $worth_min, $bargain_max, $is_closed, $bargain_num_limit, $is_both_bargain, \MongoDate $start_time, \MongoDate $end_time, $bargain_period, array $memo = array('memo'=>''))
    {
        return $this->insert(array(
            'activity_id' => $activity_id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_headimgurl' => $user_headimgurl,
            'launch_time' => new \MongoDate(),
            'code' => $code,
            'name' => $name,
            'worth' => intval($worth),
            'current_worth' => intval($worth),
            'quantity' => intval($quantity),
            'bargain_from' => intval($bargain_from),
            'bargain_to' => intval($bargain_to),
            'worth_min' => intval($worth_min),
            'bargain_max' => intval($bargain_max),
            'is_closed' => $is_closed,
            'bargain_num_limit' => intval($bargain_num_limit),
            'is_both_bargain' => $is_both_bargain,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'bargain_period' => $bargain_period,
            'total_bargain_num' => 0,
            'total_bargain_amount' => 0,
            'is_bargain_to_minworth' => false,
            'bargain_to_minworth_time' => new \MongoDate(),
            'memo' => $memo
        ));
    }

    /**
     * 增加砍价总金额和砍价次数
     *
     * @param array $bargainInfo            
     * @param string $identity_id            
     * @param number $amount            
     * @param number $num            
     * @throws Exception
     * @return array
     */
    public function incBargain($bargainInfo, $amount, $num)
    {
        $bargain_id = ($bargainInfo['_id']);
        $options = array();
        $options['query'] = array(
            '_id' => $bargainInfo['_id'],
            'current_worth' => array(
                '$gte' => $amount
            )
        );
        $options['update'] = array(
            '$inc' => array(
                'total_bargain_num' => $num,
                'total_bargain_amount' => $amount,
                'current_worth' => - $amount
            )
        );
        $options['new'] = true; // 返回更新之后的值
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("更新砍价物{$bargain_id}的砍价总金额和砍价次数的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("更新砍价物{$bargain_id}的砍价总金额和砍价次数的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        return $rst['value'];
    }

    public function setBargainToMinworth($bargainInfo)
    {
        // 如果砍到了最低价值的时候，设置一个标志位
        $this->update(array(
            '_id' => $bargainInfo['_id']
        ), array(
            '$set' => array(
                'is_bargain_to_minworth' => true,
                'bargain_to_minworth_time' => time()
            )
        ));
    }

    /**
     * 下线处理
     *
     * @param string $id            
     */
    public function doClosed($id)
    {
        $options = array();
        $options['query'] = array(
            '_id' => ($id),
            'is_closed' => false
        );
        $options['update'] = array(
            '$set' => array(
                'is_closed' => true
            )
        );
        $options['new'] = true; // 返回更新之后的值
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("更新砍价物{$id}的下线处理的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("更新砍价物{$id}的下线处理的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        return $rst['value'];
    }
}
