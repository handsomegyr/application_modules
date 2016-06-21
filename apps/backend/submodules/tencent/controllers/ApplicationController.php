<?php
namespace App\Backend\Submodules\Tencent\Controllers;

use App\Backend\Submodules\Tencent\Models\AppKey;
use App\Backend\Submodules\Tencent\Models\Application;

/**
 * @title({name="腾讯-应用设置管理"})
 *
 * @name 腾讯-应用设置管理
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
        $schemas['name'] = array(
            'name' => '应用名',
            'data' => array(
                'type' => 'string',
                'length' => 30
            ),
            'validation' => array(
                'required' => 1
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
        
        $schemas['akey'] = array(
            'name' => 'AKEY',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
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
        
        $schemas['skey'] = array(
            'name' => 'SKEY',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
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
        
        $schemas['secretKey'] = array(
            'name' => '秘钥',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
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
        return '腾讯-应用设置';
    }

    protected function getModel()
    {
        return $this->modelApplication;
    }
}