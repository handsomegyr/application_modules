<?php

namespace App\Backend\Submodules\Alipay\Controllers;

use App\Backend\Submodules\Alipay\Models\Application;
use App\Backend\Submodules\Alipay\Models\Callbackurls;

/**
 * @title({name="支付宝回调地址安全域名管理"})
 *
 * @name 支付宝回调地址安全域名管理
 */
class CallbackurlsController extends \App\Backend\Controllers\FormController
{

    private $modelApplication;

    private $modelCallbackurls;

    public function initialize()
    {
        $this->modelApplication = new Application();
        $this->modelCallbackurls = new Callbackurls();
        $this->appliactionList = $this->modelApplication->getAll();
        parent::initialize();
    }
    private $appliactionList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['app_id'] = array(
            'name' => '应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 32,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->appliactionList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->appliactionList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->appliactionList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['url'] = array(
            'name' => '回调地址安全域名',
            'data' => array(
                'type' => 'string',
                'length' => 100,
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
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['is_valid'] = array(
            'name' => '是否有效',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => true
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
        return '支付宝回调地址安全域名';
    }

    protected function getModel()
    {
        return $this->modelCallbackurls;
    }
}
