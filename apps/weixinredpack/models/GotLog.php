<?php
namespace Webcms\Weixinredpack\Models;

class GotLog extends \Webcms\Common\Models\Weixinredpack\GotLog
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
     * @param string $mch_billno            
     * @param string $re_openid            
     * @param array $re_nickname            
     * @param array $re_headimgurl            
     * @param string $client_ip            
     * @param array $activity_id            
     * @param array $customer            
     * @param string $redpack_id            
     * @param string $total_num            
     * @param string $total_amount            
     * @param boolean $isOK            
     * @param string $memo            
     */
    public function record($mch_billno, $user_id, $re_openid, $re_nickname, $re_headimgurl, $client_ip, $activity_id, $customer, $redpack_id, $total_num, $total_amount, $isOK = false, array $memo = array())
    {
        if (empty($memo)) {
            $memo = array(
                'memo' => ""
            );
        }
        return $this->insert(array(
            'mch_billno' => $mch_billno,
            'user_id' => $user_id,
            're_openid' => $re_openid,
            're_nickname' => $re_nickname,
            're_headimgurl' => $re_headimgurl,
            'client_ip' => $client_ip,
            'activity' => $activity_id,
            'customer' => $customer,
            'redpack' => $redpack_id,
            'total_num' => intval($total_num),
            'total_amount' => intval($total_amount),
            'got_time' => getCurrentTime(),
            'isOK' => $isOK,
            'memo' => $memo
        ));
    }

    /**
     * 记录日志处理结果
     *
     * @param array $logInfo            
     * @param boolean $isOK            
     * @param array $errorLog            
     * @param array $memo            
     */
    public function updateIsOK($logInfo, $isOK, array $errorLog = array(), array $memo = array())
    {
        $data = array();
        $data['isOK'] = $isOK;
        if (empty($memo)) {
            $memo = array();
        }
        foreach ($memo as $key => $value) {
            $data["memo.{$key}"] = $value;
        }
        
        $options = array();
        $options['query'] = array(
            '_id' => $logInfo['_id']
        );
        $options['update'] = array(
            '$set' => $data
        );
        if (! empty($errorLog)) {
            $options['update']['$push'] = array(
                'error_logs' => array(
                    '$each' => array(
                        array(
                            'errorLog' => $errorLog,
                            'time' => time()
                        )
                    ),
                    '$sort' => array(
                        'time' => 1
                    ),
                    '$slice' => - 1000
                )
            );
        }
        
        $options['new'] = true; // 返回更新之后的值
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("记录日志处理结果的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("记录日志处理结果的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        return $rst['value'];
    }

    /**
     * 根据OpenID获取红包数量
     *
     * @param string $re_openid            
     * @param string $activity            
     * @param string $customer            
     * @param string $redpack            
     * @return number
     */
    public function getRedpackCountByOpenId($re_openid, $activity, $customer, $redpack, $start_time, $end_time)
    {
        $query = array(
            're_openid' => $re_openid,
            'activity' => $activity,
            'customer' => $customer,
            'redpack' => $redpack
        );
        if (! empty($start_time)) {
            $query['got_time']['$gte'] = getCurrentTime($start_time);
        }
        
        if (! empty($end_time)) {
            $query['got_time']['$lte'] = getCurrentTime($end_time);
        }
        
        return $this->count($query);
    }

    /**
     * 根据OpenID获取红包日志记录信息
     *
     * @param string $re_openid            
     * @param string $activity            
     * @param string $customer            
     * @param string $redpack            
     * @return number
     */
    public function getOneRedpackInfoByOpenId($re_openid, $activity, $customer, $redpack, $start_time, $end_time)
    {
        $query = array(
            're_openid' => $re_openid,
            'activity' => $activity,
            'customer' => $customer,
            'redpack' => $redpack
        );
        if (! empty($start_time)) {
            $query['got_time']['$gte'] = getCurrentTime($start_time);
        }
        
        if (! empty($end_time)) {
            $query['got_time']['$lte'] = getCurrentTime($end_time);
        }
        
        return $this->findOne($query);
    }

    /**
     * 根据UserID获取红包数量
     *
     * @param string $user_id            
     * @param string $activity            
     * @param string $customer            
     * @param string $redpack            
     * @return number
     */
    public function getRedpackCountByUserId($user_id, $activity, $customer, $redpack, $start_time, $end_time)
    {
        $query = array(
            'user_id' => $user_id,
            'activity' => $activity,
            'customer' => $customer,
            'redpack' => $redpack
        );
        if (! empty($start_time)) {
            $query['got_time']['$gte'] = getCurrentTime($start_time);
        }
        
        if (! empty($end_time)) {
            $query['got_time']['$lte'] = getCurrentTime($end_time);
        }
        
        return $this->count($query);
    }

    public function incTryCount($_id, $trycount = 1, array $errorLog = array())
    {
        $query = array();
        $query['_id'] = myMongoId($_id);
        $data = array();
        $data['memo.try_count'] = $trycount;
        $updateData = array(
            '$inc' => $data
        );
        if (! empty($errorLog)) {
            $updateData['$push'] = array(
                'error_logs' => array(
                    '$each' => array(
                        array(
                            'errorLog' => $errorLog,
                            'time' => time()
                        )
                    ),
                    '$sort' => array(
                        'time' => 1
                    ),
                    '$slice' => - 1000
                )
            );
        }
        $this->update($query, $updateData);
    }
}