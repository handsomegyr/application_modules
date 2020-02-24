<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Logo;

/**
 * @title({name="商户LOGO"})
 *
 * @name 商户LOGO
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
            'name' => '卡券的商户logo图片文件',
            'data' => array(
                'type' => 'file',
                'length' => 128,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelLogo->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['logo_url'] = array(
            'name' => '卡券的商户logo的url',
            'data' => array(
                'type' => 'string',
                'length' => 128,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['is_uploaded'] = array(
            'name' => '是否已上传',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '商户LOGO';
    }

    protected function getModel()
    {
        return $this->modelLogo;
    }

    /**
     * @title({name="上传商户logo"})
     * 上传卡券的商户logo的Hook
     *
     * @name 上传商户logo
     */
    public function uploadAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/logo/upload
        try {
            $this->view->disable();
            $weixin = $this->getWeixin();

            $logoList = $this->modelLogo->getAll();
            foreach ($logoList as $item) {
                if (!empty($item['is_uploaded'])) {
                    continue;
                }
                $uploadPath = $this->modelLogo->getUploadPath();
                $logo = APP_PATH . "public/upload/{$uploadPath}/{$item['logo']}";
                // die($logo);
                $ret = $weixin->getCardManager()->uploadLogoUrl($logo);
                if (!empty($ret['errcode'])) {
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
