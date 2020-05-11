<?php

namespace App\Backend\Submodules\Cronjob\Controllers;

use App\Backend\Submodules\Cronjob\Models\Log;
use App\Backend\Submodules\Cronjob\Models\Job;

/**
 * @title({name="计划任务日志管理"})
 *
 * @name 计划任务日志管理
 */
class LogController extends \App\Backend\Controllers\FormController
{

    private $modelLog;
    private $modelJob;

    public function initialize()
    {
        $this->modelLog = new Log();
        $this->modelJob = new Job();
        $this->jobList = $this->modelJob->getAll();

        parent::initialize();
    }

    private $jobList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['job_name'] = array(
            'name' => '计划任务',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->jobList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->jobList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->jobList
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['execute_result'] = array(
            'name' => '执行结果',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 1024
            ),
            'validation' => array(
                'required' => false
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

        $schemas['script_execute_time'] = array(
            'name' => '脚本执行时间',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
                'length' => 11
            ),
            'validation' => array(
                'required' => false
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
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '计划任务日志';
    }

    protected function getModel()
    {
        return $this->modelLog;
    }
}
