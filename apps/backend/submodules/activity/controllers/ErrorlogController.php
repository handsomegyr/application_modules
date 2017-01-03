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
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
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
                'items' => $this->modelActivity->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'activity_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelActivity->getAll()
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
                'is_show' => false
            )
        );
        
        $schemas['error_message'] = array(
            'name' => '错误内容',
            'data' => array(
                'type' => 'string',
                'length' => '200'
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

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $activityList = $this->modelActivity->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = isset($activityList[$item['activity_id']]) ? $activityList[$item['activity_id']] : "--";
        }
        return $list;
    }
}