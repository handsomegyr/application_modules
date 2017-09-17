<?php
namespace App\Backend\Submodules\Sign\Controllers;

use App\Backend\Submodules\Sign\Models\Log;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="日志管理"})
 *
 * @name 日志管理
 */
class LogController extends \App\Backend\Controllers\FormController
{

    private $modelLog;

    private $modelActivity;

    public function initialize()
    {
        $this->modelLog = new Log();
        $this->modelActivity = new Activity();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['activity_id'] = array(
            'name' => '所属活动',
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
                'items' => function () {
                    return $this->modelActivity->getAll();
                }
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
        
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
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
                'input_type' => 'text',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['nickname'] = array(
            'name' => '用户昵称',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 50
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
                'input_type' => 'text',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['headimgurl'] = array(
            'name' => '用户头像',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 300
            ),
            'validation' => array(
                'required' => false
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
                'is_show' => false
            )
        );
        
        $schemas['sign_time'] = array(
            'name' => '签到时间',
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
                'input_type' => 'datetimepicker',
                'condition_type' => 'period',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['ip'] = array(
            'name' => 'IP',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 15
            ),
            'validation' => array(
                'required' => false
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
                'is_show' => false
            )
        );
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'text',
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
        return $schemas;
    }

    protected function getName()
    {
        return '日志';
    }

    protected function getModel()
    {
        return $this->modelLog;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $activityList = $this->modelActivity->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = isset($activityList[$item['activity_id']]) ? $activityList[$item['activity_id']] : '--';
            $item['sign_time'] = date("Y-m-d H:i:s", $item['sign_time']->sec);
        }
        return $list;
    }
}