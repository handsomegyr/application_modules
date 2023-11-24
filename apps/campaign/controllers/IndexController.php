<?php

namespace App\Campaign\Controllers;

/**
 * 例子
 *
 * 授权地址
 * http://www.myapplicationmodule.com/campaign/index/weixinauthorizebefore?callbackUrl=http%3A%2F%2Fwww.baidu.com%2F
 *
 * http://www.myapplicationmodule.com/campaign/index/weixinauthorizebefore?operation4cookie=clear
 *
 * http://www.myapplicationmodule.com/campaign/index/weixinauthorizebefore?operation4cookie=store&FromUserName=xxxx&nickname=xx&headimgurl=xx
 *
 * http://www.myapplicationmodule.com/html/index/index.html
 *
 * http://www.myapplicationmodule.com/campaign/index/weixinauthorizebefore?operation4cookie=store&FromUserName=xxxx&nickname=xx&headimgurl=xx
 *
 * @author 郭永荣
 *        
 */
class IndexController extends ControllerBase
{
    // 抽奖中奖
    protected $modelLotteryExchange = null;
    // 抽奖服务
    protected $serviceLottery = null;

    // 活动ID
    protected $activity_id = '5861e812887c22015f8b456b';

    protected function doCampaignInitialize()
    {
        $this->modelLotteryExchange = new \App\Lottery\Models\Exchange();
        $this->serviceLottery = new \App\Lottery\Services\Api();
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
        // http://www.myapplicationmodule.com/campaign/index/getcampaignuserinfo
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
            $key = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-40499, "上次操作还未完成,请等待");
                return false;
            }

            // 记录该用户
            $memo = array(
                'is_got_prize' => false,
                'is_record_lottery_user_contact_info' => false
            );
            $scene = "";
            $userInfo = $this->getOrCreateActivityUser($FromUserName, $nickname, $headimgurl, 'redpack_user', 'thirdparty_user', $scene, array(), $memo);

            // 是否是黑名单用户
            $blankUserInfo = $this->modelActivityBlackUser->getInfoByUser($FromUserName, $this->activity_id);

            // 根据具体的业务返回相应的信息
            // 获取从奖池数量
            $prizeRemainNum = 0; // $this->getPrizeRemainNum();

            // 返回值
            $ret = array(
                // 活动信息
                'activityInfo' => $activityInfo,
                // 用户信息
                'userInfo' => array(
                    // // 是否关注
                    // 'is_subscribe' => empty($userInfo['memo']['is_subscribe']) ? 0 : 1,
                    // 是否是黑名单用户
                    'is_blankuser' => empty($blankUserInfo) ? 0 : 1,
                    // 奖池
                    'prizeRemainNum' => $prizeRemainNum,
                    // 是否已经抽取过
                    'is_got_prize' => empty($userInfo['memo']['is_got_prize']) ? 0 : 1,
                    // 是否已经填写了中奖联系信息
                    'is_record_lottery_user_contact_info' => empty($userInfo['memo']['is_record_lottery_user_contact_info']) ? 0 : 1,
                    // 红包
                    'worth' => $userInfo['worth']
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
     * 抽奖接口
     */
    public function lotteryAction()
    {
        // http://www.myapplicationmodule.com/campaign/index/lottery?name=guoyongrong&mobile=13564100096&address=xxx
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
            $name = trim($this->get('name', ''));
            $mobile = trim($this->get('mobile', ''));
            $address = trim($this->get('address', ''));

            // 根据不同的奖品类别进行处理
            $nameCheck = false;
            $mobileCheck = false;
            $addressCheck = false;
            if ($nameCheck) {
                if (empty($name)) {
                    echo $this->error(-40411, "姓名不能为空");
                    return false;
                }
            }
            if ($mobileCheck) {
                if (empty($mobile)) {
                    echo $this->error(-40412, "手机号不能为空");
                    return false;
                }
                if (!isValidMobile($mobile)) {
                    echo $this->error(-40413, "手机号格式不正确");
                    return false;
                }
            }
            if ($addressCheck) {
                if (empty($address)) {
                    echo $this->error(-40414, "地址不能为空");
                    return false;
                }
            }

            // 检查是否锁定，如果没有锁定加锁
            $key = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-40499, "上次操作还未完成,请等待");
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
                echo $this->error(-40422, '该用户已经是黑名单用户');
                return false;
            }

            // 检查是否已经领取了普通红包
            if (!empty($userInfo['memo']['is_got_prize'])) {
                echo $this->error(-40431, '该用户已领取');
                return false;
            }

            // 先将抽奖机会次数减一
            $this->modelActivityUser->incWorth($userInfo, -1);

            // 抽奖处理
            // 记录抽奖用户的昵称和头像
            $user_info = array(
                'user_name' => $userInfo['nickname'],
                'user_headimgurl' => $userInfo['headimgurl']
            );
            // 抽奖用户联系方式
            $identityContact = array(
                'name' => '',
                'mobile' => '',
                'address' => ''
            );
            // 记录活动用户ID
            $memo = array(
                'activity_user_id' => $userInfo['_id']
            );
            $lotteryResult = $this->serviceLottery->doLottery($this->activity_id, $FromUserName, $this->now, array(), array(), 'weixin', $user_info, $identityContact, $memo);

            // 抽奖成功的话
            if (empty($lotteryResult['error_code']) && !empty($lotteryResult['result'])) {
                $successInfo = $lotteryResult['result'];
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
                echo ($this->result("OK", $ret));
                fastcgi_finish_request();

                // 记录中奖信息
                $query = array(
                    '_id' => $userInfo['_id']
                );
                $data = array();
                $data['memo'] = array_merge($userInfo['memo'], array(
                    'is_got_prize' => true,
                    'prizeInfo' => $successInfo
                ));
                $this->modelActivityUser->update($query, array(
                    '$set' => $data
                ));
                // 发送模版消息
                // $this->sendTemplateMsg($userInfo['user_id'], $successInfo['prize_name'], $successInfo['prize_virtual_code']);
                return true;
            } else {
                // 失败的话
                $e = new \Exception($lotteryResult['error_msg'], $lotteryResult['error_code']);
                $this->modelErrorLog->log($this->activity_id, $e);
                echo ($this->error(-40432, '活动太火爆，奖品还在路上'));
                return false;
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 记录中奖用户信息的接口
     */
    public function recorduserinfoAction()
    {
        // http://www.myapplicationmodule.com/campaign/index/recorduserinfo?exchange_id=5865f1edfcc2b60a008b456c&identity_id=xxxx&name=guoyongrong&mobile=13564100096&address=shanghai
        try {
            $this->view->disable();

            $name = trim($this->get('name', ''));
            $mobile = trim($this->get('mobile', ''));
            $address = trim($this->get('address', ''));
            $exchange_id = trim($this->get('exchange_id', ''));
            $identity_id = trim($this->get('identity_id', ''));

            if (empty($exchange_id)) {
                echo $this->error(-40451, "中奖ID不能为空");
                return false;
            }

            if (empty($identity_id)) {
                echo $this->error(-40452, "身份ID不能为空");
                return false;
            }

            // 根据不同的奖品类别进行处理
            $nameCheck = true;
            $mobileCheck = true;
            $addressCheck = false;
            if ($nameCheck) {
                if (empty($name)) {
                    echo $this->error(-40453, "姓名不能为空");
                    return false;
                }
            }
            if ($mobileCheck) {
                if (empty($mobile)) {
                    echo $this->error(-40454, "手机号不能为空");
                    return false;
                }
                if (!isValidMobile($mobile)) {
                    echo $this->error(-40455, "手机号格式不正确");
                    return false;
                }
            }
            if ($addressCheck) {
                if (empty($address)) {
                    echo $this->error(-40456, "地址不能为空");
                    return false;
                }
            }
            $info = array(
                'is_valid' => true
            );

            if (!empty($name))
                $info['contact_name'] = $name;

            if (!empty($mobile))
                $info['contact_mobile'] = $mobile;

            if (!empty($address))
                $info['contact_address'] = $address;

            if (empty($info)) {
                echo $this->error(-40457, "用户信息不能为空");
                return false;
            }

            // 判断是否中奖
            $exchangeInfo = $this->modelLotteryExchange->checkExchangeBy($identity_id, $exchange_id);
            if (empty($exchangeInfo)) {
                echo $this->error(-40458, "该用户无此兑换信息");
                return false;
            }
            // 获取活动用户信息
            $userInfo = $this->modelActivityUser->getInfoById($exchangeInfo['memo']['activity_user_id']);
            if (empty($userInfo)) {
                echo $this->error(-40458, "该用户无此兑换信息");
                return false;
            }

            // 记录中奖用户的信息
            $this->modelLotteryExchange->updateExchangeInfo($exchange_id, $info);

            // 更新活动用户的是否填写抽奖联系信息
            $query = array(
                '_id' => $userInfo['_id']
            );
            $data = array();
            $data['memo'] = array_merge($userInfo['memo'], array(
                'is_record_lottery_user_contact_info' => true
            ));
            $this->modelActivityUser->update($query, array(
                '$set' => $data
            ));
            echo ($this->result('处理完成'));
            return true;
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 抽奖接口2
     * 压测用的
     *
     * 检查是否有重复的code
     * SELECT CODE,COUNT(*) AS num FROM iprize_code
     * GROUP BY CODE
     * HAVING num>1
     *
     * 检查是否有重复的code的中奖记录
     * SELECT prize_virtual_code,COUNT(*) AS num FROM ilottery_exchange
     * WHERE activity_id='5861e812887c22015f8b456b'
     * GROUP BY prize_virtual_code
     * HAVING num>1
     *
     * 查看详情
     * SELECT * FROM ilottery_exchange
     * WHERE activity_id='5861e812887c22015f8b456b'
     * AND prize_virtual_code = '10000001'
     *
     *
     *
     * select count(*) from ilottery_exchange;
     * SELECT COUNT(*) FROM iprize_code where is_used=1;
     * SELECT 2002-allow_number FROM ilottery_rule;
     * select count(*) from ilottery_record;
     *
     * 清理数据
     * UPDATE iprize_code SET activity_id='',is_used=0;
     * UPDATE ilottery_rule SET allow_number=2002;
     * DELETE FROM ilottery_record;
     * DELETE FROM ilottery_exchange;
     * DELETE FROM iactivity_errorlog;
     */
    public function lottery4testAction()
    {
        // http://www.myapplicationmodule.com/campaign/index/lottery4test
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
            $uniq = uniqid();
            $userInfo = array();
            $userInfo['_id'] = $uniq;
            $userInfo['FromUserName'] = $uniq;
            $userInfo['nickname'] = $uniq;
            $userInfo['headimgurl'] = $uniq;

            $FromUserName = $userInfo['FromUserName'];
            $name = trim($this->get('name', ''));
            $mobile = trim($this->get('mobile', ''));
            $address = trim($this->get('address', ''));

            // 根据不同的奖品类别进行处理
            $nameCheck = false;
            $mobileCheck = false;
            $addressCheck = false;
            if ($nameCheck) {
                if (empty($name)) {
                    echo $this->error(-40411, "姓名不能为空");
                    return false;
                }
            }
            if ($mobileCheck) {
                if (empty($mobile)) {
                    echo $this->error(-40412, "手机号不能为空");
                    return false;
                }
                if (!isValidMobile($mobile)) {
                    echo $this->error(-40413, "手机号格式不正确");
                    return false;
                }
            }
            if ($addressCheck) {
                if (empty($address)) {
                    echo $this->error(-40414, "地址不能为空");
                    return false;
                }
            }

            // 检查是否锁定，如果没有锁定加锁
            $key = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(-40499, "上次操作还未完成,请等待");
                return false;
            }

            // 抽奖处理
            // 记录抽奖用户的昵称和头像
            $user_info = array(
                'user_name' => $userInfo['nickname'],
                'user_headimgurl' => $userInfo['headimgurl']
            );
            // 抽奖用户联系方式
            $identityContact = array(
                'name' => '',
                'mobile' => '',
                'address' => ''
            );
            // 记录活动用户ID
            $memo = array(
                'activity_user_id' => $userInfo['_id']
            );
            // 测试并发
            $this->serviceLottery->_isTest4Concurreny = true;
            $lotteryResult = $this->serviceLottery->doLottery($this->activity_id, $FromUserName, $this->now, array(), array(), 'weixin', $user_info, $identityContact, $memo);

            // 抽奖成功的话
            if (empty($lotteryResult['error_code']) && !empty($lotteryResult['result'])) {
                $successInfo = $lotteryResult['result'];
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
                echo ($this->result("OK", $ret));
                // 发送模版消息
                return true;
            } else {
                // 失败的话
                $e = new \Exception($lotteryResult['error_msg'], $lotteryResult['error_code']);
                // $this->modelErrorLog->log($this->activity_id, $e);
                echo ($this->error($lotteryResult['error_code'], $lotteryResult['error_msg']));
                return false;
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    public function bfAction()
    {
        // http://www.myapplicationmodule.com/campaign/index/bf?name=xxx
        try {
            $name = $this->get('name');
            $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $name);
            echo $cacheKey;
            $oLock = new \iLock($cacheKey);
            $l = $oLock->lock();
            var_dump($l);
            if ($l) {
                die('并发');
            }
            sleep(10);
            die('f');
        } catch (Exception $e) {
            var_dump($e);
            exit();
        }
    }


    public function downloadAction()
    {
        // http://www.myapplicationmodule.com/campaign/index/download?file=5e4b536969dc0a08a154c969.jpg&rename=
        try {
            //https://www.jianshu.com/p/e6b6c7be15de
            //https://kovyrin.net/2006/11/01/nginx-x-accel-redirect-php-rails/
            $this->view->disable();
            if (!isset($_GET['file'])) {
                die('文件不存在');
            }
            //比如下载的文件为： /public/upload/5e4b536969dc0a08a154c969.jpg
            $file = $_GET['file'];
            $rename = isset($_GET['rename']) ? $_GET['rename'] : $file;
            // 模拟校验下载权限
            header('Content-Type:application/octet-stream;');
            header('Content-Disposition: attachment; filename=' . $rename);
            header('X-Accel-Redirect: /xsendfile_download/' . ltrim($file, '/'));
        } catch (Exception $e) {
            var_dump($e);
            exit();
        }
    }

    private function sendTemplateMsg($openid, $prize_name, $code)
    {
        try {
            // 模版消息
            $template_id = "q3VV4Pk_amMCmZbAoegVFCxxQrcEUrhgrJKjFQoU1G0";
            $url = "{$this->webPath}tuanyuan/index/coupon";
            $topcolor = "#FF0000";
            $data = array();
            $data['first'] = array(
                "value" => "你的团圆RP爆棚，获得必胜客免费富贵团圆海陆比萨兑换券一份。",
                "color" => "#0A0A0A"
            );
            // 券名称
            $data['keyword1'] = array(
                "value" => "富贵团圆海陆比萨兑换券",
                "color" => "#0A0A0A"
            );
            // 兑换码
            $data['keyword2'] = array(
                "value" => $code,
                "color" => "#0A0A0A"
            );
            // 失效期
            $data['keyword3'] = array(
                "value" => "2016年1月24日",
                "color" => "#0A0A0A"
            );
            $data['remark'] = array(
                "value" => "点击详情，领取兑换券。",
                "color" => "#0A0A0A"
            );

            // asyncSendTpl($openid, $template_id, $url, $data);
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
        }
    }
}
