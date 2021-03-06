<?php
namespace App\Backend\Submodules\Tencent\Controllers;

use App\Backend\Submodules\Tencent\Models\AppKey;

/**
 * @title({name="腾讯-应用密码管理"})
 *
 * @name 腾讯-应用密码管理
 */
class AppKeyController extends \App\Backend\Controllers\FormController
{

    private $modelAppKey;

    public function initialize()
    {
        $this->modelAppKey = new AppKey();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        
        $schemas['appName'] = array(
            'name' => '运用名称',
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '腾讯-应用密码';
    }

    protected function getModel()
    {
        return $this->modelAppKey;
    }
}