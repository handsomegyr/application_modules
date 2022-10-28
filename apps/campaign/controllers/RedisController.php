<?php

namespace App\Campaign\Controllers;

/**
 * Redis事例
 *
 * @author Administrator
 *        
 */
class RedisController extends ControllerBase
{

    private $redis = null;

    public function initialize()
    {
        try {
            parent::initialize();
            $this->view->disable();
            $di = \Phalcon\Di\Di::getDefault();
            $this->redis = $di->get("redis");
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
        }
    }

    /**
     * 测试的接口
     */
    public function testAction()
    {
        // http://www.myapplicationmodule.com/campaign/redis/test
        try {
            $this->redis->set('application_modules_key1', uniqid());
            $value = $this->redis->get('application_modules_key1');
            echo $this->result("OK", $value);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取redis所有key信息
     */
    public function getallkeysAction()
    {
        // http://www.myapplicationmodule.com/campaign/redis/getallkeys
        try {
            // 获取所有的key
            $ret['allkeys'] = $this->redis->keys('*');
            echo $this->result('OK', $ret);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 删除redis所有key信息
     */
    public function deletekeysAction()
    {
        // http://www.myapplicationmodule.com/campaign/redis/deletekeys
        try {
            // 获取所有的key
            $allkeys = $this->redis->keys('*');
            if (!empty($allkeys)) {
                foreach ($allkeys as $key) {
                    $this->redis->del($key);
                    // // 检查key中是否有以下的字符串
                    // $pos1 = strpos($key, 'live');
                    // // 注意这里使用的是 ===。简单的 == 不能像我们期待的那样工作，
                    // // 因为 'a' 是第 0 位置上的（第一个）字符。
                    // // 如果都没有的话就删除这个key
                    // if ($pos1 === false) {
                    // // echo "The string '$findme' was not found in the string '$mystring'";
                    // $this->redis->del($key);
                    // }
                }
            }
            $ret['allkeys'] = $this->redis->keys('*');
            echo $this->result('OK', $ret);
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }


    /**
     * 直播用
     * http://www.myapplicationmodule.com/campaign/redis/deletekeys
     * http://www.myapplicationmodule.com/live/user/login?room_id=5a4f2686cb6ef462f64ee4a1&openid=xxx&nickname=%E9%83%AD%E6%B0%B8%E8%8D%A3&headimgurl=http://qzapp.qlogo.cn/qzapp/221403/12EBD57369718EBF0CC9FC352C2969AB/100&unionid=uxxx&authtype=weixin&source=weixin&channel=weixin
     * http://www.myapplicationmodule.com/live/user/login?room_id=5a4f2686cb6ef462f64ee4a1&openid=xxx1&nickname=%E6%96%BD%E4%B8%B9&headimgurl=https://tfs.alipayobjects.com/images/partner/T1VpheXnCdXXXXXXXX&unionid=uxxx1&authtype=weixin&source=weixin&channel=weixin
     * http://www.myapplicationmodule.com/live/resource/getlist
     * 
     * http://www.myapplicationmodule.com/live/index.html?user_id=1
     * http://www.myapplicationmodule.com/live/index.html?user_id=2
     */
}
