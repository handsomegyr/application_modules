<?php
namespace App\Campaign\Controllers;

/**
 * 例子
 *
 * 授权地址
 * http://yoox.rice5.com.cn/campaign/cny/weixinauthorizebefore?callbackUrl=http%3A%2F%2Fwww.baidu.com%2F
 *
 * http://yoox.rice5.com.cn/campaign/cny/weixinauthorizebefore?operation4cookie=clear
 *
 * http://yoox.rice5.com.cn/campaign/cny/weixinauthorizebefore?operation4cookie=store&FromUserName=xxxx&nickname=xx&headimgurl=xx
 *
 * http://yoox.rice5.com.cn/html/cny/index.html
 *
 * http://yoox.rice5.com.cn/campaign/cny/weixinauthorizebefore?operation4cookie=store&FromUserName=ok0K2vystcQkKolNr3anJd-soVuI&nickname=郭永荣&headimgurl=xx
 *
 * @author 郭永荣
 *        
 */
class CnyController extends ControllerBase
{
    // 错误日志
    protected $modelErrorLog = null;
    // 活动相关
    protected $modelActivity = null;
    // 活动用户
    protected $modelActivityUser = null;
    // 活动黑名单用户
    protected $modelActivityBlackUser = null;
    
    // 抽奖中奖
    protected $modelLotteryExchange = null;
    // 抽奖服务
    protected $serviceLottery = null;
    
    // 活动ID
    protected $activity_id = '5861e812887c22015f8b456b';
    
    // 是否需要微信公众号关注
    private $is_need_subscribed = false;

    private $today = '';

    private $now = null;

    public function initialize()
    {
        $this->now = getCurrentTime();
        $this->today = date('Ymd', $this->now->sec);
        
        $this->modelErrorLog = new \App\Activity\Models\ErrorLog();
        $this->modelActivity = new \App\Activity\Models\Activity();
        $this->modelActivityUser = new \App\Activity\Models\User();
        $this->modelActivityBlackUser = new \App\Activity\Models\BlackUser();
        $this->modelLotteryExchange = new \App\Lottery\Models\Exchange();
        $this->serviceLottery = new \App\Lottery\Services\Api();
        
        try {
            parent::initialize();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
        }
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
        // http://yoox.rice5.com.cn/campaign/cny/getcampaignuserinfo
        try {
            $this->view->disable();
            
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo2($this->activity_id, $this->now->sec);
            
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
            
            // 从cookie中直接获取
            $userInfo = empty($_COOKIE['Weixin_userInfo']) ? array() : json_decode($_COOKIE['Weixin_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(- 40498, "用户信息为空");
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
                echo $this->error(- 40499, "上次操作还未完成,请等待");
                return false;
            }
            
            // 记录该用户
            $memo = array(
                'is_got_prize' => false,
                'is_record_lottery_user_contact_info' => false
            );
            $userInfo = $this->getOrCreateActivityUser($FromUserName, $nickname, $headimgurl, 'redpack_user', 'thirdparty_user', $memo);
            
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
                    // 是否关注
                    // 'is_subscribe' => empty($userInfo['memo']['is_subscribe']) ? 0 : 1,
                    // 是否是黑名单用户
                    'is_blankuser' => empty($blankUserInfo) ? 0 : 1,
                    // 奖池
                    'prizeRemainNum' => $prizeRemainNum,
                    // 是否已经中奖过
                    'is_got_prize' => empty($userInfo['memo']['is_got_prize']) ? 0 : 1,
                    // 是否已经填写了中奖联系信息
                    'is_record_lottery_user_contact_info' => empty($userInfo['memo']['is_record_lottery_user_contact_info']) ? 0 : 1,
                    // 抽奖机会，当worth为0是说明不能再抽奖了
                    'worth' => $userInfo['worth'],
                    // 中奖奖品信息
                    'prizeInfo' => empty($userInfo['memo']['prizeInfo']) ? '' : $this->getPrizeInfo($userInfo['memo']['prizeInfo'])
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
        // http://yoox.rice5.com.cn/campaign/cny/lottery?name=guoyongrong&mobile=13564100096&address=xxx
        try {
            $this->view->disable();
            // 获取活动信息
            $activityInfo = $this->modelActivity->getActivityInfo2($this->activity_id, $this->now->sec);
            
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
            
            // 从cookie中直接获取
            $userInfo = empty($_COOKIE['Weixin_userInfo']) ? array() : json_decode($_COOKIE['Weixin_userInfo'], true);
            if (empty($userInfo)) {
                echo $this->error(- 40498, "用户信息为空");
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
                    echo $this->error(- 40411, "请填写姓名");
                    return false;
                }
            }
            if ($mobileCheck) {
                if (empty($mobile)) {
                    echo $this->error(- 40412, "请填写手机号");
                    return false;
                }
                if (! isValidMobile($mobile)) {
                    echo $this->error(- 40413, "手机号格式不正确");
                    return false;
                }
            }
            if ($addressCheck) {
                if (empty($address)) {
                    echo $this->error(- 40414, "请填写地址");
                    return false;
                }
            }
            
            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(- 40499, "请等待");
                return false;
            }
            
            $userInfo = $this->modelActivityUser->getInfoByUserId($FromUserName, $this->activity_id);
            if (empty($userInfo)) {
                echo $this->error(- 40421, 'FromUserName不正确');
                return false;
            }
            
            // 是否是黑名单用户
            $blankUserInfo = $this->modelActivityBlackUser->getInfoByUser($FromUserName, $this->activity_id);
            if (! empty($blankUserInfo)) {
                echo $this->error(- 40422, '该用户已经禁用');
                return false;
            }
            
            // 检查是否已经领取了奖品
            if (! empty($userInfo['memo']['is_got_prize'])) {
                echo $this->error(- 40431, '该用户已领取');
                return false;
            }
            
            // 先将抽奖机会次数减一
            // $this->modelActivityUser->incWorth($userInfo, - 1);
            
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
            $lotteryResult = $this->serviceLottery->doLottery($this->activity_id, $FromUserName, array(), array(), 'weixin', $user_info, $identityContact, $memo);
            
            // 抽奖成功的话
            if (empty($lotteryResult['error_code']) && ! empty($lotteryResult['result'])) {
                $successInfo = $lotteryResult['result'];
                $ret = $this->getPrizeInfo($successInfo);
                echo ($this->result("OK", $ret));
                fastcgi_finish_request();
                
                // // 记录中奖信息
                // $query = array(
                // '_id' => $userInfo['_id']
                // );
                // $data = array();
                // $data['memo'] = array_merge($userInfo['memo'], array(
                // // 'is_got_prize' => true,
                // // 'is_record_lottery_user_contact_info' => empty($successInfo['prize_is_virtual']) ? false : true,
                // 'prizeInfo' => $successInfo
                // ));
                // $this->modelActivityUser->update($query, array(
                // '$set' => $data
                // ));
                // 发送模版消息
                // $this->sendTemplateMsg($userInfo['user_id'], $successInfo['prize_name'], $successInfo['prize_virtual_code']);
                return true;
            } else {
                // 失败的话
                $e = new \Exception($lotteryResult['error_msg'], $lotteryResult['error_code']);
                $this->modelErrorLog->log($this->activity_id, $e);
                echo ($this->error(- 40432, '没有中奖'));
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
        // http://yoox.rice5.com.cn/campaign/cny/recorduserinfo?exchange_id=5865f1edfcc2b60a008b456c&identity_id=xxxx&name=guoyongrong&mobile=13564100096&address=shanghai
        try {
            $this->view->disable();
            
            $name = trim($this->get('name', ''));
            $mobile = trim($this->get('mobile', ''));
            $address = trim($this->get('address', ''));
            $exchange_id = trim($this->get('exchange_id', ''));
            $identity_id = trim($this->get('identity_id', ''));
            
            if (empty($exchange_id)) {
                echo $this->error(- 40451, "中奖ID不能为空");
                return false;
            }
            
            if (empty($identity_id)) {
                echo $this->error(- 40452, "身份ID不能为空");
                return false;
            }
            
            // 根据不同的奖品类别进行处理
            $nameCheck = true;
            $mobileCheck = true;
            $addressCheck = true;
            if ($nameCheck) {
                if (empty($name)) {
                    echo $this->error(- 40453, "请填写姓名");
                    return false;
                }
            }
            if ($mobileCheck) {
                if (empty($mobile)) {
                    echo $this->error(- 40454, "请填写手机号");
                    return false;
                }
                if (! isValidMobile($mobile)) {
                    echo $this->error(- 40455, "手机号格式不正确");
                    return false;
                }
            }
            if ($addressCheck) {
                if (empty($address)) {
                    echo $this->error(- 40456, "请填写地址");
                    return false;
                }
            }
            $info = array(
                'is_valid' => true
            );
            
            if (! empty($name))
                $info['contact_name'] = $name;
            
            if (! empty($mobile))
                $info['contact_mobile'] = $mobile;
            
            if (! empty($address))
                $info['contact_address'] = $address;
            
            if (empty($info)) {
                echo $this->error(- 40457, "用户信息不能为空");
                return false;
            }
            
            // 判断是否中奖
            $exchangeInfo = $this->modelLotteryExchange->checkExchangeBy($identity_id, $exchange_id);
            if (empty($exchangeInfo)) {
                echo $this->error(- 40458, "该用户无此兑换信息");
                return false;
            }
            // 获取活动用户信息
            $userInfo = $this->modelActivityUser->getInfoById($exchangeInfo['memo']['activity_user_id']);
            if (empty($userInfo)) {
                echo $this->error(- 40458, "该用户无此兑换信息");
                return false;
            }
            
            // 检查手机号是否使用过了
            $isMobileExist = $this->modelLotteryExchange->findOne(array(
                'contact_mobile' => $mobile,
                'prize_is_virtual' => false
            ));
            if (! empty($isMobileExist)) {
                echo $this->error(- 40461, "手机号已存在");
                return false;
            }
            // 记录中奖用户的信息
            $this->modelLotteryExchange->updateExchangeInfo($exchange_id, $info);
            
            // // 更新活动用户的是否填写抽奖联系信息
            // $query = array(
            // '_id' => $userInfo['_id']
            // );
            // $data = array();
            // $data['memo'] = array_merge($userInfo['memo'], array(
            // 'is_record_lottery_user_contact_info' => true
            // ));
            // $this->modelActivityUser->update($query, array(
            // '$set' => $data
            // ));
            echo ($this->result('处理完成'));
            return true;
        } catch (\Exception $e) {
            // $this->modelErrorLog->log($this->activity_id, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 发送短信的接口
     */
    public function sendsmsAction()
    {
        // http://yoox.rice5.com.cn/campaign/cny/sendsms?exchange_id=5865f1edfcc2b60a008b456c&identity_id=xxxx&mobile=13564100096
        try {
            $this->view->disable();
            
            $mobile = trim($this->get('mobile', ''));
            $exchange_id = trim($this->get('exchange_id', ''));
            $identity_id = trim($this->get('identity_id', ''));
            
            if (empty($exchange_id)) {
                echo $this->error(- 40451, "中奖ID不能为空");
                return false;
            }
            
            if (empty($identity_id)) {
                echo $this->error(- 40452, "身份ID不能为空");
                return false;
            }
            
            if (empty($mobile)) {
                echo $this->error(- 40454, "手机号不能为空");
                return false;
            }
            if (! isValidMobile($mobile)) {
                echo $this->error(- 40455, "手机号格式不正确");
                return false;
            }
            
            // 判断是否中奖
            $exchangeInfo = $this->modelLotteryExchange->checkExchangeBy($identity_id, $exchange_id);
            if (empty($exchangeInfo)) {
                echo $this->error(- 40458, "该用户无此兑换信息");
                return false;
            }
            // 获取活动用户信息
            $userInfo = $this->modelActivityUser->getInfoById($exchangeInfo['memo']['activity_user_id']);
            if (empty($userInfo)) {
                echo $this->error(- 40458, "该用户无此兑换信息");
                return false;
            }
            
            // 发送短信
            // {"code":200,"name":"OK","description":"message queued, status=waiting"}
            $ret = $this->doSendSms($mobile, $exchangeInfo['prize_virtual_code']);
            if (! (isset($ret['code']) && $ret['code'] == 200)) {
                echo $this->error(- 40471, "无法发送短信");
                return false;
            }
            
            // 记录中奖用户的信息
            $info = array(
                'is_valid' => true
            );
            $info['contact_mobile'] = $mobile;
            $this->modelLotteryExchange->updateExchangeInfo($exchange_id, $info);
            
            // // 更新活动用户的是否填写抽奖联系信息
            // $query = array(
            // '_id' => $userInfo['_id']
            // );
            // $data = array();
            // $data['memo'] = array_merge($userInfo['memo'], array(
            // 'is_record_lottery_user_contact_info' => true
            // ));
            // $this->modelActivityUser->update($query, array(
            // '$set' => $data
            // ));
            
            // 发送短信
            echo ($this->result('处理完成'));
            return true;
            
            $universe = 'customer';
            $pass = '954ac5fbfab5760dc62b04238b1157027e65a599';
            $service_url = 'https://s3s.fr/api/triggersms/nph-3.pl';
            
            // Build a JSON array of recipient using data from DB or CSV file
            $rcpts = array(
                array(
                    'cellphone' => '33600000000',
                    'firstname' => 'John',
                    'lastname' => 'Doe',
                    'c0' => '123456'
                ),
                array(
                    'cellphone' => '33600000001',
                    'firstname' => 'Jane',
                    'lastname' => 'Doe',
                    'c0' => '123457'
                )
            );
            // Declare other sending parameters
            $params = array(
                'universe' => $universe, // your universe
                'key' => $pass, // your secret key
                'opcode' => 'O12345', // operation code to be used
                'message' => '6HIb4XwaP', // Spring message id
                'rcpts' => json_encode($rcpts)
            ); // recipients
               
            // Trigger SMS API documentation Splio development team
               
            // Encode all parameters including JSON array
            $params_str = http_build_query($params);
            
            // API call using curl library
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $service_url);
            
            // Turn of SSL check just for the example.
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // Can solve problems with curl
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Expect:"
            ));
            // Setup POST request
            curl_setopt($curl, CURLOPT_POST, count($params));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params_str);
            
            // Actual call
            $curl_response = curl_exec($curl);
            curl_close($curl); // Print the server response
                               
            // var_dump($curl_response);
                               
            // And the JSON received
                               
            // var_dump(json_decode($curl_response, true));
                               
            // {"status":"ok","message":"ok","code":200}
                               
            // {"status":"error","message":"incorrect/missing parameters","code":400}
            
            $ret = json_decode($curl_response, true);
            if ($ret['status'] == 'ok') {
                echo ($this->result('处理完成'));
                return true;
            } else {
                echo $this->error($ret['code'], $ret['message']);
                return false;
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 发送短信的接口
     */
    public function testsendsmsAction()
    {
        // http://yoox.rice5.com.cn/campaign/cny/testsendsms?code=xxxx&mobile=13564100096
        try {
            $this->view->disable();
            
            $mobile = trim($this->get('mobile', ''));
            $code = trim($this->get('code', uniqid()));
            
            if (empty($mobile)) {
                echo $this->error(- 40454, "请填写手机号");
                return false;
            }
            if (! isValidMobile($mobile)) {
                echo $this->error(- 40455, "手机号格式错误");
                return false;
            }
            
            // 发送短信
            $ret = $this->doSendSms($mobile, $code);
            echo ($this->result('处理完成', $ret));
            return true;
            
            $ret = json_decode($curl_response, true);
            if ($ret['status'] == 'ok') {
                echo ($this->result('处理完成'));
                return true;
            } else {
                echo $this->error($ret['code'], $ret['message']);
                return false;
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    protected function getOrCreateActivityUser($FromUserName, $nickname, $headimgurl, $redpack_user, $thirdparty_user, array $memo = array())
    {
        // 生成活动用户
        $userInfo = $this->modelActivityUser->getOrCreateByUserId($FromUserName, $nickname, $headimgurl, $redpack_user, $thirdparty_user, 1, 0, $this->activity_id, $memo);
        return $userInfo;
    }

    private function sendTemplateMsg($openid, $prize_name, $code)
    {
        try {
            // 模版消息
            $template_id = "q3VV4Pk_amMCmZbAoegVFCxxQrcEUrhgrJKjFQoU1G0";
            $url = "{$this->webPath}tuanyuan/cny/coupon";
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

    private function doSendSms($phone, $code)
    {
        // $code = rand(1, 10) . rand(1, 10) . rand(1, 10) . rand(1, 10) . rand(1, 10) . rand(1, 10); // 优惠码
        $phone = '0086' . $phone; // 手机号码前面必须带0086
        $message = '【YOOX】恭喜您获得一份来自意大利YOOX.CN的时尚新年礼，您的专属折上折优惠码' . $code . '！即日起至2017年1月20日前往YOOX.CN（http://t.cn/RMIDxxW）选购心仪单品，结算时输入该代码，季末特惠商品除外。感谢您的参与！';
        $url = 'https://api.spl4cn.com/api/forwardsms/1.php?universe=yoox_cn&key=29ef24d1b10b352939268cbf1a593a7e5dab8672&recipient=' . $phone . '&message=' . $message . '&unicode=1&long=1'; // 链接地址
        
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url, array());
        $statusCode = $response->getStatusCode();
        $isSuccessful = ($statusCode >= 200 && $statusCode < 300) || $statusCode == 304;
        
        if ($isSuccessful) {
            $body = $response->getBody();
            $json = json_decode($body, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \InvalidArgumentException('Unable to parse JSON data: ');
            }
            return $json;
        } else {
            throw new \Exception("服务器未有效的响应请求");
        }
        return;
        
        // 初始化
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($curl);
        curl_close($curl);
        // return json_decode($data, true);
        var_dump($data);
        return $data;
    }
}

