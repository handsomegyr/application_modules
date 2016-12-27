<?php
namespace App\Campaign\Controllers;

/**
 * 发微信红包事例
 *
 * @author Administrator
 *        
 */
class HongbaoController extends ControllerBase
{

    private $activity_id = '5714eff747489a3e778b4584';

    private $customer_id = '5714eefb47489a03098b4580';

    private $is_need_subscribed = false;

    private $modelErrorLog = null;

    private $modelHongbao = null;

    private $modelHongbaoUser = null;

    private $modelWeixinredpack = null;

    private $modelWeixinApplication = null;

    private $servicesApi = null;

    private $today = '';

    private $now = null;

    public function initialize()
    {
        $this->now = getCurrentTime();
        $this->today = date('Ymd', $this->now->sec);
        
        $this->modelErrorLog = new \App\System\Models\ErrorLog();
        
        $this->modelHongbao = new \App\Campaign\Models\Hongbao();
        
        $this->modelHongbaoUser = new \App\Weixinredpack\Models\User();
        
        $this->modelWeixinApplication = new \App\Weixin\Models\Application();
        
        $this->modelWeixinredpack = new \App\Weixinredpack\Models\Redpack();
        
        $this->servicesApi = new \App\Weixinredpack\Services\Api(array(), 'nojson');
        try {
            parent::initialize();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
        }
    }

    /**
     * 首页，任何人都能进入
     */
    public function indexAction()
    {
        try {
            // http://160418fg0095demo.itg.site/hongbao/index/index?FromUserName=ol6BkwxV-PJVlfRUd5nhUJ_EGM20&istest=1
            $userInfo = $this->getUserInfo();
            // 检查是否已经有了红包所需的微信号
            $hongbaoUserInfo = $this->modelHongbaoUser->getInfoByFromUserName($userInfo['FromUserName']);
            if (empty($hongbaoUserInfo) || empty($hongbaoUserInfo['re_openid'])) {
                // 需要进行微信授权
                $this->doHongbaoAuthorize();
            }
            $hongbaoInfo = $this->modelHongbao->getInfoByFromUserName($userInfo['FromUserName']);
            $money = 0;
            if (! empty($hongbaoInfo)) {
                $money = $hongbaoInfo['money'] - $hongbaoInfo['get_money'];
            }
            $this->assign('money', $money);
            
            // 检查今天是否已经提现过了
            $is_withdraw_today = false;
            if ($hongbaoUserInfo['withdraw_date'] == $this->today) {
                $is_withdraw_today = true;
            }
            $this->assign('is_withdraw_today', $is_withdraw_today);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 红包提现的接口
     */
    public function withdrawAction()
    {
        // http://160418fg0095demo.itg.site/hongbao/index/withdraw?FromUserName=xxxx&money=100
        try {
            $this->view->disable();
            
            $money = intval($this->get('money', '0')); // 红包
            if (empty($money) || $money < 100) {
                echo $this->error(- 95, "提现金额不满足要求");
                return false;
            }
            
            $FromUserName = $this->get('FromUserName', '');
            if (empty($FromUserName)) {
                echo ($this->error(- 91, "微信ID为空或不正确"));
                return false;
            }
            
            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $FromUserName);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo $this->error(- 99, "上次操作还未完成,请等待");
                return false;
            }
            
            $hongbaoInfo = $this->modelHongbao->getInfoByFromUserName($FromUserName);
            
            if (empty($hongbaoInfo)) {
                echo ($this->error(- 93, '微信用户ID不正确'));
                return false;
            }
            
            // 账户里面的红包金额够用于提现吗
            if (($hongbaoInfo['money'] - $hongbaoInfo['get_money']) < $money) {
                echo $this->error(- 96, "帐号的金额不足，无法提现");
                return false;
            }
            
            $hongbaoUserInfo = $this->modelHongbaoUser->getInfoByFromUserName($FromUserName);
            if (empty($hongbaoUserInfo) || empty($hongbaoUserInfo['re_openid'])) {
                echo ($this->error(- 97, '微信红包用户ID不正确'));
                return false;
            }
            
            // 进行红包提现操作
            // 检查今天是否已经提现过了
            if ($hongbaoUserInfo['withdraw_date'] == $this->today) {
                echo $this->error(- 98, "今天已经成功提现过了");
                return false;
            }
            
            $redpackInfo = $this->modelWeixinredpack->getInfo4Today($this->now);
            if (empty($redpackInfo)) {
                echo ($this->error(- 89, "微信红包没有设置"));
                return false;
            }
            
            $redpack_id = $redpackInfo['_id'];
            $re_openid = $hongbaoUserInfo['re_openid'];
            $amount = intval($money);
            $info = array(
                'openid' => $FromUserName,
                'nickname' => $hongbaoInfo['nickname'],
                'headimgurl' => $hongbaoInfo['headimgurl'],
                're_nickname' => $hongbaoInfo['nickname'],
                're_headimgurl' => $hongbaoInfo['headimgurl']
            );
            
            $config = $this->getDI()->get('config');
            $token = $this->modelWeixinApplication->getTokenByAppid($config['weixin']['appid']);
            
            // 当正式上线的时候改成true
            $this->servicesApi->isNeedSendRedpack = true;
            $this->servicesApi->weixinRedpackSettings = array(
                'appid' => $token['appid'],
                'secret' => $token['secret'],
                'access_token' => $token['access_token'],
                'mch_id' => $token['mch_id'],
                'sub_mch_id' => $token['sub_mch_id'],
                'key' => $token['key'],
                'cert.pem' => APP_PATH . "cache/apiclient_cert.pem",
                'key.pem' => APP_PATH . "cache/apiclient_key.pem"
            );
            $gotInfo = $this->servicesApi->sendRedpack($this->activity_id, $this->customer_id, $redpack_id, $re_openid, $amount, $info);
            if (empty($gotInfo['error_code']) && ! empty($gotInfo['result'])) {
                $exchangeInfo = $gotInfo['result'];
                // 更新红包信息
                $this->modelHongbao->incAmount($FromUserName, $amount);
                // 更新当天提现红包的时间
                $this->modelHongbaoUser->updateWithdrawDate($hongbaoUserInfo['_id'], $this->today, $money);
                echo $this->result("OK", $exchangeInfo);
                return true;
            } else {
                // 失败的话
                echo ($this->error($gotInfo['error_code'], $gotInfo['error_msg']));
                return false;
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 玩具筋斗云授权之后的页面
     */
    public function hongbaoauthorizeAction()
    {
        try {
            // http://160418fg0095demo.itg.site/hongbao/index/hongbaoauthorize?callbackUrl=xxx
            $callbackUrl = trim($this->get('callbackUrl', ''));
            $callbackUrl = urldecode($callbackUrl);
            
            $FromUserName = trim($this->get('FromUserName', ''));
            $nickname = trim($this->get('nickname', ''));
            $headimgurl = trim($this->get('headimgurl', ''));
            $timestamp = trim($this->get('timestamp', ''));
            $signkey = trim($this->get('signkey', ''));
            
            // url的参数上已经有了FromUserName参数并且不是空的时候
            if (! empty($FromUserName)) {
                $secretKey = "160418fg0095";
                // 校验微信id,上线测试时需要加上去
                if ($this->validateOpenid4Guotai($FromUserName, $timestamp, $secretKey, $signkey)) {
                    
                    $key = cacheKey(__FILE__, __CLASS__, $FromUserName);
                    $objLock = new \iLock($key);
                    if ($objLock->lock()) {
                        $this->refreshPage(5);
                    }
                    
                    // 获取用户个人信息
                    $userInfo = empty($_SESSION['Weixin_userInfo']) ? array() : $_SESSION['Weixin_userInfo'];
                    if (! empty($userInfo)) {
                        if (isset($userInfo['user_id'])) {
                            $userInfo['FromUserName'] = $userInfo['user_id'];
                        }
                        // 授权成功之后的处理
                        $hongbaoUserInfo = $this->modelHongbaoUser->getInfoByFromUserName($userInfo['FromUserName']);
                        if (empty($hongbaoUserInfo)) {
                            // 需要进行微信授权
                            $this->modelHongbaoUser->record($userInfo['FromUserName'], $FromUserName);
                            usleep(1000 * 300); // 300毫秒
                        } else {
                            if (empty($hongbaoUserInfo['re_openid'])) {
                                $this->modelHongbaoUser->updateReopenid($hongbaoUserInfo['_id'], $FromUserName);
                                usleep(1000 * 300); // 300毫秒
                            }
                        }
                    }
                }
            }
            
            // 跳转地址
            if (empty($callbackUrl)) {
                $callbackUrl = $this->getUrl("index");
            }
            $this->_redirect($callbackUrl);
            exit();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 测试发送微信红包
     *
     * @return boolean
     */
    public function sendredpackAction()
    {
        // http://160418fg0095demo.itg.site/hongbao/index/sendredpack
        try {
            $this->view->disable();
            $redpack_id = '5714ef1447489a42778b4587';
            $re_openid = 'o8IA5v3TFEYexu_vPSMsf_7Yv1bc';
            $defaultInfo = array(
                'openid' => 'ol6BkwxV-PJVlfRUd5nhUJ_EGM20',
                'nickname' => '郭永荣',
                'headimgurl' => 'http://wx.qlogo.cn/mmopen/gXzibx1VXR1Y8rPYKW6vWLYbEON8zdZ3P4DnZeEHNY6Jib6eT9wjEBqwibtUSuLMqYnviakoop11iadZeP4xnoSTNGRqZltajjib78/0',
                're_nickname' => '郭永荣',
                're_headimgurl' => 'http://wx.qlogo.cn/mmopen/gXzibx1VXR1Y8rPYKW6vWLYbEON8zdZ3P4DnZeEHNY6Jib6eT9wjEBqwibtUSuLMqYnviakoop11iadZeP4xnoSTNGRqZltajjib78/0',
                'client_ip' => getIp()
            );
            
            // 发红包
            $amount = 100; // 1元
            $config = $this->getDI()->get('config');
            $token = $this->modelWeixinApplication->getTokenByAppid($config['weixin']['appid']);
            // 当正式上线的时候改成true
            $this->servicesApi->isNeedSendRedpack = true;
            $this->servicesApi->weixinRedpackSettings = array(
                'appid' => $token['appid'],
                'secret' => $token['secret'],
                'access_token' => $token['access_token'],
                'mch_id' => $token['mch_id'],
                'sub_mch_id' => $token['sub_mch_id'],
                'key' => $token['key'],
                'cert.pem' => APP_PATH . "cache/apiclient_cert.pem",
                'key.pem' => APP_PATH . "cache/apiclient_key.pem"
            );
            // print_r($this->servicesApi->weixinRedpackSettings);
            // die('xxxxxxxx');
            $gotInfo = $this->servicesApi->sendRedpack($this->activity_id, $this->customer_id, $redpack_id, $re_openid, $amount, $defaultInfo);
            
            echo ($this->result('处理完成', $gotInfo));
            return true;
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }
    
    // 获取用户信息
    private function getUserInfo($is_need_subscribed = true)
    {
        $userInfo = empty($_SESSION['Weixin_userInfo']) ? array() : $_SESSION['Weixin_userInfo'];
        if (! empty($userInfo)) {
            $userInfo['FromUserName'] = $userInfo['user_id'];
            $userInfo['nickname'] = $userInfo['user_name'];
            $userInfo['headimgurl'] = $userInfo['user_headimgurl'];
            $this->assign('FromUserName', $userInfo['FromUserName']);
            $this->assign('nickname', $userInfo['nickname']);
            $this->assign('headimgurl', str_replace('/0', '/64', $userInfo['headimgurl']));
            return $userInfo;
        } else {
            // 不是接口调用的话
            if (! $this->getRequest()->isAjax()) {
                unset($_SESSION['isWeixinAuthorizing']);
                unset($_SESSION['Weixin_userInfo']);
                $this->refreshPage(5);
            } else {
                return array();
            }
        }
    }

    /**
     * 玩具筋斗云授权
     */
    private function doHongbaoAuthorize()
    {
        $request = $this->getRequest();
        $scheme = $request->getScheme();
        $path = '/';
        $moduleName = 'hongbao';
        $controllerName = 'index';
        
        $callbackUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $callbackUrl = urlencode($callbackUrl);
        
        $redirectUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$path}{$moduleName}/{$controllerName}/hongbaoauthorize?callbackUrl={$callbackUrl}";
        $redirectUrl = urlencode($redirectUrl);
        
        // 玩具筋斗云的授权
        $authorizeUrl = "http://160418fg0095.intonead.com/weixin/sns/index";
        $scope = "snsapi_base";
        $url = "{$authorizeUrl}?scope={$scope}&redirect={$redirectUrl}";
        
        header("Location:{$url}");
        exit();
    }

    /**
     * 微信openid校验
     *
     * @param string $FromUserName            
     * @param string $timestamp            
     * @param string $secretKey            
     * @param string $signature            
     * @return boolean
     */
    private function validateOpenid4Guotai($FromUserName, $timestamp, $secretKey, $signature)
    {
        $secret = sha1($FromUserName . "|" . $secretKey . "|" . $timestamp);
        if ($signature != $secret) {
            return false;
        } else {
            return true;
        }
    }
}

