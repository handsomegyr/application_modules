<?php

namespace App\Backend\Submodules\Bargain\Controllers;

use App\Backend\Submodules\Bargain\Models\Log;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="砍价日志"})
 *
 * @name 砍价日志
 */
class LogController extends \App\Backend\Controllers\FormController
{

    private $modelLog;

    private $modelActivity;

    public function initialize()
    {
        $this->modelLog = new Log();
        $this->modelActivity = new Activity();
        $this->activityList = $this->modelActivity->getAll();
        parent::initialize();
    }

    private $activityList = null;

    protected function getSchemas2($schemas)
    {
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

        $schemas['user_id'] = array(
            'name' => '用户ID',
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
        $schemas['user_name'] = array(
            'name' => '用户昵称',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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
        $schemas['user_headimgurl'] = array(
            'name' => '用户头像',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['client_ip'] = array(
            'name' => 'IP',
            'data' => array(
                'type' => 'string',
                'length' => 15,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'ip',
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
        $schemas['bargain_id'] = array(
            'name' => '砍价物ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
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
        $schemas['bargain_time'] = array(
            'name' => '砍价时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($now)
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
        $schemas['bargain_num'] = array(
            'name' => '砍价次数',
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
        $schemas['bargain_amount'] = array(
            'name' => '砍价金额(分)',
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
        $schemas['is_system_bargain'] = array(
            'name' => '是否系统砍',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
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

        return $schemas;
    }

    protected function getName()
    {
        return '砍价日志';
    }

    protected function getModel()
    {
        return $this->modelLog;
    }
}
