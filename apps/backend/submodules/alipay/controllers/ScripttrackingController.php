<?php

namespace App\Backend\Submodules\Alipay\Controllers;

use App\Backend\Submodules\Alipay\Models\Application;
use App\Backend\Submodules\Alipay\Models\ScriptTracking;

/**
 * @title({name="支付宝执行时间跟踪统计管理"})
 *
 * @name 支付宝执行时间跟踪统计管理
 */
class ScripttrackingController extends \App\Backend\Controllers\FormController
{

    private $modelApplication;

    private $modelScriptTracking;

    public function initialize()
    {
        $this->modelApplication = new Application();
        $this->modelScriptTracking = new ScriptTracking();
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
        $schemas['type'] = array(
            'name' => '监控类型',
            'data' => array(
                'type' => 'string',
                'length' => 255,
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
        $schemas['start_time'] = array(
            'name' => '开始时间',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['end_time'] = array(
            'name' => '截止时间',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['execute_time'] = array(
            'name' => '执行计算',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['who'] = array(
            'name' => 'who',
            'data' => array(
                'type' => 'string',
                'length' => 255,
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

        return $schemas;
    }

    protected function getName()
    {
        return '支付宝执行时间跟踪统计';
    }

    protected function getModel()
    {
        return $this->modelScriptTracking;
    }
}
