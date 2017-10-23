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
        
        $schemas['type'] = array(
            'name' => '监控类型',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
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
        
        $schemas['start_time'] = array(
            'name' => '开始时间',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        
        $schemas['end_time'] = array(
            'name' => '截止时间',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        
        $schemas['execute_time'] = array(
            'name' => '执行计算',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        
        $schemas['who'] = array(
            'name' => 'who',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 30
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

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $appliactionList = $this->modelApplication->getAll();
        foreach ($list['data'] as &$item) {
            $item['app_name'] = isset($appliactionList[$item['app_id']]) ? $appliactionList[$item['app_id']] : "--";
        }
        return $list;
    }
}