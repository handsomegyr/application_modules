<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\Callbackurls;

/**
 * @title({name="微信回调地址安全域名管理"})
 *
 * @name 微信回调地址安全域名管理
 */
class CallbackurlsController extends \App\Backend\Controllers\FormController
{

    private $modelCallbackurls;

    public function initialize()
    {
        $this->modelCallbackurls = new Callbackurls();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['url'] = array(
            'name' => '回调地址安全域名',
            'data' => array(
                'type' => 'string',
                'length' => '100'
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
        
        $schemas['is_valid'] = array(
            'name' => '是否有效',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => true
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '微信回调地址安全域名';
    }

    protected function getModel()
    {
        return $this->modelCallbackurls;
    }
}