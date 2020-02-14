<?php

namespace App\Backend\Submodules\Cronjob\Controllers;

use App\Backend\Submodules\Cronjob\Models\Job;

/**
 * @title({name="计划任务管理"})
 *
 * @name 计划任务管理
 */
class JobController extends \App\Backend\Controllers\FormController
{

    private $modelJob;

    public function initialize()
    {
        $this->modelJob = new Job();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '任务名称',
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
                'input_type' => 'text',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['desc'] = array(
            'name' => '任务功能描述',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 100
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

        $schemas['start_time'] = array(
            'name' => '执行开始时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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

        $schemas['end_time'] = array(
            'name' => '任务结束时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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

        $schemas['cmd'] = array(
            'name' => '任务命令',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['cycle'] = array(
            'name' => '执行周期(分钟)',
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
                'is_show' => true
            )
        );

        $schemas['cron'] = array(
            'name' => 'cron语法',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 20
            ),
            'validation' => array(
                'required' => false
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

        $schemas['last_execute_time'] = array(
            'name' => '最后一次执行时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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

        $schemas['last_execute_result'] = array(
            'name' => '最后一次执行结果',
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
                'is_show' => false
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '计划任务';
    }

    protected function getModel()
    {
        return $this->modelJob;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        //$activityList = $this->modelActivity->getAll();
        foreach ($list['data'] as &$item) {
            //$item['activity_name'] = isset($activityList[$item['activity_id']]) ? $activityList[$item['activity_id']] : '--';
            $item['start_time'] = date("Y-m-d H:i:s", $item['start_time']->sec);
            $item['end_time'] = date("Y-m-d H:i:s", $item['end_time']->sec);
            if (!empty($item['last_execute_time'])) {
                $item['last_execute_time'] = date("Y-m-d H:i:s", $item['last_execute_time']->sec);
            }
        }
        return $list;
    }
}
