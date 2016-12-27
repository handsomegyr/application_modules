<?php
namespace App\Weixinredpack\Services;

class Api
{

    private $_activity;

    private $_customer;

    private $_redpack;

    private $_gotLog;

    private $_rule;

    private $_limit;

    private $_reissue;

    private $_rtnMsgType = 'json';

    private $_isInstance = false;

    private $lockBag = array();
    
    // 是否需要发送微信红包,默认是不发送
    public $isNeedSendRedpack = false;
    
    // 发送微信红包所需的配置信息
    public $weixinRedpackSettings = array(
        'appid' => '',
        'secret' => '',
        'mch_id' => '',
        'sub_mch_id' => '',
        'key' => '',
        'access_token' => '',
        'cert.pem' => '',
        'key.pem' => ''
    );

    public function __construct(array $weixinRedpackSettings, $rtnMsgType = 'json')
    {
        $this->weixinRedpackSettings = $weixinRedpackSettings;
        $this->_rtnMsgType = $rtnMsgType;
        
        $this->_redpack = new \App\Weixinredpack\Models\Redpack();
        $this->_gotLog = new \App\Weixinredpack\Models\GotLog();
        $this->_rule = new \App\Weixinredpack\Models\Rule();
        $this->_customer = new \App\Weixinredpack\Models\Customer();
        $this->_limit = new \App\Weixinredpack\Models\Limit();
        $this->_reissue = new \App\Weixinredpack\Models\Reissue();
    }

    /**
     * 做发送微信红包动作
     *
     * @param string $activity_id            
     * @param string $customer_id            
     * @param string $redpack_id            
     * @param string $re_openid            
     * @param number $amount            
     * @param array $info            
     */
    public function sendRedpack($activity_id, $customer_id, $redpack_id, $re_openid, $amount = 0, array $info = array('openid'=>'','nickname'=>'','headimgurl'=>'','re_nickname'=>'','re_headimgurl'=>''))
    {
        $defaultInfo = array(
            'openid' => '', // 参加活动的用户ID
            'nickname' => '', // 参加活动的用户昵称
            'headimgurl' => '', // 参加活动的用户头像
            're_nickname' => '', // 领取红包的用户昵称
            're_headimgurl' => '', // 领取红包的用户头像
            'client_ip' => ''
        );
        if (empty($info)) {
            $info = $defaultInfo;
        } else {
            $info = array_merge($defaultInfo, $info);
        }
        
        if ($this->_isInstance) {
            return $this->error(- 1, "每个实例只能执行一次doSendRedpack方法，如需反复发送微信红包，请分别实例化Api类");
        }
        
        if (empty($activity_id)) {
            return $this->error(- 2, "活动编号为空");
        }
        
        if (empty($customer_id)) {
            return $this->error(- 3, "客户ID为空");
        }
        
        if (empty($redpack_id)) {
            return $this->error(- 4, "红包ID为空");
        }
        
        if (empty($re_openid)) {
            return $this->error(- 5, "微信ID为空");
        }
        
        try {
            
            $this->_isInstance = true;
            
            // 锁定防止高并发
            $lockKey = "sendRedpack_a{$activity_id}_c{$customer_id}_u{$re_openid}";
            // $objLock = new \iLock($lockKey);
            // 不能随便设置过期时间,只能设置成脚本执行时间
            // 如果过期时间设置的过小的话,上个请求的写数据库操作还未完成,但是锁已经过期,所以另一个请求可以进来了,
            // 这样在判断限制条件的时候,由于数据库的写操作还未完成,获取不到数据,通过了检查.导致出现了多发红包的记录
            // if ($objLock->lock()) {
            // return $this->error(- 99, "处于锁定状态，请稍后尝试");
            // }
            
            // 检查客户信息
            $customerInfo = $this->_customer->getInfoById($customer_id);
            if (empty($customerInfo)) {
                return $this->error(- 7, "该客户不存在");
            }
            // 获取客户的余额
            $customerRemainAmount = $this->_customer->getRemainAmount($customerInfo);
            if ($customerRemainAmount <= 0) {
                return $this->error(- 12, "该客户余额不足");
            }
            // 如果指定了发放红包金额并且客户余额已经小于发放金额的时候
            if ($amount > 0 && $customerRemainAmount < $amount) {
                return $this->error(- 12, "该客户余额不足");
            }
            
            // 检查红包信息
            $redpackInfo = $this->_redpack->getInfoById($redpack_id);
            if (empty($redpackInfo)) {
                return $this->error(- 8, "该红包不存在");
            }
            
            // 检查微信红包获取情况和红包获得限制条件的关系
            $this->_limit->setLogModel($this->_gotLog);
            $limit = $this->_limit->checkLimit($activity_id, $customer_id, $redpack_id, $re_openid);
            if ($limit == false) {
                return $this->error(- 10, "你未满足获取红包的条件");
            }
            
            // 检查中奖规则，检测用户是否中奖
            $rule = $this->_rule->getValidRule($activity_id, $customer_id, $redpack_id);
            if ($rule == false) {
                return $this->error(- 11, "你来晚了,红包已发完");
            }
            
            // 获取最新的订单号
            // 商户订单号（每个必须唯一）组成： mch_id+yyyymmdd+10 位一天内 不重复
            $rand = mt_rand(0, 9999999999);
            $mch_billno = $this->weixinRedpackSettings["mch_id"] . date('Ymd') . str_pad($rand, 10, '0', STR_PAD_LEFT);
            $client_ip = empty($defaultInfo['client_ip']) ? getIp() : $defaultInfo['client_ip']; // '203.166.161.25';
            $total_num = 1; // $rule['personal_can_get_num'];
            
            if ($amount <= 0) {
                // 如果是小于等于0
                $randAmount = rand($rule['min_cash'], $rule['max_cash']);
                $total_amount = $total_num * $randAmount;
            } else {
                // 大于0的时候
                $total_amount = $total_num * $rule['max_cash'];
                $total_amount = min($amount, $total_amount);
            }
            $total_amount = min($rule['amount'], $customerRemainAmount, $total_amount);
            
            $min_value = $total_amount;
            $max_value = $total_amount;
            
            $nick_name = empty($rule['nick_name']) ? $customerInfo['nick_name'] : $rule['nick_name'];
            $send_name = empty($rule['send_name']) ? $customerInfo['send_name'] : $rule['send_name'];
            $wishing = $rule['wishing'];
            $act_id = $redpackInfo['code'];
            $act_name = $redpackInfo['name'];
            $remark = $rule['remark'];
            $logo_imgurl = empty($rule['logo_imgurl']) ? "" : $rule['logo_imgurl'];
            $share_content = empty($rule['share_content']) ? "" : $rule['share_content'];
            $share_url = empty($rule['share_url']) ? "" : $rule['share_url'];
            $share_imgurl = empty($rule['share_imgurl']) ? "" : $rule['share_imgurl'];
            
            // 记录LOG信息
            $isOK = false;
            $logMemo = array(
                'openid' => $info['openid'], // 参加活动的用户ID
                'nickname' => $info['nickname'], // 参加活动的用户昵称
                'headimgurl' => $info['headimgurl'], // 参加活动的用户头像
                'nick_name' => $nick_name,
                'send_name' => $send_name,
                'min_value' => $min_value,
                'max_value' => $max_value,
                'wishing' => $wishing,
                'act_id' => $act_id,
                'act_name' => $act_name,
                'remark' => $remark,
                'logo_imgurl' => $logo_imgurl,
                'share_content' => $share_content,
                'share_url' => $share_url,
                'share_imgurl' => $share_imgurl
            ); // 是否需要发送微信红包,默认是不发送
            
            $try_count = 0; // 尝试次数,
            $is_reissue = false; // 是否有资格补发红包
            $logInfo = $this->_gotLog->record($mch_billno, $re_openid, $info['re_nickname'], $info['re_headimgurl'], $client_ip, $activity_id, $customer_id, $redpack_id, $total_num, $total_amount, $this->isNeedSendRedpack, $isOK, $try_count, $is_reissue, $logMemo);
            
            // 更新该规则的剩余数量和金额
            $this->_rule->updateRemain($rule, $total_amount, $total_num);
            
            // 更新客户的使用金额
            $this->_customer->incUsedAmount($customer_id, $total_amount);
            
            // 通过以上2步骤的话,说明他是有资格领取微信红包的,事后可以补发
            $logInfoUpdateData = array(
                'is_reissue' => true
            );
            $this->_gotLog->update(array(
                '_id' => $logInfo['_id']
            ), array(
                '$set' => $logInfoUpdateData
            ));
            
            try {
                // 调用微信红包发送接口
                $ret = $this->sendWeixinRedpack($mch_billno, $nick_name, $send_name, $re_openid, $total_amount, $min_value, $max_value, $total_num, $wishing, $client_ip, $act_id, $act_name, $remark, $logo_imgurl, $share_content, $share_url, $share_imgurl);
                
                // 处理结果
                $isOK = true;
                $errorLog = array();
            } catch (\Exception $e) {
                // 处理结果
                $isOK = false;
                $ret['error_code'] = $e->getCode();
                if (empty($ret['error_code'])) {
                    $ret['error_code'] = - 8888;
                }
                $ret['error_msg'] = $e->getMessage();
                $errorLog = $ret;
            }
            // 更新LOG信息
            $memo = array_merge($ret, $logInfo);
            $logInfo = $this->_gotLog->updateIsOK($logInfo, $isOK, $errorLog, $memo);
            
            if ($isOK) {
                return $this->result("OK", convertToPureArray($logInfo));
            } else {
                return $this->error($ret['error_code'], $ret['error_msg']);
            }
        } catch (\Exception $e) {
            $error_code = $e->getCode();
            if (empty($error_code)) {
                $error_code = - 9999;
            }
            return $this->error($error_code, $e->getMessage());
        }
    }

    public function result($msg = '', $result = '')
    {
        $rst = array(
            'success' => true,
            'message' => $msg,
            'result' => $result
        );
        if ($this->_rtnMsgType == 'json') {
            return json_encode($rst);
        } else {
            return $rst;
        }
    }

    public function error($code, $msg)
    {
        $rst = array(
            'success' => false,
            'error_code' => $code,
            'error_msg' => $msg
        );
        
        if ($this->_rtnMsgType == 'json') {
            return json_encode($rst);
        } else {
            return $rst;
        }
    }

    /**
     * 调用微信接口发红包
     *
     * @param array $customerInfo            
     * @param string $mch_billno            
     * @param string $nick_name            
     * @param string $send_name            
     * @param string $re_openid            
     * @param number $total_amount
     *            单位分
     * @param number $min_value
     *            单位分
     * @param number $max_value
     *            单位分
     * @param number $total_num            
     * @param string $wishing            
     * @param string $client_ip            
     * @param string $act_id            
     * @param string $act_name            
     * @param string $remark            
     * @param string $logo_imgurl            
     * @param string $share_content            
     * @param string $share_url            
     * @param string $share_imgurl            
     */
    private function sendWeixinRedpack($mch_billno, $nick_name, $send_name, $re_openid, $total_amount, $min_value, $max_value, $total_num, $wishing, $client_ip, $act_id, $act_name, $remark, $logo_imgurl = "", $share_content = "", $share_url = "", $share_imgurl = "")
    {
        /**
         * $nick_name = "国泰广告-nname";
         * $send_name = "国泰广告-sname";
         * $re_openid = "oFEX-joe9BYUKqluMFux104CxRNE";
         * // $re_openid = "oFEX-jkiGkPL2j8EnqCz_nCZ0GlU";//杨明
         * $total_amount = 100; // 单位分
         * $min_value = 100;
         * $max_value = 100;
         * $total_num = 1;
         * $wishing = "祝愿";
         * $client_ip = getIp();
         * $act_id = "20088";
         * $act_name = "活动名称xxxx";
         * $remark = "杨明活动备注xxx";
         * $logo_imgurl = "http://mmbiz.qpic.cn/mmbiz/iaAQwicknkictTEYUBmw9dkEn1qInaDiay9vndVdksyF1FVNcc0RrwO8ias2xwCfwcX4RLSdq8KPxF1SOW3yckvMibpg/0";
         * $share_content = "分享内容xxx";
         * $share_url = "http://www.baidu.com/";
         * $share_imgurl = "http://mmbiz.qpic.cn/mmbiz/iaAQwicknkictTEYUBmw9dkEn1qInaDiay9vndVdksyF1FVNcc0RrwO8ias2xwCfwcX4RLSdq8KPxF1SOW3yckvMibpg/0";
         */
        if ($this->isNeedSendRedpack) {
            // 获取accesstoken
            $accessToken = $this->weixinRedpackSettings['access_token'];
            $appid = $this->weixinRedpackSettings["appid"];
            $secret = $this->weixinRedpackSettings["secret"];
            $mchid = $this->weixinRedpackSettings["mch_id"]; // "1220225801";
            $sub_mch_id = empty($this->weixinRedpackSettings["sub_mch_id"]) ? '' : $this->weixinRedpackSettings["sub_mch_id"];
            $key = $this->weixinRedpackSettings["key"]; // "NG4HWVH26C733KWK6F98J8CK4BN3D2R7";
                                                        
            // $cert = APPLICATION_PATH . "/../cert/weixinpay337/apiclient_cert.pem";
                                                        
            // $certKey = APPLICATION_PATH . "/../cert/weixinpay337/apiclient_key.pem";
            
            $fileName = $this->weixinRedpackSettings['cert.pem'];
            if (file_exists($fileName)) {
                $cert = $fileName;
            } else {
                throw new \Exception('cert.pem is not exist');
            }
            
            $fileName = $this->weixinRedpackSettings['key.pem'];
            if (file_exists($fileName)) {
                $certKey = $fileName;
            } else {
                throw new \Exception('key.pem is not exist');
            }
            
            $objWeixinPay = new \Weixin\Pay337();
            $objWeixinPay->setAppId($appid);
            $objWeixinPay->setAppSecret($secret);
            $objWeixinPay->setAccessToken($accessToken);
            $objWeixinPay->setMchid($mchid);
            $objWeixinPay->setSubMchId($sub_mch_id);
            $objWeixinPay->setKey($key);
            $objWeixinPay->setCert($cert);
            $objWeixinPay->setCertKey($certKey);
            $nonce_str = \Weixin\Helpers::createNonceStr(32);
            $ret = $objWeixinPay->sendredpack($nonce_str, $mch_billno, $nick_name, $send_name, $re_openid, $total_amount, $min_value, $max_value, $total_num, $wishing, $client_ip, $act_id, $act_name, $remark, $logo_imgurl, $share_content, $share_url, $share_imgurl);
        } else {
            // sleep(1);
            $ret = array(
                'is_test' => '测试用',
                'total_amount' => $total_amount,
                'return_code' => 'SUCCESS',
                'return_msg' => '发放成功',
                'result_code' => 'SUCCESS',
                'err_code' => '0',
                'err_code_des' => 'SUCCESS',
                'mch_billno' => $mch_billno,
                're_openid' => $re_openid
            );
        }
        return $ret;
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {}

    /**
     * 以5分钟运行一次计划任务,补发红包
     */
    public function cron()
    {
        if (empty($this->isNeedSendRedpack)) {
            throw new \Exception('发送微信红包没有打开,请设置isNeedSendRedpack为true', - 9777);
        }
        
        $errorList = array();
        $successList = array();
        $preTime = time() - 2 * 60; // 2分钟之前的记录
        $query = array(
            'isOK' => false,
            'is_reissue' => true,
            'got_time' => array(
                '$lte' => getCurrentTime($preTime)
            ),
            'try_count' => array(
                '$lte' => 2
            )
        );
        
        $list = $this->_gotLog->find($query, array(
            'try_count' => 1,
            'got_time' => 1,
            '_id' => 1
        ), 0, 49);
        
        if (! empty($list['datas'])) {
            foreach ($list['datas'] as $logInfo) {
                $isOK = false;
                $ret = array();
                $logId = myMongoId($logInfo['_id']);
                try {
                    $activity_id = $logInfo['activity'];
                    $customer_id = $logInfo['customer'];
                    $redpack_id = $logInfo['redpack'];
                    $re_openid = $logInfo['re_openid'];
                    $re_nickname = $logInfo['re_nickname'];
                    $re_headimgurl = $logInfo['re_headimgurl'];
                    $mch_billno = $logInfo['mch_billno'];
                    $total_num = $logInfo['total_num'];
                    $total_amount = $logInfo['total_amount'];
                    $client_ip = $logInfo['client_ip'];
                    $nick_name = $logInfo['memo']['nick_name'];
                    $send_name = $logInfo['memo']['send_name'];
                    $min_value = $logInfo['memo']['min_value'];
                    $max_value = $logInfo['memo']['max_value'];
                    $wishing = $logInfo['memo']['wishing'];
                    $act_id = $logInfo['memo']['act_id'];
                    $act_name = $logInfo['memo']['act_name'];
                    $remark = $logInfo['memo']['remark'];
                    $logo_imgurl = $logInfo['memo']['logo_imgurl'];
                    $share_content = $logInfo['memo']['share_content'];
                    $share_url = $logInfo['memo']['share_url'];
                    $share_imgurl = $logInfo['memo']['share_imgurl'];
                    
                    $customerInfo = $this->_customer->getInfoById($customer_id);
                    if (empty($customerInfo)) {
                        throw new \Exception('客户信息不存在');
                    }
                    $redpackInfo = $this->_redpack->getInfoById($redpack_id);
                    if (empty($redpackInfo)) {
                        throw new \Exception('红包信息不存在');
                    } else {
                        $act_id = $redpackInfo['code'];
                        $act_name = $redpackInfo['name'];
                    }
                    
                    // 调用微信红包发送接口
                    $ret = $this->sendWeixinRedpack($mch_billno, $nick_name, $send_name, $re_openid, $total_amount, $min_value, $max_value, $total_num, $wishing, $client_ip, $act_id, $act_name, $remark, $logo_imgurl, $share_content, $share_url, $share_imgurl);
                    
                    // 处理结果
                    $isOK = true;
                    $errorLog = array();
                    // 更新LOG信息
                    $newlogInfo = $this->_gotLog->updateIsOK($logInfo, $isOK, $errorLog, $ret);
                    $successList[] = $newlogInfo;
                    // 记录补发日志
                    unset($logInfo['_id']);
                    $this->_reissue->record($logId, $logInfo);
                } catch (\Exception $e) {
                    // 处理结果
                    $isOK = false;
                    $ret['error_code'] = $e->getCode();
                    if (empty($ret['error_code'])) {
                        $ret['error_code'] = - 9999;
                    }
                    $ret['error_msg'] = "日志记录ID为{$logId}的处理失败:" . $e->getMessage();
                    $errorLog = $ret;
                    $this->_gotLog->incTryCount($logId, 1, $errorLog);
                    $errorList[] = $ret;
                }
            }
        }
        return array(
            'errorList' => $errorList,
            'successList' => $successList
        );
    }
}

