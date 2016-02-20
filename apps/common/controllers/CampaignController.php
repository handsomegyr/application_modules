<?php
namespace Webcms\Common\Controllers;

class CampaignController extends ControllerBase
{

    protected function initialize()
    {
        parent::initialize();
        
        if (! $this->getRequest()->isAjax()) {
            $jssdk = $this->getJssdkInfo();
            $this->assign("jssdkInfo", $jssdk);
        }
    }

    

    public function getJssdkInfo()
    {
        $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $info = $cache->get($cacheKey);
        
        if (empty($info)) {
            $jssdk = doGet("http://wxapi.toysrus.com.cn/wxapi/getjt.php");
            $jssdk = (string) $jssdk;
            if (! empty($jssdk)) {
                if (isJson($jssdk)) {
                    $info = json_decode($jssdk, true);
                    $cache->save($cacheKey, $info, 1 * 60); // 1分钟
                } else {
                    throw new \Exception("获取微信jssdk信息不是有效的json格式");
                }
            } else {
                throw new \Exception("可能网络繁忙,获取微信jssdk信息请求失败");
            }
        } else {
            // die('getJssdkInfo from cache');
        }
        return $info;
    }

    public function getAccessToken()
    {
        $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $info = $cache->get($cacheKey);
        
        if (empty($info)) {
            $accessToken = doGet("http://wxapi.toysrus.com.cn/wxapi/getat.php");
            $accessToken = (string) $accessToken;
            if (! empty($accessToken)) {
                if (isJson($accessToken)) {
                    $ret = json_decode($accessToken, true);
                    if (! empty($ret["access_token"])) {
                        $info = $ret['access_token'];
                        $cache->save($cacheKey, $info, 1 * 60); // 1分钟
                    } else {
                        throw new \Exception("获取微信accesstoken信息为空");
                    }
                } else {
                    throw new \Exception("获取微信accesstoken信息不是有效的json格式");
                }
            } else {
                throw new \Exception("可能网络繁忙,获取微信accesstoken信息请求失败");
            }
        } else {
            // die('getAccessToken from cache');
        }
        return $info;
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
    
    public function getaccesstokenAction()
    {
        $this->view->disable();
        $accessToken = $this->getAccessToken();
        echo 'access token:'.$accessToken;
    }
}
