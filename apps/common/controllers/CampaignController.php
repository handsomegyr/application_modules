<?php
namespace App\Common\Controllers;

class CampaignController extends ControllerBase
{

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 判断是否已关注
     *
     * @param string $FromUserName            
     * @return boolean
     */
    public function isSubscribed($FromUserName)
    {
        $is_subscribe = false;
        /**
         * 先从缓存中查找有没有该$FromUserName所对应的记录，
         * 如果没有就调用微信用户接口获取微信用户信息,
         * 根据用户信息中的subscribe字段判断是否关注
         * 如果关注的话，将这条用户信息存入缓存中，缓存时间一天
         */
        $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__, $FromUserName);
        $cache = $this->getDI()->get("cache");
        $is_subscribe = false; // $cache->get($cacheKey);
        
        if (empty($is_subscribe)) {
            $access_token = $this->getAccessToken();
            $weixin = new \Weixin\Client();
            $weixin->setAccessToken($access_token);
            $userInfo = $weixin->getUserManager()->getUserInfo($FromUserName);
            if (! empty($userInfo['errcode'])) {
                $e = new \Exception($userInfo['errmsg'], $userInfo['errcode']);
                throw $e;
            } else {
                if (! empty($userInfo['subscribe'])) {
                    $is_subscribe = true;
                    $cache->save($cacheKey, $is_subscribe, 2 * 60); // 2分钟
                }
            }
        }
        return $is_subscribe;
    }

    public function clearcacheAction()
    {
        $this->view->disable();
        $cache = $this->getDI()->get("cache");
        $key = $this->get('key', '');
        if (! empty($key)) {
            $cache->delete($key);
        } else {
            // Delete all items from the cache
            $keys = $cache->queryKeys();
            foreach ($keys as $key) {
                $cache->delete($key);
            }
        }
        echo 'Clear Cache OK';
    }

    public function getcachekeyAction()
    {
        $this->view->disable();
        $cache = $this->getDI()->get("cache");
        $keys = $cache->queryKeys();
        var_dump($keys);
    }

    /**
     * 按COOKIE方式，进行授权处理
     *
     * @name 授权处理
     */
    public function weixinauthorizecallbackAction()
    {
        try {
            // http://xxxx.umaman.com/xxx/index/weixinauthorizecallback?callbackUrl=xxx
            // $callbackUrl = trim($this->get('callbackUrl', ''));
            $callbackUrl = isset($_SESSION['Weixin_callbackUrl']) ? $_SESSION['Weixin_callbackUrl'] : '';
            if (empty($callbackUrl)) {
                die('callbackurl 不能为空');
            }
            $userInfo = empty($_COOKIE['Weixin_userInfo']) ? array() : json_decode($_COOKIE['Weixin_userInfo'], true);
            
            if (empty($userInfo)) {
                // 如果在进行授权处理中的话
                if (! empty($_SESSION['isAuthorizing'])) {
                    $FromUserName = trim($this->get('FromUserName', ''));
                    $nickname = trim($this->get('nickname', ''));
                    $headimgurl = trim($this->get('headimgurl', ''));
                    $timestamp = trim($this->get('timestamp', ''));
                    $signkey = trim($this->get('signkey', ''));
                    
                    // url的参数上已经有了FromUserName参数并且不是空的时候
                    if (! empty($FromUserName)) {
                        $secretKey = $this->getSecretKey();
                        // 校验微信id,上线测试时需要加上去
                        if (empty($secretKey) || $this->validateOpenid($FromUserName, $timestamp, $secretKey, $signkey)) {
                            // 授权处理完成
                            unset($_SESSION['isAuthorizing']);
                            // 存储微信用户到COOKIE,保存30天
                            $userInfo = array(
                                'FromUserName' => $FromUserName,
                                'nickname' => trim($nickname),
                                'headimgurl' => trim($headimgurl),
                                'signkey' => $signkey,
                                'timestamp' => $timestamp
                            );
                            setcookie('Weixin_userInfo', json_encode($userInfo), time() + 3600 * 24 * 30, '/');
                            $_COOKIE['Weixin_userInfo'] = json_encode($userInfo);
                            
                            // 授权成功之后的处理
                            $this->authorize();
                        }
                    }
                }
            }
            // 跳转地址
            $this->_redirect($callbackUrl);
            exit();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function getcookiesAction()
    {
        $this->view->disable();
        
        var_dump($_COOKIE['Weixin_userInfo']);
    }

    public function getSecretKey()
    {
        $config = $this->getDI()->get('config');
        $secretKey = $config['weixinAuthorize']['secretKey'];
        return $secretKey;
    }

    public function authorize()
    {}
}
