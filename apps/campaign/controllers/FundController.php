<?php

/**
 * 基金财富号
 * https://cloud.huaan.com.cn/campaign/fund/alipayauthorizebefore?operation4cookie=store&user_id=xxx&nickname=xxx&headimgurl=xxx
 * 
 * https://cloud.huaan.com.cn/campaign/fund/alipayauthorizebefore?callbackUrl=https%3A%2F%2Fwww.baidu.com%2F
 * 
 * https://cloud.huaan.com.cn/one/index.html
 * 
 * https://cloud.huaan.com.cn/campaign/fund/alipayauthorizebefore?operation4cookie=clear
 * 
 * @author 郭永荣
 *
 */
class FundController extends ControllerBase
{
    // 用于签名认证
    protected $secretKey = "170908fg0353";
    
    // 当前时间
    protected $now = null;
    
    // 活动
    protected $modelActivity = null;
    // 活动错误日志
    protected $modelActivityErrorLog = null;
    // 活动用户
    protected $modelActivityUser = null;
    // 活动黑名单用户
    protected $modelActivityBlackUser = null;
    
    // 活动相关
    // 活动1
    protected $activity1 = '59bf89689fe152002d05b2a8';
    
    // 抽奖相关
    // 抽奖活动1
    protected $lottery_activity_id1 = '59bf88b6d3df9000dd012a26';

    public function initialize()
    {
        $this->now = getCurrentTime();
        
        $this->modelActivity = new \App\Activity\Models\Activity();
        $this->modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $this->modelActivityUser = new \App\Activity\Models\User();
        $this->modelActivityBlackUser = new \App\Activity\Models\BlackUser();
        
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 获取用户信息的接口
     */
    public function getcampaignuserinfoAction()
    {
        // http://www.applicationmodule.com/campaign/sign/getcampaignuserinfo
        try {
            $userInfo = empty($_COOKIE['Alipay_userInfo']) ? array() : json_decode($_COOKIE['Alipay_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(- 999, "用户信息为空");
                return false;
            }
            
            $user_id = trim($userInfo['user_id']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);
            
            // 检查cookie的有效性
            $isValid = true; // $this->validateOpenid($user_id, $timestamp, $this->secretKey, $signkey);
            if (! $isValid) {
                echo $this->error(- 999, "用户信息是伪造的");
                return false;
            }
            
            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $user_id);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(- 888, "上次操作还未完成,请等待");
                return false;
            }
            
            // 获取活动用户
            $memo = array(
                'is_correct' => false,
                'is_answer' => false
            );
            $userInfo = $this->modelActivityUser->getOrCreateByUserId($user_id, $nickname, $headimgurl, '', '', 0, $this->activity1, $memo);
            if (empty($userInfo)) {
                echo $this->error(- 40491, 'user_id不正确');
                return false;
            }
            if (empty($nickname)) {
                $nickname = $userInfo['nickname'];
            }
            if (empty($headimgurl)) {
                $headimgurl = $userInfo['headimgurl'];
            }
            
            $ret = array();
            // 活动用户信息
            $ret['userInfo'] = array(
                // 是否回答正确
                'is_correct' => empty($userInfo['memo']['is_correct']) ? 0 : 1,
                // 是否回答过问题
                'is_answer' => empty($userInfo['memo']['is_answer']) ? 0 : 1
            );
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            $this->modelActivityErrorLog->log($this->activity1, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 回答问题发红包的接口
     */
    public function answerAction()
    {
        // https://cloud.huaan.com.cn/campaign/fund/answer?user_id=oQWzx0Ek-IteVNwoVbMk-15bVUX4&nickname=xxx&headimgurl=xxx&answer=xxx
        try {
            $answer = intval($this->get('answer', '0'));
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo($this->activity1, $this->now->sec);
            
            // 活动是否开始了
            if (empty($activityInfo['is_activity_started'])) {
                echo $this->error(- 40401, "活动未开始");
                return false;
            }
            // 活动是否暂停
            if (! empty($activityInfo['is_actvity_paused'])) {
                echo $this->error(- 40402, "活动已暂停");
                return false;
            }
            // 活动是否结束了
            if (! empty($activityInfo['is_activity_over'])) {
                echo $this->error(- 40403, "活动已结束");
                return false;
            }
            
            $userInfo = empty($_COOKIE['Alipay_userInfo']) ? array() : json_decode($_COOKIE['Alipay_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(- 40497, "用户信息为空");
                return false;
            }
            
            $user_id = trim($userInfo['user_id']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);
            
            // 检查cookie的有效性
            $isValid = $this->validateOpenid($user_id, $timestamp, $this->secretKey, $signkey);
            if (! $isValid) {
                echo $this->error(- 40498, "用户信息是伪造的");
                return false;
            }
            
            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $user_id);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(- 40499, "上次操作还未完成,请等待");
                return false;
            }
            
            $userInfo = $this->modelActivityUser->getInfoByUserId($user_id, $this->activity1);
            if (empty($userInfo)) {
                echo $this->error(- 40491, 'user_id不正确');
                return false;
            }
            // 检查是否已经回答过问题
            if (! empty($userInfo['memo']['is_answer'])) {
                echo $this->error(- 40492, '该用户已做过');
                return false;
            }
            
            $config = Zend_Registry::get('config');
            $modelApliayApplication = new Alipay_Model_Application();
            $appConfig = $modelApliayApplication->getApplicationInfoByAppId($config['alipay']['appid']);
            if (empty($appConfig)) {
                throw new \Exception('appid所对应的记录不存在');
            }
            
            // 是否答对题目
            $is_correct = ($answer === 3);
            $data = array();
            $data['memo.is_answer'] = true;
            $data['memo.is_correct'] = $is_correct;
            $this->modelActivityUser->update(array(
                '_id' => $userInfo['_id']
            ), array(
                '$set' => $data
            ));
            
            // 检查是否答对题目
            if (! $is_correct) {
                echo $this->error(- 10, "回答不正确");
                return false;
            }
            
            // 抽奖
            $ret = $this->getCoupon($userInfo, '', $appConfig);
            if (empty($ret)) {
                echo $this->error(- 20, "来晚了一步");
                return false;
            }
            
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            $this->modelActivityErrorLog->log($this->activity1, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 华安基金
     */
    public function fundperformancechart2Action()
    {
        // https://cloud.huaan.com.cn/campaign/fund/fundperformancechart2?fundcodes=040008
        try {
            $fundcodes = $this->get('fundcodes', '');
            
            if (empty($fundcodes)) {
                echo $this->error(- 3, 'fundcodes未指定');
                return false;
            }
            
            $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__, $fundcodes);
            $cache = Zend_Registry::get('cache');
            $ret = $cache->load($cacheKey);
            if (empty($ret)) {
                $config = Zend_Registry::get('config');
                $modelApliayApplication = new Alipay_Model_Application();
                $appConfig = $modelApliayApplication->getApplicationInfoByAppId($config['alipay']['appid']);
                if (empty($appConfig)) {
                    throw new \Exception('appid所对应的记录不存在');
                }
                
                // 调用支付宝相应接口
                $objiAlipay = new \iAlipay($appConfig['app_id'], $appConfig['merchant_private_key'], $appConfig['merchant_public_key'], $appConfig['alipay_public_key'], $appConfig['charset'], $appConfig['gatewayUrl'], $appConfig['sign_type']);
                $ret = $objiAlipay->alipayFinanceFundFundquotationQueryRequest($fundcodes);
                
                $cache->save($ret, $cacheKey, array(), 60 * 5);
            }
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            $this->modelActivityErrorLog->log($this->activity1, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 测试1
     */
    public function test1Action()
    {
        // https://cloud.huaan.com.cn/campaign/fund/test1&camp_id=170921821480493
        try {
            $user_id = $this->get('user_id', '2088602345138428');
            $camp_id = $this->get('camp_id', '170921821480493');
            
            $this->_app = new Alipay_Model_Application();
            $this->_appConfig = $this->_app->getApplicationInfoByAppId('2017071707783020');
            if (empty($this->_appConfig)) {
                throw new \Exception('appid所对应的记录不存在');
            }
            
            $objiAlipay = new \iAlipay($this->_appConfig['app_id'], $this->_appConfig['merchant_private_key'], $this->_appConfig['merchant_public_key'], $this->_appConfig['alipay_public_key'], $this->_appConfig['charset'], $this->_appConfig['gatewayUrl'], $this->_appConfig['sign_type']);
            $ret = $objiAlipay->alipayMarketingCampaignDrawcampTriggerRequest($user_id, $camp_id);
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            $this->modelActivityErrorLog->log($this->activity1, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 测试2
     */
    public function test2Action()
    {
        // https://cloud.huaan.com.cn/campaign/fund/test2?camp_id=170921821480493&prize_id=xx
        try {
            $camp_id = $this->get('camp_id', '170921821480493');
            $prize_id = $this->get('prize_id', '20170921000730018232000BP4C6');
            
            $this->_app = new Alipay_Model_Application();
            $this->_appConfig = $this->_app->getApplicationInfoByAppId('2017071707783020');
            if (empty($this->_appConfig)) {
                throw new \Exception('appid所对应的记录不存在');
            }
            
            $objiAlipay = new \iAlipay($this->_appConfig['app_id'], $this->_appConfig['merchant_private_key'], $this->_appConfig['merchant_public_key'], $this->_appConfig['alipay_public_key'], $this->_appConfig['charset'], $this->_appConfig['gatewayUrl'], $this->_appConfig['sign_type']);
            $ret = $objiAlipay->alipayMarketingCampaignPrizeAmountQueryRequest($camp_id, $prize_id);
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            $this->modelActivityErrorLog->log($this->activity1, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 测试3
     */
    public function test3Action()
    {
        // https://cloud.huaan.com.cn/campaign/fund/test3?camp_id=170921821480493
        try {
            $camp_id = $this->get('camp_id', '170921821480493');
            
            $this->_app = new Alipay_Model_Application();
            $this->_appConfig = $this->_app->getApplicationInfoByAppId('2017071707783020');
            if (empty($this->_appConfig)) {
                throw new \Exception('appid所对应的记录不存在');
            }
            
            $objiAlipay = new \iAlipay($this->_appConfig['app_id'], $this->_appConfig['merchant_private_key'], $this->_appConfig['merchant_public_key'], $this->_appConfig['alipay_public_key'], $this->_appConfig['charset'], $this->_appConfig['gatewayUrl'], $this->_appConfig['sign_type']);
            $ret = $objiAlipay->alipayMarketingCampaignDrawcampQueryRequest($camp_id);
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            $this->modelActivityErrorLog->log($this->activity1, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 抽奖处理
     */
    private function doLottery($userInfo, $activity_id, $uniqueId, $prize_id = "", array $exclude_prize_ids = array(), array $info = array(), array $identityContact = array())
    {
        $modelLotteryServiceApi = new Lottery_Model_Service_Api("nojson");
        
        $prize_ids = array();
        if (! empty($prize_id)) {
            $prize_ids[] = $prize_id;
        }
        
        $lotteryResult = $modelLotteryServiceApi->doLottery($activity_id, $uniqueId, Lottery_Model_Identity::SOURCE_WEIXIN, $info, "", $prize_ids, $exclude_prize_ids, $identityContact);
        return $lotteryResult;
    }

    private function getCoupon(&$userInfo, $prize_id, $appConfig)
    {
        // 抽奖处理
        $exclude_prize_ids = array();
        $info = array(
            'source_data' => array(
                'user_id' => myMongoId($userInfo['_id']),
                'alipay_user_id' => $userInfo['userid'],
                'nickname' => $userInfo['nickname'],
                'headimgurl' => $userInfo['headimgurl']
            )
        );
        $identityContact = array(
            'name' => '',
            'mobile' => '',
            'address' => ''
        );
        
        // 抽奖
        $lotteryInfo = $this->doLottery($userInfo, $this->lottery_activity_id1, $userInfo['userid'], $prize_id, $exclude_prize_ids, $info, $identityContact);
        
        // 抽奖成功的话
        if (! empty($lotteryInfo['success']) && ! empty($lotteryInfo['result'])) {
            
            $exchangeInfo = $lotteryInfo['result'];
            $prize_info = $exchangeInfo['prize_info'];
            $code_info = empty($exchangeInfo['prize_code']) ? array(
                'code' => '',
                'pwd' => ''
            ) : array(
                'code' => $exchangeInfo['prize_code']['code'],
                'pwd' => $exchangeInfo['prize_code']['pwd']
            );
            $is_virtual = empty($prize_info['is_virtual']) ? false : true;
            $prize_category = empty($prize_info['category']) ? 0 : $prize_info['category'];
            $virtual_currency = empty($prize_info['virtual_currency']) ? 0 : $prize_info['virtual_currency'];
            
            // 调用支付宝相应接口
            $camp_id = $prize_info['memo']['camp_id'];
            $objiAlipay = new \iAlipay($appConfig['app_id'], $appConfig['merchant_private_key'], $appConfig['merchant_public_key'], $appConfig['alipay_public_key'], $appConfig['charset'], $appConfig['gatewayUrl'], $appConfig['sign_type']);
            $ret2 = $objiAlipay->alipayMarketingCampaignDrawcampTriggerRequest($userInfo['userid'], $camp_id);
            
            // 修改活动用户信息
            $otherIncData = array(
                'memo.prize_num' => 1
            );
            // 更新该用户的已处理标志
            $otherUpdateData = array(
                "memo.prize_list.{$exchangeInfo['exchange_id']}" => array(
                    'exchange_id' => $exchangeInfo['exchange_id'],
                    'identity_id' => $exchangeInfo['identity_id'],
                    'exchange_time' => $exchangeInfo['__CREATE_TIME__'],
                    'identity_contact' => $identityContact,
                    'prize_info' => $prize_info,
                    'code_info' => $code_info,
                    'prize_id' => $exchangeInfo['prize_id']
                ),
                "memo.is_got_coupon_{$prize_id}" => true,
                "memo.alipayMarketingCampaignDrawcampTrigger" => $ret2
            );
            
            $options = array();
            $options['query'] = array(
                '_id' => $userInfo['_id']
            );
            $update = array(
                '$inc' => $otherIncData,
                '$set' => $otherUpdateData
            );
            $options['update'] = $update;
            $options['new'] = true; // 返回更新之后的值
            $rstTemp2 = $this->modelActivityUser->findAndModify($options);
            if (empty($rstTemp2['ok'])) {
                throw new \Exception("用户信息更新失败" . json_encode($rstTemp2));
            }
            
            if (empty($rstTemp2['value'])) {
                throw new \Exception("用户信息更新失败" . json_encode($rstTemp2));
            }
            $userInfo = $rstTemp2['value'];
            
            $ret = array();
            $ret['exchange_id'] = $exchangeInfo['exchange_id'];
            $ret['identity_id'] = $exchangeInfo['identity_id'];
            $ret['prize_info']['prize_id'] = $exchangeInfo['prize_id'];
            $ret['prize_info']['prize_code'] = $prize_info['prize_code'];
            $ret['prize_info']['prize_name'] = $prize_info['prize_name'];
            $ret['prize_info']['is_virtual'] = $is_virtual;
            $ret['code_info'] = $code_info;
            
            return $ret;
        } else {
            $e = new \Exception($lotteryInfo['error_msg'], $lotteryInfo['error_code']);
            $this->modelActivityErrorLog->log($this->activity1, $e);
            return false;
        }
    }
}

