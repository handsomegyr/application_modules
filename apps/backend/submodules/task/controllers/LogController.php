<?php

namespace App\Backend\Submodules\Task\Controllers;

use App\Backend\Submodules\Task\Models\Log;

/**
 * @title({name="任务日志管理"})
 *
 * @name 任务日志管理
 */
class LogController extends \App\Backend\Controllers\FormController
{

    private $modelLog;

    public function initialize()
    {
        $this->modelLog = new Log();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['task'] = array(
            'name' => '任务名',
            'data' => array(
                'type' => 'string',
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
                'is_show' => true
            )
        );
        $schemas['is_success'] = array(
            'name' => '是否成功',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
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
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['request'] = array(
            'name' => '请求参数',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
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
        $schemas['result'] = array(
            'name' => '获得结果',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
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
        $schemas['log_time'] = array(
            'name' => '日志时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        return '任务日志';
    }

    protected function getModel()
    {
        return $this->modelLog;
    }
}
