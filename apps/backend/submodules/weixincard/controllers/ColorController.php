<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Color;

/**
 * @title({name="颜色管理"})
 *
 * @name 颜色管理
 */
class ColorController extends \App\Backend\Controllers\FormController
{

    private $modelColor;

    public function initialize()
    {
        $this->modelColor = new Color();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['value'] = array(
            'name' => '颜色值',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => true
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
        
        $schemas['name'] = array(
            'name' => '颜色名称',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => true
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
        return '颜色';
    }

    protected function getModel()
    {
        return $this->modelColor;
    }

    /**
     * 从微信公众平台上获取颜色列表到本地的Hook
     */
    public function getcolorsAction()
    {
        // http://www.applicationmodule.com:10080/admin/weixincard/color/getcolors
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
            
            $ret = $this->_weixin->getCardManager()->getcolors();
            // print_r($ret);
            // die('xxx');
            
            // Array ( [errcode] => 0 [errmsg] => ok [colors] => Array ( [0] => Array ( [name] => Color010 [value] => #55bd47 ) [1] => Array ( [name] => Color020 [value] => #10ad61 ) [2] => Array ( [name] => Color030 [value] => #35a4de ) [3] => Array ( [name] => Color040 [value] => #3d78da ) [4] => Array ( [name] => Color050 [value] => #9058cb ) [5] => Array ( [name] => Color060 [value] => #de9c33 ) [6] => Array ( [name] => Color070 [value] => #ebac16 ) [7] => Array ( [name] => Color080 [value] => #f9861f ) [8] => Array ( [name] => Color090 [value] => #e75735 ) [9] => Array ( [name] => Color100 [value] => #d54036 ) [10] => Array ( [name] => Color101 [value] => #cf3e36 ) ) )
            if (! empty($ret['errcode'])) {
                throw new \Exception($ret['errmsg'], $ret['errcode']);
            }
            
            if (! empty($ret['colors'])) {
                $this->modelColor->remove(array());
                foreach ($ret['colors'] as $color) {
                    $this->modelColor->record($color['value'], $color['name']);
                }
            }
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}