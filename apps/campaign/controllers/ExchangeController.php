<?php

namespace App\Campaign\Controllers;

/**
 * 兑换例子
 *
 * 授权地址
 * http://www.myapplicationmodule.com/campaign/exchange/weixinauthorizebefore?callbackUrl=http%3A%2F%2Fwww.baidu.com%2F
 *
 * http://www.myapplicationmodule.com/campaign/exchange/weixinauthorizebefore?operation4cookie=clear
 *
 * http://www.myapplicationmodule.com/campaign/exchange/weixinauthorizebefore?operation4cookie=store&FromUserName=xxxx&nickname=xx&headimgurl=xx
 *
 * http://www.myapplicationmodule.com/html/exchange/index.html
 *
 * http://www.myapplicationmodule.com/campaign/exchange/weixinauthorizebefore?operation4cookie=store&FromUserName=ok0K2vystcQkKolNr3anJd-soVuI&nickname=郭永荣&headimgurl=xx
 *
 * @author 郭永荣
 *        
 */
class LotteryController extends ControllerBase
{
    // 抽奖中奖
    protected $modelExchangeExchange = null;
    // 抽奖服务
    protected $serviceExchane = null;

    // 活动ID
    protected $activity_id = '5861e812887c22015f8b456b';

    protected function doCampaignInitialize()
    {
        $this->modelExchangeExchange = new \App\Exchange\Models\Exchange();
        $this->serviceExchane = new \App\Exchange\Services\Api();
    }

    /**
     * 首页，任何人都能进入
     */
    public function indexAction()
    {
        try {
            die('index');
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 获取用户信息的接口
     */
    public function getcampaignuserinfoAction()
    {
        // http://www.myapplicationmodule.com/campaign/exchange/getcampaignuserinfo
        try {
            $this->view->disable();

            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo2($this->activity_id, $this->now);

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

            // 从cookie中直接获取
            $userInfo = empty($_COOKIE['Weixin_userInfo']) ? array() : json_decode($_COOKIE['Weixin_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(-40498, "用户信息为空");
                return false;
            }

            $FromUserName = trim($userInfo['FromUserName']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);

            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-40499, "上次操作还未完成,请等待");
                return false;
            }

            // 记录该用户
            $scene = "";
            $memo = array(
                // 场景
                'scene' => $scene
            );
            $userInfo = $this->getOrCreateActivityUser($FromUserName, $nickname, $headimgurl, 'redpack_user', 'thirdparty_user', $scene, array(), $memo);

            // 是否是黑名单用户
            $blankUserInfo = $this->modelActivityBlackUser->getInfoByUser($FromUserName, $this->activity_id);

            // 根据具体的业务返回相应的信息
            $exchangeRules = $this->modelExchangeRule->getRules($this->activity_id, $this->now);

            // 返回值
            $ret = array(
                // 活动信息
                'activityInfo' => $activityInfo,
                // 兑换奖品列表
                'exchangeRules' => $exchangeRules,
                // 用户信息
                'userInfo' => array(
                    // 是否关注
                    // 'is_subscribe' => empty($userInfo['memo']['is_subscribe']) ? 0 : 1,
                    // 是否是黑名单用户
                    'is_blankuser' => empty($blankUserInfo) ? 0 : 1,
                )
            );
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 兑换接口
     */
    public function exchangeAction()
    {
        // http://www.myapplicationmodule.com/campaign/exchange/exchange?&rule_id=xxx&name=guoyongrong&mobile=13564100096&address=xxx
        try {
            $this->view->disable();
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo2($this->activity_id, $this->now);

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

            // 从cookie中直接获取
            $userInfo = empty($_COOKIE['Weixin_userInfo']) ? array() : json_decode($_COOKIE['Weixin_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(-40498, "用户信息为空");
                return false;
            }

            $FromUserName = trim($userInfo['FromUserName']);
            $rule_id = trim($this->get('rule_id', ''));
            if (empty($rule_id)) {
                return $this->error(-40411, "rule_id为空");
            }
            $name = trim($this->get('name', ''));
            $mobile = trim($this->get('mobile', ''));
            $address = trim($this->get('address', ''));

            // 根据不同的奖品类别进行处理
            $nameCheck = false;
            $mobileCheck = false;
            $addressCheck = false;
            if ($nameCheck) {
                if (empty($name)) {
                    echo $this->error(-40411, "请填写姓名");
                    return false;
                }
            }
            if ($mobileCheck) {
                if (empty($mobile)) {
                    echo $this->error(-40412, "请填写手机号");
                    return false;
                }
                if (!isValidMobile($mobile)) {
                    echo $this->error(-40413, "手机号格式不正确");
                    return false;
                }
            }
            if ($addressCheck) {
                if (empty($address)) {
                    echo $this->error(-40414, "请填写地址");
                    return false;
                }
            }

            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-40499, "请等待");
                return false;
            }

            $userInfo = $this->modelActivityUser->getInfoByUserId($FromUserName, $this->activity_id);
            if (empty($userInfo)) {
                echo $this->error(-40421, 'FromUserName不正确');
                return false;
            }

            // 是否是黑名单用户
            $blankUserInfo = $this->modelActivityBlackUser->getInfoByUser($FromUserName, $this->activity_id);
            if (!empty($blankUserInfo)) {
                echo $this->error(-40422, '该用户已经禁用');
                return false;
            }

            // 检查是否已经领取了奖品
            if (!empty($userInfo['memo']['is_got_prize'])) {
                echo $this->error(-40431, '该用户已领取');
                return false;
            }

            // 先将兑换机会次数减一
            // $this->modelActivityUser->incWorth($userInfo, - 1);

            // 兑换处理
            // 记录兑换用户的昵称和头像
            $user_info = array(
                'user_name' => $userInfo['nickname'],
                'user_headimgurl' => $userInfo['headimgurl']
            );

            // 兑换用户联系方式
            $identityContact = array(
                'name' => $name,
                'mobile' => $mobile,
                'address' => $address
            );
            // 记录活动用户ID
            $memo = array(
                'activity_user_id' => $userInfo['_id']
            );

            // 兑换数量
            $quantity = 1;
            // 兑换消耗积分
            $score = 100;

            $exchangeResult = $this->serviceExchange->doExchange($this->activity_id, $FromUserName, $this->now, $rule_id, $quantity, $score, 'weixin', $user_info, $identityContact, array(), $memo);

            // 兑换成功的话
            if (empty($exchangeResult['error_code']) && !empty($exchangeResult['result'])) {
                $successInfo = $exchangeResult['result'];
                $ret = $this->getPrizeInfo($successInfo);
                echo ($this->result("OK", $ret));
                fastcgi_finish_request();
                return true;
            } else {
                // 失败的话
                $e = new \Exception($exchangeResult['error_msg'], $exchangeResult['error_code']);
                $this->modelErrorLog->log($this->activity_id, $e);
                echo ($this->error(-40432, "没有兑换成功:错误内容:{$exchangeResult['error_code']}:{$exchangeResult['error_msg']}"));
                return false;
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    private function getPrizeInfo($successInfo)
    {
        $ret = array();
        $ret['exchange_id'] = $successInfo['_id'];
        $ret['identity_id'] = $successInfo['user_id'];
        $ret['prize_info']['prize_id'] = $successInfo['prize_id'];
        $ret['prize_info']['prize_code'] = $successInfo['prize_code'];
        $ret['prize_info']['prize_name'] = $successInfo['prize_name'];
        $ret['prize_info']['virtual_currency'] = empty($successInfo['prize_virtual_currency']) ? 0 : $successInfo['prize_virtual_currency'];
        $ret['prize_info']['category'] = empty($successInfo['prize_category']) ? 0 : $successInfo['prize_category'];
        $ret['prize_info']['is_virtual'] = empty($successInfo['prize_is_virtual']) ? false : true;
        $ret['code_info']['code'] = $successInfo['prize_virtual_code'];
        $ret['code_info']['pwd'] = $successInfo['prize_virtual_pwd'];
        return $ret;
    }
}
