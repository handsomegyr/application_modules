<?php
namespace App\Weixin\Models;

class Application extends \App\Common\Models\Weixin\Application
{

    private $_params = array();

    private $_expire = 30;

    /**
     * 开启token的缓存信息
     *
     * @param number $cacheTime            
     */
    public function setTokenCache($expire = 300)
    {
        $this->_expire = (int) $expire;
    }

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getApplicationInfoByAppId($appid)
    {
        $cacheKey = $this->getCacheKey($appid);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);
        if (empty($application)) {
            $application = $this->findOne(array(
                'appid' => $appid
            ));
            if (! empty($application)) {
                $expire_time = $this->getExpireTime($application);
                $cache->save($cacheKey, $application, $expire_time);
            }
        }
        return $application;
    }

    /**
     * 获取有效的token信息
     *
     * @throws Exception
     * @return mixed array
     */
    public function getTokenByAppid($appid)
    {
        $cacheKey = $this->getCacheKey($appid);
        $cache = $this->getDI()->get('cache');
        noLock:
        $token = $this->getApplicationInfoByAppId($appid);
        if ($token == null) {
            return null;
        }
        
        try {
            $token = $this->refreshInfo($cacheKey, $token);
        } catch (\Exception $e) {}
        return $token;
    }

    /**
     * 发送通知消息给某人
     *
     * @param string $to
     *            openid
     * @param string $content
     *            内容
     */
    public function notify($to, $content)
    {
        $appConfig = $this->getToken();
        $weixin = new \Weixin\Client();
        if (! empty($appConfig['access_token'])) {
            $weixin->setAccessToken($appConfig['access_token']);
            $weixin->getMsgManager()
                ->getCustomSender()
                ->sendText($to, $content);
            return true;
        }
        return false;
    }

    /**
     * 调试数据
     *
     * @param string $type            
     * @return array
     */
    public function debug($type)
    {
        $datas = array();
        switch ($type) {
            case 'subscribe':
                $datas['FromUserName'] = 'FromUserName';
                $datas['ToUserName'] = 'ToUserName';
                $datas['MsgType'] = 'event';
                $datas['Event'] = 'subscribe';
                $datas['EventKey'] = 'qrscene_1';
                break;
            case 'text':
                $datas['Content'] = '联系';
                $datas['FromUserName'] = 'o8NOajuFB07kWd4eHbKhY24OXPFE';
                $datas['ToUserName'] = 'gh_127af9cfa796';
                $datas['MsgType'] = 'text';
                break;
            case 'reply':
                $datas['Content'] = '图片';
                $datas['FromUserName'] = 'FromUserName';
                $datas['ToUserName'] = 'ToUserName';
                $datas['MsgType'] = 'text';
                break;
            case 'image':
                $datas['FromUserName'] = 'FromUserName';
                $datas['ToUserName'] = 'ToUserName';
                $datas['MsgType'] = 'image';
                $datas['PicUrl'] = 'http://mmbiz.qpic.cn/mmbiz/DOiao54mZbb3K2hOsGN8dYQaAZIC8L46iaYictB2NNgJ1iav34rEX0bH6wnwpzanx0Dt9Zt0LZiaUsmM9EmgDESkXKw/0';
                $datas['MediaId'] = 'IozZY1RthEwbB3VsUux8kT0RNY14qXBZ-fYCdKS8u4CrExJe6ecRooSjiXc4n2uE';
                break;
            case 'event':
                $datas['FromUserName'] = 'FromUserName';
                $datas['ToUserName'] = 'ToUserName';
                $datas['MsgType'] = 'event';
                $datas['Event'] = 'CLICK';
                $datas['EventKey'] = 'Click测试文档';
                break;
        }
        return $datas;
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getExpireTime(array &$info)
    {
        $expire_time = 60;
        return $expire_time;
    }

    private function getCacheKey($appid)
    {
        $cacheKey = cacheKey(__FILE__, __CLASS__, $appid);
        return $cacheKey;
    }

    private function refreshInfo($cacheKey, $token)
    {
        $cache = $this->getDI()->get('cache');
        if (isset($token['access_token_expire']) && ! empty($token['is_advanced'])) {
            if ($token['access_token_expire']->sec <= time()) {
                if (! empty($token['appid']) && ! empty($token['secret'])) {
                    $lockKey = cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__);
                    $objLock = new \iLock($lockKey);
                    if (! $objLock->lock()) {
                        $objToken = new \Weixin\Token\Server($token['appid'], $token['secret']);
                        $arrToken = $objToken->getAccessToken();
                        if (! isset($arrToken['access_token'])) {
                            throw new \Exception(json_encode($arrToken));
                        }
                        
                        $cmd = array();
                        $cmd['query'] = array(
                            '_id' => $token['_id']
                        );
                        $cmd['update'] = array(
                            '$set' => array(
                                'access_token' => $arrToken['access_token'],
                                'access_token_expire' => getCurrentTime(time() + 7200)
                            )
                        );
                        $cmd['new'] = true;
                        $cmd['upsert'] = true;
                        $rst = $this->findAndModify($cmd);
                        if ($rst['ok'] == 1) {
                            $expire_time = $this->getExpireTime($rst['value']);
                            $cache->save($cacheKey, $rst['value'], $expire_time);
                            $objLock->release();
                            $token = $rst['value'];
                        } else {
                            throw new \Exception(json_encode($rst));
                        }
                    }
                }
            }
            
            // 缓存有效期不能超过token过期时间
            if ((time() + $this->_expire) > $token['access_token_expire']->sec) {
                $this->_expire = $token['access_token_expire']->sec - time();
            }
        }
        
        jsnoLock:
        // 获取jsapi_ticket
        if (! empty($token['is_advanced'])) {
            if (! isset($token['jsapi_ticket_expire']) || $token['jsapi_ticket_expire']->sec <= time()) {
                if (! empty($token['appid']) && ! empty($token['secret'])) {
                    $lockKey = cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__);
                    $objLock = new \iLock($lockKey);
                    if (! $objLock->lock()) {
                        
                        // 获取jsapi_ticket
                        $objJssdk = new \Weixin\Jssdk();
                        $objJssdk->setAppId($token['appid']);
                        $objJssdk->setAppSecret($token['secret']);
                        $objJssdk->setAccessToken($token['access_token']);
                        $arrJsApiTicket = $objJssdk->getJsApiTicket();
                        
                        $cmd = array();
                        $cmd['query'] = array(
                            '_id' => $token['_id']
                        );
                        $cmd['update'] = array(
                            '$set' => array(
                                'jsapi_ticket_expire' => getCurrentTime(time() + $arrJsApiTicket['expires_in']),
                                'jsapi_ticket' => $arrJsApiTicket['ticket']
                            )
                        );
                        $cmd['new'] = true;
                        $cmd['upsert'] = true;
                        $rst = $this->findAndModify($cmd);
                        if ($rst['ok'] == 1) {
                            $expire_time = $this->getExpireTime($rst['value']);
                            $cache->save($cacheKey, $rst['value'], $expire_time);
                            $objLock->release();
                            $token = $rst['value'];
                        } else {
                            throw new \Exception(json_encode($rst));
                        }
                    }
                }
            }
        }
        
        weixincardnoLock:
        // 获取微信卡券的api_ticket
        if (! empty($token['is_weixin_card'])) {
            if (! isset($token['wx_card_api_ticket_expire']) || $token['wx_card_api_ticket_expire']->sec <= time()) {
                if (! empty($token['appid']) && ! empty($token['secret'])) {
                    $lockKey = cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__);
                    $objLock = new \iLock($lockKey);
                    if (! $objLock->lock()) {
                        
                        // 获取微信卡券的api_ticket
                        $weixin = new \Weixin\Client();
                        $weixin->setAccessToken($token['access_token']);
                        $ret = $weixin->getCardManager()->getApiTicket();
                        
                        $cmd = array();
                        $cmd['query'] = array(
                            '_id' => $token['_id']
                        );
                        $cmd['update'] = array(
                            '$set' => array(
                                'wx_card_api_ticket_expire' => getCurrentTime(time() + $ret['expires_in']),
                                'wx_card_api_ticket' => $ret['ticket']
                            )
                        );
                        $cmd['new'] = true;
                        $cmd['upsert'] = true;
                        $rst = $this->findAndModify($cmd);
                        if ($rst['ok'] == 1) {
                            $expire_time = $this->getExpireTime($rst['value']);
                            $cache->save($cacheKey, $rst['value'], $expire_time);
                            $objLock->release();
                            $token = $rst['value'];
                        } else {
                            throw new \Exception(json_encode($rst));
                        }
                    }
                }
            }
        }
        return $token;
    }
}