<?php

namespace App\Campaign\Controllers;

/**
 * 签到事例
 * http://www.myapplicationmodule.com/campaign/sign/weixinauthorizebefore?operation4cookie=store&FromUserName=oFTgHwbw1xwUz8MgIBLW74kXcqnY&nickname=xxx&headimgurl=xxx
 *
 * http://www.myapplicationmodule.com/campaign/sign/weixinauthorizebefore?callbackUrl=http%3A%2F%2Fwww.myapplicationmodule.com%2Fhtml%2FfamilyTrust%2Findex.html
 *
 * http://www.myapplicationmodule.com/sign/index.html
 *
 * http://www.myapplicationmodule.com/campaign/sign/weixinauthorizebefore?operation4cookie=clear
 *
 * @author Administrator
 *        
 */
class SignController extends ControllerBase
{

    // 活动相关
    // 活动1-游戏
    protected $activity1 = '59bde95f9fff63070a8b4567';

    // 用于签名认证
    protected $secretKey = "guoyongrong";

    // 签到
    protected $modelSignUser;

    protected $modelSignLog;

    protected function doCampaignInitialize()
    {
        $this->modelSignUser = new \App\Sign\Models\Sign();
        $this->modelSignLog = new \App\Sign\Models\Log();
        $this->view->disable();
    }

    /**
     * 获取用户信息的接口
     */
    public function getcampaignuserinfoAction()
    {
        // http://www.myapplicationmodule.com/campaign/sign/getcampaignuserinfo
        try {
            $userInfo = empty($_COOKIE['Weixin_userInfo']) ? array() : json_decode($_COOKIE['Weixin_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(-999, "用户信息为空");
                return false;
            }

            $FromUserName = trim($userInfo['FromUserName']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);

            // 检查cookie的有效性
            $isValid = true; // $this->validateOpenid($FromUserName, $timestamp, $this->secretKey, $signkey);
            if (!$isValid) {
                echo $this->error(-999, "用户信息是伪造的");
                return false;
            }

            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-888, "上次操作还未完成,请等待");
                return false;
            }

            // 记录微信用户
            $memo = array(
                // 是否抽奖
                'is_lottery' => 0
            );
            $scene = "";
            $userInfo = $this->modelActivityUser->getOrCreateByUserId($this->activity1, $FromUserName, $this->now, $nickname, $headimgurl, '', '', 0, 0, $scene, array(), $memo);

            // 是否是黑名单用户
            $blankUserInfo = $this->modelActivityBlackUser->getInfoByUser($FromUserName, $this->activity1);

            // 根据具体的业务返回相应的信息
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo($this->activity1, $this->now, true);
            $activityInfoDetail = $activityInfo['activityInfo'];
            unset($activityInfo['activityInfo']);

            // 判断今天是否已签到
            $isTodaySigned = false;
            // 连续签到天数
            $continue_sign_count = 0;
            // 根据userid获取上次签到信息
            $info = $this->modelSignUser->getLastInfoByUserId($FromUserName, $this->activity1);
            // 如果找到上次的签到数据
            if (!empty($info)) {
                // 检查签到的结果
                $judgeResult = $this->modelSignUser->judgeSignTime(strtotime($info['last_sign_time']), $this->now);
                // 今天是否已签到
                $isTodaySigned = ($judgeResult === -1);
                // 连续签到天数
                $continue_sign_count = ($judgeResult === 0) ? 0 : $info['continue_sign_count'];
            }

            // 计算活动的公布时间
            $ret = array(
                // 活动信息
                'activityInfo' => $activityInfo,
                // 用户信息
                'userInfo' => array(
                    // 是否是黑名单用户
                    'is_blankuser' => empty($blankUserInfo) ? 0 : 1,
                    // 是否已经抽取过
                    'is_lottery' => empty($userInfo['memo']['is_lottery']) ? 0 : 1
                ),
                // 用户信息
                'signInfo' => array(
                    // 今天是否签到过
                    'isTodaySigned' => empty($isTodaySigned) ? 0 : 1,
                    // 连续签到几天
                    'continue_sign_count' => $continue_sign_count
                )
            );
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 签到接口
     */
    public function doAction()
    {
        // http://www.myapplicationmodule.com/campaign/sign/do
        try {
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo2($this->activity1, $this->now);

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

            $userInfo = empty($_COOKIE['Weixin_userInfo']) ? array() : json_decode($_COOKIE['Weixin_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(-1, "用户信息为空");
                return false;
            }

            $FromUserName = trim($userInfo['FromUserName']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);

            // 检查cookie的有效性
            $isValid = true; // $this->validateOpenid($FromUserName, $timestamp, $this->secretKey, $signkey);
            if (!$isValid) {
                echo $this->error(-2, "用户信息是伪造的");
                return false;
            }

            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-40499, "上次操作还未完成,请等待");
                return false;
            }

            $userInfo = $this->modelActivityUser->getInfoByUserId($FromUserName, $this->activity1);
            if (empty($userInfo)) {
                echo $this->error(-2, 'FromUserName不正确');
                return false;
            }

            // 是否是黑名单用户
            $blankUserInfo = $this->modelActivityBlackUser->getInfoByUser($FromUserName, $this->activity1);
            if (!empty($blankUserInfo)) {
                echo $this->error(-40404, '该用户已经是黑名单用户');
                return false;
            }

            // 以下签到逻辑需要根据实际业务进行修改
            // 是否需要签到
            $is_need_sign = false;
            // 判断今天是否已签到
            $isTodaySigned = false;
            // 根据userid获取上次签到信息
            $info = $this->modelSignUser->getLastInfoByUserId($FromUserName, $this->activity1);
            // 如果找到上次的签到数据
            if (!empty($info)) {
                // 检查签到的结果
                $judgeResult = $this->modelSignUser->judgeSignTime(strtotime($info['last_sign_time']), $this->now);
                // 今天是否已签到
                $isTodaySigned = ($judgeResult === -1);
                // 如果今天没有签过的话
                if (!$isTodaySigned) {
                    // 需要签到
                    $is_need_sign = true;
                }
            } else {
                // 没有找到记录就需要重新签到
                $is_need_sign = true;
                // 连续签到
                $judgeResult = 1;
            }

            // 如果不需要签到的话
            if (!$is_need_sign) {
                return $this->error(-4, "你已经签到过");
            }

            // 如果需要签到的话
            if ($is_need_sign) {
                $scene = "";
                // 签到处理
                $memo = array(
                    'scene' => $scene
                );
                $ip = getIp();
                // 记录流水
                $signLog = $this->modelSignLog->log($this->activity1, $FromUserName, $nickname, $headimgurl, $this->now, $ip, $scene, $memo);
                // 处理签到信息
                $info = $this->modelSignUser->process($this->activity1, $FromUserName, $nickname, $headimgurl, $this->now, $ip, $signLog['_id'], $judgeResult, $info, $memo);
            }

            // 发送成功
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}
