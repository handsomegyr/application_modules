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
                'length' => 128,
                'file' => array(
                    'path' => $this->modelLogo->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
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
                'length' => '128'
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
        // http://www.applicationmodule.com/admin/weixincard/logo/upload
        try {
            $this->view->disable();
            $weixin = $this->getWeixin();
            
            $logoList = $this->modelLogo->getAll();
            foreach ($logoList as $item) {
                if (! empty($item['is_uploaded'])) {
                    continue;
                }
                $uploadPath = $this->modelLogo->getUploadPath();
                $logo = APP_PATH . "public/upload/{$uploadPath}/{$item['logo']}";
                // die($logo);
                $ret = $weixin->getCardManager()->uploadLogoUrl($logo);
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