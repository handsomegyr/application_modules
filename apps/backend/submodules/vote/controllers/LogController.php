<?php

namespace App\Backend\Submodules\Vote\Controllers;

use App\Backend\Submodules\Vote\Models\Log;
use App\Backend\Submodules\Vote\Models\Subject;
use App\Backend\Submodules\Vote\Models\Item;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="投票日志管理"})
 *
 * @name 投票日志管理
 */
class LogController extends \App\Backend\Controllers\FormController
{

    private $modelLog;

    private $modelSubject;

    private $modelItem;

    private $modelActivity;

    public function initialize()
    {
        $this->modelLog = new Log();
        $this->modelSubject = new Subject();
        $this->modelItem = new Item();
        $this->modelActivity = new Activity();

        $this->subjectList = $this->modelSubject->getAll();
        $this->activityList = $this->modelActivity->getAll();
        $this->itemList = $this->modelItem->getAll();

        parent::initialize();
    }

    private $subjectList = null;
    private $activityList = null;
    private $itemList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['activity'] = array(
            'name' => '所属活动',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => ''
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
        $schemas['subject'] = array(
            'name' => '投票主题',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->subjectList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->subjectList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->subjectList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['item'] = array(
            'name' => '选项',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->itemList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->itemList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->itemList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['vote_time'] = array(
            'name' => '投票时间',
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
        $schemas['identity'] = array(
            'name' => '投票凭证',
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
        $schemas['ip'] = array(
            'name' => 'IP',
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
        $schemas['session_id'] = array(
            'name' => '会话ID',
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
        $schemas['vote_num'] = array(
            'name' => '投票次数',
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
        $schemas['view_num'] = array(
            'name' => '浏览次数',
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
        $schemas['share_num'] = array(
            'name' => '分享次数',
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
        return '投票日志';
    }

    protected function getModel()
    {
        return $this->modelLog;
    }
}
