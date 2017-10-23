<?php
namespace App\Backend\Submodules\Alipay\Controllers;

use App\Backend\Submodules\Alipay\Models\Application;

/**
 * @title({name="支付宝应用管理"})
 *
 * @name 支付宝应用管理
 */
class ApplicationController extends \App\Backend\Controllers\FormController
{

    private $modelApplication;

    public function initialize()
    {
        $this->modelApplication = new Application();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;
        
        $schemas['app_id'] = array(
            'name' => '应用ID',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 32
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
                'input_type' => 'text',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['app_name'] = array(
            'name' => '应用名称',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 50
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['merchant_private_key'] = array(
            'name' => '商户私钥',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 4096
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['merchant_public_key'] = array(
            'name' => '商户应用公钥',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 4096
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['alipay_public_key'] = array(
            'name' => '支付宝公钥',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 4096
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['charset'] = array(
            'name' => '编码格式',
            'data' => array(
                'type' => 'string',
                'defaultValue' => 'utf-8',
                'length' => 10
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['gatewayUrl'] = array(
            'name' => '支付宝网关',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 100
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['sign_type'] = array(
            'name' => '签名方式',
            'data' => array(
                'type' => 'string',
                'defaultValue' => 'RSA2',
                'length' => 10
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['secretKey'] = array(
            'name' => '签名密钥',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 50
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '支付宝应用';
    }

    protected function getModel()
    {
        return $this->modelApplication;
    }
}