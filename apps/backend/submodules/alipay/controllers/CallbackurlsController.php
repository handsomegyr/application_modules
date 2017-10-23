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
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
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
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelApplication->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'app_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelApplication->getAll()
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['url'] = array(
            'name' => '回调地址安全域名',
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
        
        $schemas['is_valid'] = array(
            'name' => '是否有效',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => '0',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
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

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $appliactionList = $this->modelApplication->getAll();
        foreach ($list['data'] as &$item) {
            $item['app_name'] = isset($appliactionList[$item['app_id']]) ? $appliactionList[$item['app_id']] : "--";
        }
        return $list;
    }
}