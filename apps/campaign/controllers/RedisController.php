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

    public function initialize()
    {
        try {
            parent::initialize();
            $this->view->disable();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
        }
    }

    /**
     * 测试的接口
     */
    public function testAction()
    {
        // http://www.applicationmodule.com:10080/campaign/redis/test
        try {
            $di = \Phalcon\DI::getDefault();
            $redis = $di->get("redis");
            //var_dump($redis);
            
            $redis->set('application_modules_key1', uniqid());
            $value = $redis->get('application_modules_key1');
            
            echo $this->result("OK", $value);
            return true;
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }
}

