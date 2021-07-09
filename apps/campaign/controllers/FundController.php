<?php

use App\Alipay\Models\Application;

/**
 * 基金财富号
 * http://www.myapplicationmodule.com/campaign/fund/alipayauthorizebefore?operation4cookie=store&user_id=xxx&nickname=xxx&headimgurl=xxx
 *
 * http://www.myapplicationmodule.com/campaign/fund/alipayauthorizebefore?callbackUrl=https%3A%2F%2Fwww.baidu.com%2F
 *
 * https://cloud.huaan.com.cn/one/index.html
 *
 * http://www.myapplicationmodule.com/campaign/fund/alipayauthorizebefore?operation4cookie=clear
 *
 * @author 郭永荣
 *        
 */
class FundController extends ControllerBase
{
    // 用于签名认证
    protected $secretKey = "170908fg0353";

    // 活动相关
    // 活动1
    protected $activity1 = '59ed93599fff63190a8b4568';

    protected function doCampaignInitialize()
    {
        $this->view->disable();
    }

    /**
     * 获取用户信息的接口
     */
    public function getcampaignuserinfoAction()
    {
        // http://www.myapplicationmodule.com/campaign/sign/getcampaignuserinfo
        try {
            $userInfo = empty($_COOKIE['Alipay_userInfo']) ? array() : json_decode($_COOKIE['Alipay_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(-999, "用户信息为空");
                return false;
            }

            $user_id = trim($userInfo['user_id']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);

            // 检查cookie的有效性
            $isValid = $this->validateOpenid($user_id, $timestamp, $this->secretKey, $signkey);
            if (!$isValid) {
                echo $this->error(-999, "用户信息是伪造的");
                return false;
            }

            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $user_id);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-888, "上次操作还未完成,请等待");
                return false;
            }

            // 获取活动用户
            $memo = array(
                'is_correct' => false,
                'is_answer' => false
            );
            $scene = "";
            $userInfo = $this->modelActivityUser->getOrCreateByUserId($this->activity1, $user_id, $this->now, $nickname, $headimgurl, '', '', 0, 0,  $scene, array(), $memo);
            if (empty($userInfo)) {
                echo $this->error(-40491, 'user_id不正确');
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
        // http://www.myapplicationmodule.com/campaign/fund/answer?answer=0
        try {
            $answer = intval($this->get('answer', '0'));
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo($this->activity1, $this->now->sec);

            // 活动是否开始了
            if (empty($activityInfo['is_activity_started'])) {
                echo $this->error(-40401, "活动未开始");
                return false;
            }
            // 活动是否暂停
            if (!empty($activityInfo['is_actvity_paused'])) {
                echo $this->error(-40402, "活动已暂停");
                return false;
            }
            // 活动是否结束了
            if (!empty($activityInfo['is_activity_over'])) {
                echo $this->error(-40403, "活动已结束");
                return false;
            }

            $userInfo = empty($_COOKIE['Alipay_userInfo']) ? array() : json_decode($_COOKIE['Alipay_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(-40497, "用户信息为空");
                return false;
            }

            $user_id = trim($userInfo['user_id']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);

            // 检查cookie的有效性
            $isValid = $this->validateOpenid($user_id, $timestamp, $this->secretKey, $signkey);
            if (!$isValid) {
                echo $this->error(-40498, "用户信息是伪造的");
                return false;
            }

            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $user_id);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-40499, "上次操作还未完成,请等待");
                return false;
            }

            $userInfo = $this->modelActivityUser->getInfoByUserId($user_id, $this->activity1);
            if (empty($userInfo)) {
                echo $this->error(-40491, 'user_id不正确');
                return false;
            }
            // 检查是否已经回答过问题
            if (!empty($userInfo['memo']['is_answer'])) {
                echo $this->error(-40492, '该用户已做过');
                return false;
            }

            $config = $this->getDI()->get('config');
            $modelApliayApplication = new Application();
            $appConfig = $modelApliayApplication->getApplicationInfoByAppId($config['alipay']['appid']);
            if (empty($appConfig)) {
                throw new \Exception('appid所对应的记录不存在');
            }

            // 是否答对题目
            $is_correct = ($answer === 3);
            $data = $userInfo['memo'];
            $data['memo']['is_answer'] = true;
            $data['memo']['is_correct'] = $is_correct;
            $this->modelActivityUser->update(array(
                '_id' => $userInfo['_id']
            ), array(
                '$set' => $data
            ));

            // 检查是否答对题目
            if (!$is_correct) {
                echo $this->error(-10, "回答不正确");
                return false;
            }

            // 调用支付宝相应接口
            $camp_id = 'xxxx';
            $objiAlipay = new \iAlipay($appConfig['app_id'], $appConfig['merchant_private_key'], $appConfig['merchant_public_key'], $appConfig['alipay_public_key'], $appConfig['charset'], $appConfig['gatewayUrl'], $appConfig['sign_type']);
            $ret = $objiAlipay->alipayMarketingCampaignDrawcampTriggerRequest($userInfo['userid'], $camp_id);

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
        // http://www.myapplicationmodule.com/campaign/fund/fundperformancechart2?fundcodes=040008
        try {
            $fundcodes = $this->get('fundcodes', '');

            if (empty($fundcodes)) {
                echo $this->error(-3, 'fundcodes未指定');
                return false;
            }

            $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__, $fundcodes);
            $cache = Zend_Registry::get('cache');
            $ret = $cache->load($cacheKey);
            if (empty($ret)) {
                $config = $this->getDI()->get('config');
                $modelApliayApplication = new Application();
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
        // http://www.myapplicationmodule.com/campaign/fund/test1&camp_id=170921821480493
        try {
            $user_id = $this->get('user_id', '2088602345138428');
            $camp_id = $this->get('camp_id', '170921821480493');

            $this->_app = new Application();
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
        // http://www.myapplicationmodule.com/campaign/fund/test2?camp_id=170921821480493&prize_id=xx
        try {
            $camp_id = $this->get('camp_id', '170921821480493');
            $prize_id = $this->get('prize_id', '20170921000730018232000BP4C6');

            $this->_app = new Application();
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
        // http://www.myapplicationmodule.com/campaign/fund/test3?camp_id=170921821480493
        try {
            $camp_id = $this->get('camp_id', '170921821480493');

            $this->_app = new Application();
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
     * user_id校验
     *
     * @param string $user_id            
     * @param string $timestamp            
     * @param string $secretKey            
     * @param string $signature            
     * @return boolean
     */
    private function validateOpenid($user_id, $timestamp, $secretKey, $signature)
    {
        $secret = sha1($user_id . "|" . $secretKey . "|" . $timestamp);
        if ($signature != $secret) {
            return false;
        } else {
            return true;
        }
    }
}
