<?php
namespace App\Points\Models;

class User extends \App\Common\Models\Points\User
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'code' => 1
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
     * 根据用户ID获取用户信息
     *
     * @param string $user_id            
     * @param number $category            
     * @return array
     */
    public function getInfoByUserId($user_id, $category)
    {
        $info = $this->findOne(array(
            'user_id' => strval($user_id),
            'category' => intval($category)
        ));
        return $info;
    }

    /**
     * 根据用户IDs获取用户列表信息
     *
     * @param string $user_id            
     * @param number $category            
     * @return array
     */
    public function getListByUserIds(array $user_ids, $category)
    {
        $query = array(
            'user_id' => array(
                '$in' => $user_ids
            ),
            'category' => intval($category)
        );
        $sort = array(
            '_id' => - 1
        );
        $ret = $this->findAll($query, $sort);
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $item) {
                $list[$item['user_id']] = $item;
            }
        }
        return $list;
    }

    /**
     * 添加或消耗积分
     *
     * @param string $category            
     * @param string $user_id            
     * @param string $user_name            
     * @param string $uniqueId            
     * @param \MongoDate $add_time            
     * @param number $points            
     * @param string $stage            
     * @param string $desc            
     */
    public function addOrReduce($category, $user_id, $user_name, $user_headimgurl, $uniqueId, $add_time, $points, $stage, $desc)
    {
        if (empty($add_time)) {
            $add_time = getCurrentTime();
        }
        $lockKey = cacheKey(__FILE__, __CLASS__, __METHOD__, $uniqueId, $category);
        $objLock = new \iLock($lockKey);
        // 检查是否已经加锁了
        if ($objLock->lock()) {
            return;
        }
        $modelLog = new Log();
        $logInfo = $modelLog->getInfoByUniqueId($uniqueId, $category);
        if (! empty($logInfo)) {
            return;
        }
        
        $points = intval($points);
        if ($points >= 0) {
            // 增加积分处理
            $is_consumed = false;
            $data = array();
            $data['point_time'] = $add_time;
            $options = array();
            $options['query'] = array(
                'user_id' => $user_id,
                'category' => $category
            );
            $options['update'] = array(
                '$set' => $data,
                '$inc' => array(
                    'current' => $points,
                    'total' => $points
                )
            );
            $options['new'] = true; // 返回更新之后的值
            $rst = $this->findAndModify($options);
            if (empty($rst['ok'])) {
                throw new \Exception("添加积分的findAndModify执行错误，返回结果为:" . json_encode($rst), 804);
            }
            if (empty($rst['value'])) {
                throw new \Exception("添加积分的findAndModify执行错误，返回结果为:" . json_encode($rst), 804);
            }
        } else {
            // 消费积分处理
            $points = abs($points);
            $is_consumed = true;
            $data = array();
            $data['point_time'] = $add_time;
            $options = array();
            $options['query'] = array(
                'user_id' => $user_id,
                'category' => $category,
                'current' => array(
                    '$gte' => $points
                )
            );
            $options['update'] = array(
                '$set' => $data,
                '$inc' => array(
                    'current' => - $points,
                    'consume' => $points
                )
            );
            $options['new'] = true; // 返回更新之后的值
            $rst = $this->findAndModify($options);
            if (empty($rst['ok'])) {
                throw new \Exception("消费积分的findAndModify执行错误，返回结果为:" . json_encode($rst), 804);
            }
            if (empty($rst['value'])) {
                throw new \Exception("消费积分的findAndModify执行错误，返回结果为:" . json_encode($rst), 804);
            }
        }
        // 记录积分日志
        $modelLog->log($category, $user_id, $user_name, $user_headimgurl, $uniqueId, $is_consumed, $add_time, $points, $stage, $desc);
        return $rst['value'];
    }

    /**
     * 根据user_id生成或获取记录
     *
     * @param number $category            
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param number $current            
     * @param number $freeze            
     * @param number $consume            
     * @param number $expire            
     * @param array $memo            
     * @return array
     */
    public function getOrCreateByUserId($category, $user_id, $user_name, $user_headimgurl, $current = 0, $freeze = 0, $consume = 0, $expire = 0, array $memo = array('memo'=>''))
    {
        $info = $this->getInfoByUserId($user_id, $category);
        if (empty($info)) {
            $info = $this->create($category, $user_id, $user_name, $user_headimgurl, $current, $freeze, $consume, $expire, $memo);
        }
        return $info;
    }

    /**
     * 生成记录
     *
     * @param number $category            
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param number $current            
     * @param number $freeze            
     * @param number $consume            
     * @param number $expire            
     * @param array $memo            
     * @return array
     */
    public function create($category, $user_id, $user_name, $user_headimgurl, $current = 0, $freeze = 0, $consume = 0, $expire = 0, array $memo = array('memo'=>''))
    {
        $data = array();
        $data['category'] = $category; // 积分分类
        $data['user_id'] = $user_id; // 微信ID
        $data['user_name'] = $user_name; // 用户名
        $data['user_headimgurl'] = $user_headimgurl; // 头像
        $data['current'] = $current; // 积分
        $data['total'] = $current; // 总积分
        $data['freeze'] = $freeze; // 冻结积分
        $data['consume'] = $consume; // 消费积分
        $data['expire'] = $expire; // 过期积分
        $data['point_time'] = getCurrentTime(); // 积分时间
        $data['memo'] = $memo; // 备注
        $info = $this->insert($data);
        return $info;
    }
}