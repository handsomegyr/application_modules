<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Testwhitelist;

/**
 * @title({name="测试白名单管理"})
 *
 * @name 测试白名单管理
 */
class TestwhitelistController extends \App\Backend\Controllers\FormController
{

    private $modelTestwhitelist;

    public function initialize()
    {
        $this->modelTestwhitelist = new Testwhitelist();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['openid'] = array(
            'name' => '测试用openid',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['username'] = array(
            'name' => '测试用微信昵称',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '测试白名单';
    }

    protected function getModel()
    {
        return $this->modelTestwhitelist;
    }

    /**
     * 设置测试用户白名单的Hook
     */
    public function setAction()
    {
        // http://www.applicationmodule.com:10080/admin/weixincard/testwhitelist/set
        try {
            $this->view->disable();
            
            $this->_config = $this->getDI()->get('config');
            $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : $this->_config['weixin']['appid'];
            
            $this->_app = new \App\Weixin\Models\Application();
            $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            
            $this->_weixin = new \Weixin\Client();
            $this->_weixin->setAccessToken('UZmJYKlNmVLEbUJlZ3FaHXl153Wb1Sx7PrH6CQVfoTTBbI0gZijVCXp6q0iHwVo4_CP0sD52uZ6VRJu0GnqAFpvrs54VrpEbMyXZcjNEjA5hqD4DuPZuw8VD3xYqcU9-VHAgACAIUN');
            // if (! empty($this->_appConfig['access_token'])) {
            // $this->_weixin->setAccessToken($this->_appConfig['access_token']);
            // }
            
            $whiteList = $this->modelTestwhitelist->getAll();
            $openids = array();
            $usernames = array();
            $ids = array();
            foreach ($whiteList as $item) {
                $ids[] = $item['_id'];
                if (! empty($item['openid'])) {
                    $openids[] = $item['openid'];
                }
                if (! empty($item['username'])) {
                    $usernames[] = $item['username'];
                }
            }
            if (! empty($ids)) {
                if (! empty($openids) || ! empty($usernames)) {
                    $ret = $this->_weixin->getCardManager()->testwhitelistSet($openids, $usernames);
                    if (! empty($ret['errcode'])) {
                        throw new \Exception($ret['errmsg'], $ret['errcode']);
                    }
                }
                $this->modelTestwhitelist->updateIsset($ids, true);
            }
            
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}