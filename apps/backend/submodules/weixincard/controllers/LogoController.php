<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Logo;

/**
 * @title({name="商户logo管理"})
 *
 * @name 商户logo管理
 */
class LogoController extends \App\Backend\Controllers\FormController
{

    private $modelLogo;

    public function initialize()
    {
        $this->modelLogo = new Logo();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['logo'] = array(
            'name' => '商户logo图片',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelLogo->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['logo_url'] = array(
            'name' => '商户logo的url',
            'data' => array(
                'type' => 'string',
                'length' => '100'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
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
        return '商户logo';
    }

    protected function getModel()
    {
        return $this->modelLogo;
    }

    /**
     * 上传卡券的商户logo的Hook
     */
    public function uploadAction()
    {
        // http://www.applicationmodule.com:10080/admin/weixincard/logo/upload
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
            $logoList = $this->modelLogo->getAll();
            foreach ($logoList as $item) {
                if (! empty($item['is_uploaded'])) {
                    continue;
                }
                $uploadPath = $this->modelLogo->getUploadPath();
                $logo = APP_PATH . "public/upload/{$uploadPath}/{$item['logo']}";
                // die($logo);
                $ret = $this->_weixin->getCardManager()->uploadLogoUrl($logo);
                if (! empty($ret['errcode'])) {
                    throw new \Exception($ret['errmsg'], $ret['errcode']);
                }
                $logo_url = $ret['url'];
                $this->modelLogo->updateIsUploaded(myMongoId($item['_id']), $logo_url);
            }
            
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}