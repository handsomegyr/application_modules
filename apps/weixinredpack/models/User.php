<?php
namespace Webcms\Weixinredpack\Models;

class User extends \Webcms\Common\Models\Weixinredpack\User
{

    private $_keys = array(
        'openid',
        'nickname',
        'headimgurl'
    );

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
     * 根据openid 获取信息
     *
     * @param string $openid            
     * @return array
     */
    public function getInfoByOpenid($openid)
    {
        $query = array(
            'openid ' => $openid
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 格式化信息
     *
     * @param array $info            
     * @return array
     */
    private function formatInfo($info)
    {
        $rst = array();
        foreach ($this->_keys as $key) {
            if (isset($info[$key])) {
                $rst[$key] = $info[$key];
            }
        }
        return $rst;
    }

    /**
     * 记录微信红包的获取用户
     *
     * @param string $info            
     */
    public function record($re_openid, $info)
    {
        $info = $this->formatInfo($info);
        $query['openid'] = (string) $re_openid;
        $info = array_merge($info, $query);
        
        $identityInfo = $this->findOne($query);
        if ($identityInfo != null) {
            $query['_id'] = $identityInfo['_id'];
            $info['__MODIFY_TIME__'] = getCurrentTime();
        } else {
            if (! isset($info['total_amount'])) {
                $info['total_amount'] = 0;
            }
            if (! isset($info['total_num'])) {
                $info['total_num'] = 0;
            }
            $info['lock'] = false;
            $info['__CREATE_TIME__'] = $info['__MODIFY_TIME__'] = getCurrentTime();
        }
        
        $options = array();
        $options['query'] = $query;
        $options['update'] = array(
            '$set' => $info
        );
        $options['new'] = true;
        $options['upsert'] = true;
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("微信红包的获取用户为:{$re_openid}的记录操作失败" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("微信红包的获取用户为:{$re_openid}的记录操作失败" . json_encode($rst));
        }
        return $rst['value'];
    }

    /**
     * 根据ID获取红包数量
     *
     * @param string $id            
     * @param string $activity            
     * @param string $customer            
     * @param string $redpack            
     * @return number
     */
    public function getRedpackCountById($id, $activity, $customer, $redpack, $start_time, $end_time)
    {
        $info = $this->getInfoById($id);
        
        if (empty($info)) {
            throw new \Exception("用户信息不存在");
        } else {
            if (! empty($info['redpacklogs'])) {
                if ($redpack === "all") {
                    $findme = "a{$activity}_c{$customer}_rp";
                    $redpackNum = 0;
                    foreach ($info['redpacklogs'] as $key => $log) {
                        $pos = strpos($key, $findme);
                        // 注意这里使用的是 ===。简单的 == 不能像我们期待的那样工作，
                        // 因为 'a' 是第 0 位置上的（第一个）字符。
                        if ($pos === false) {
                            ;
                        } else {
                            foreach ($log as $item) {
                                if ($item['log_time'] >= $start_time && $item['log_time'] <= $end_time) {
                                    $redpackNum += $item['num'];
                                }
                            }
                        }
                    }
                    return $redpackNum;
                } else {
                    $key = "a{$activity}_c{$customer}_rp{$redpack}";
                    if (key_exists($key, $info['redpacklogs'])) {
                        $redpackNum = 0;
                        foreach ($info['redpacklogs'][$key] as $item) {
                            if ($item['log_time'] >= $start_time && $item['log_time'] <= $end_time) {
                                $redpackNum += $item['num'];
                            }
                        }
                        return $redpackNum;
                    } else {
                        return 0;
                    }
                }
            } else {
                return 0;
            }
        }
    }

    /**
     * 记录微信红包的log
     *
     * @param string $info            
     */
    public function recordRedpackLog($user_id, $activity, $customer, $redpack, $activity_openid, $activity_nickname, $activity_headimgurl, $total_num, $total_amount, $mch_billno, $log_id, $log_time)
    {
        $total_num = intval($total_num);
        $total_amount = intval($total_amount);
        $query = array();
        $query['_id'] = myMongoId($user_id);
        $info = array();
        $info['activity_openid'] = $activity_openid;
        $info['activity_nickname'] = $activity_nickname;
        $info['activity_headimgurl'] = $activity_headimgurl;
        $info['num'] = $total_num;
        $info['amount'] = $total_amount;
        $info['mch_billno'] = $mch_billno;
        $info['log_id'] = $log_id;
        $info['log_time'] = $log_time;
        
        $key = "a{$activity}_c{$customer}_rp{$redpack}";
        $options = array();
        $options['query'] = $query;
        
        $options['update'] = array(
            '$push' => array(
                'redpacklogs.' . $key => array(
                    '$each' => array(
                        $info
                    ),
                    '$sort' => array(
                        'time' => 1
                    ),
                    '$slice' => - 1000
                )
            ),
            '$inc' => array(
                'total_amount' => $total_amount,
                'total_num' => $total_num
            )
        );
        $options['new'] = true;
        $options['upsert'] = true;
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("微信红包的获取用户为:{$user_id}的记录获取红包log操作失败" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("微信红包的获取用户为:{$user_id}的记录获取红包log操作失败" . json_encode($rst));
        }
        return $rst['value'];
    }

    /**
     * 加锁
     *
     * @param string $id            
     * @param boolean $isExclusive            
     * @throws Exception
     * @return boolean
     */
    public function lock($id)
    {
        // 锁定之前，先清除过期锁
        $this->expire($id);
        
        $query = array(
            '_id' => myMongoId($id),
            'lock' => false
        );
        
        $options = array();
        $options['query'] = $query;
        $options['update'] = array(
            '$set' => array(
                'lock' => true,
                'expire' => getCurrentTime(time() + 300)
            )
        );
        $options['new'] = false; // 返回更新之前的值
        
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("findandmodify失败");
        }
        
        if (empty($rst['value'])) {
            // 已经被锁定
            return true;
        } else {
            // 未被加锁，但是现在已经被锁定
            return false;
        }
    }

    /**
     * 解锁
     *
     * @param string $id            
     */
    public function unlock($id)
    {
        return $this->update(array(
            '_id' => myMongoId($id)
        ), array(
            '$set' => array(
                'lock' => false,
                'expire' => getCurrentTime()
            )
        ));
    }

    /**
     * 自动清除过期的锁
     *
     * @param string $id            
     */
    public function expire($id)
    {
        return $this->update(array(
            '_id' => myMongoId($id),
            'expire' => array(
                '$lte' => getCurrentTime()
            )
        ), array(
            '$set' => array(
                'lock' => false
            )
        ));
    }
}