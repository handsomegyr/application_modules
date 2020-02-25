<?php

namespace App\Campaign\Controllers;

/**
 * 游戏事例
 * http://www.applicationmodule.com/campaign/game/weixinauthorizebefore?operation4cookie=store&FromUserName=oFTgHwbw1xwUz8MgIBLW74kXcqnY&nickname=xxx&headimgurl=xxx
 *
 * http://www.applicationmodule.com/campaign/game/weixinauthorizebefore?callbackUrl=http%3A%2F%2Fwww.applicationmodule.com%2Fhtml%2FfamilyTrust%2Findex.html
 *
 * http://www.applicationmodule.com/game/index.html
 *
 * http://www.applicationmodule.com/campaign/game/weixinauthorizebefore?operation4cookie=clear
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

    // 游戏
    protected $modelGameGame;
    protected $modelGameUser;
    protected $modelGameLog;

    protected function doCampaignInitialize()
    {
        $this->modelGameGame = new \App\Game\Models\Game();
        $this->modelGameUser = new \App\Game\Models\User();
        $this->modelGameLog = new \App\Game\Models\Log();
        $this->view->disable();
    }

    /**
     * 获取用户信息的接口
     */
    public function getcampaignuserinfoAction()
    {
        // http://www.applicationmodule.com/campaign/game/getcampaignuserinfo
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
            $userInfo = $this->modelActivityUser->getOrCreateByUserId($this->activity1, $FromUserName, $this->now, $nickname, $headimgurl, '', '', 0, 0, $scene, $memo);

            // 是否是黑名单用户
            $blankUserInfo = $this->modelActivityBlackUser->getInfoByUser($FromUserName, $this->activity1);

            // 根据具体的业务返回相应的信息
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo($this->activity1, $this->now->sec, true);
            unset($activityInfo['activityInfo']);

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
                // 是否看过新手引导
                'is_view_novice_guide' => empty($userInfo["memo"]["is_view_novice_guide"]) ? 0 : 1
            );
            echo $this->result("OK", $ret);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 记录看过了新手引导
     */
    public function viewnoviceguideAction()
    {
        // http://www.applicationmodule.com/campaign/game/viewnoviceguide
        try {
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo2($this->activity1, $this->now->sec);

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

            // 更新活动用户信息
            $data = array();
            $data['memo'] = array_merge($userInfo['memo'], array(
                'is_view_novice_guide' => 1
            ));
            $this->modelActivityUser->update(array('_id' => $userInfo['id']), array('$set' => $data));
            return $this->result("OK", array());
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 记录游戏结果
     */
    public function recordgameresultAction()
    {
        // http://www.applicationmodule.com/campaign/game/recordgameresult?score=xx
        try {
            $score = intval($this->get("score", "0"));
            if (empty($score) || $score < 0) {
                return ($this->error(-1, '游戏分数为空'));
            }

            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo2($this->activity1, $this->now->sec);

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

            // 获取游戏信息
            $gameInfo = $this->modelGame->getGameInfo($this->game_id, $this->now, true);

            // 游戏是否开始了
            if (empty($gameInfo['is_game_started'])) {
                echo $this->error(-2, "游戏未开始");
                return false;
            }
            // 游戏是否结束了
            if (!empty($gameInfo['is_game_over'])) {
                echo $this->error(-3, "游戏已结束");
                return false;
            }

            // 检查游戏分数
            if (!empty($gameInfo['max_score_limit'])) {
                if (intval($gameInfo['max_score_limit']) < $score) {
                    echo ($this->error(-4, '游戏分数异常'));
                    return false;
                }
            }

            try {
                $this->modelGame->begin();

                // 生成游戏用户
                $memo = array(
                    'memo' => ''
                );

                $user_num = 0;
                $gameUserInfo = $this->modelGameUser->getInfoByUserId($FromUserName, $this->game_id, $this->activity_id);
                if (empty($gameUserInfo)) {
                    $user_num = 1;
                    // 生成
                    $gameUserInfo = $this->modelGameUser->create($this->activity_id, $this->game_id, $FromUserName, $nickname, $headimgurl, $score, $this->now, $memo);
                    if (empty($gameUserInfo)) {
                        throw new \Exception("游戏用户信息生成失败");
                    }
                }

                // 记录比赛开始数据
                $memo = array(
                    'memo' => ''
                );
                $gameLogInfo = $this->modelGameLog->log($this->activity_id, $this->game_id, $FromUserName, $nickname, $headimgurl, $score, 0, "", "", \Input::getClientIp(), $this->now, $memo);
                if (empty($gameLogInfo)) {
                    throw new \Exception("游戏日志信息记录失败");
                }

                // 记录比赛分数 更新排行榜
                $this->modelGameUser->updateScore($gameUserInfo, $score, $this->now);

                // 更新该游戏的统计信息
                $this->modelGame->updateStatistics($this->game_id, $score, $user_num, $this->now);

                $this->modelGame->commit();
            } catch (\Exception $e) {
                $this->modelGame->rollback();
                throw $e;
            }

            // 获取排名信息
            $gameUserInfo = $this->modelGameUser->getInfoById($gameUserInfo['id']);
            $myRankInfo = $this->modelGameUser->getMyRank($gameUserInfo, $gameInfo);

            $ret = array();
            $ret['myRankInfo'] = $myRankInfo;
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 获取玩家排名列表
     */
    public function getranklistAction()
    {
        // http://www.applicationmodule.com/campaign/game/getranklist?page=1&limit=15
        try {
            $page = intval($this->get("page", '1'));
            $limit = intval($this->get("limit", '15'));
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo2($this->activity1, $this->now->sec);

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

            // 获取游戏信息
            $gameInfo = $this->modelGame->getGameInfo($this->game_id, $this->now, true);

            // 游戏是否开始了
            if (empty($gameInfo['is_game_started'])) {
                echo $this->error(-2, "游戏未开始");
                return false;
            }
            // 游戏是否结束了
            if (!empty($gameInfo['is_game_over'])) {
                echo $this->error(-3, "游戏已结束");
                return false;
            }

            // 获取排名列表
            $rankList = $this->getRankList2($gameInfo, $page, $limit);

            // 获取排名信息
            $gameUserInfo = $this->modelGameUser->getInfoByUserId($FromUserName, $this->game_id, $this->activity_id);
            if (empty($gameUserInfo)) {
                $gameUserInfo = array();
                $gameUserInfo["max_score"] = 0;
                $gameUserInfo["max_score_time"] = $this->now;
                $gameUserInfo["user_id"] = $FromUserName;
                $gameUserInfo['game_id'] = $this->game_id;
                $gameUserInfo['activity_id'] = $this->activity_id;
            }
            $myRankInfo = $this->modelGameUser->getMyRank($gameUserInfo, $gameInfo);

            $ret = array();
            $ret['rankList'] = $rankList;
            $ret['myRankInfo'] = $myRankInfo;
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    protected function getRankList2($gameInfo, $page, $limit)
    {
        // 如果是正式环境的话 加缓存处理
        $is_production = false;
        if ($is_production) {
            $game_id = $gameInfo["_id"];
            $cacheKey = "game:ranklist:game_id:{$game_id}:page:{$page}:limit:{$limit}";
            $cache = $this->getDI()->get("cache");
            $rankList = $cache->get($cacheKey);
            if (empty($rankList)) {
                $rankList = $this->modelGameUser->getRankList($gameInfo, $page, $limit);
                // 加缓存处理
                $cacheTime = 1 * 60; // 1分钟
                $cache->save($cacheKey, $rankList, $cacheTime);
            }
        } else {
            $rankList = $this->modelGameUser->getRankList($gameInfo, $page, $limit);
        }
        return $rankList;
    }
}
