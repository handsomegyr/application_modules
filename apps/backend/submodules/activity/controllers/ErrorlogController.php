<?php

namespace App\Backend\Submodules\Activity\Controllers;

use App\Backend\Submodules\Activity\Models\ErrorLog;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="活动错误信息管理"})
 *
 * @name 活动错误信息管理
 */
class ErrorlogController extends \App\Backend\Controllers\FormController
{

    private $modelActivity;

    private $modelErrorLog;

    public function initialize()
    {
        $this->modelErrorLog = new ErrorLog();
        $this->modelActivity = new Activity();
        $this->activityList = $this->modelActivity->getAll();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['clearlog'] = array(
            'title' => '清空日志',
            'action' => 'clearlog',
            // 'is_show' => true,
            'is_show' => function () {
                return true;
            },
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    private $activityList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['activity_id'] = array(
            'name' => '活动名称',
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
                'items' => $this->activityList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->activityList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->activityList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['error_code'] = array(
            'name' => '错误代号',
            'data' => array(
                'type' => 'string',
                'length' => '30'
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
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['error_message'] = array(
            'name' => '错误内容',
            'data' => array(
                'type' => 'string',
                'length' => '1024'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
            ),
            'list' => array(
                'is_show' => true,
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    $column->style('width:70%;word-break:break-all;');
                }
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['happen_time'] = array(
            'name' => '发生时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
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
        return '活动错误信息';
    }

    protected function getModel()
    {
        return $this->modelErrorLog;
    }


    /**
     * @title({name="清空日志"})
     * 清空日志
     *
     * @name 清空日志
     */
    public function clearlogAction()
    {
        // http://www.myapplicationmodule.com/admin/activity/errorlog/clearlog
        try {
            $this->modelErrorLog->truncate();
            // $connection = $this->modelErrorLog->getDb();
            // $connection->query("TRUNCATE `activity_errorlog`", array());
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功清空数据');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
