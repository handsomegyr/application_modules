<?php
namespace App\Backend\Submodules\Vote\Controllers;

use App\Backend\Submodules\Vote\Models\Log;
use App\Backend\Submodules\Vote\Models\Subject;
use App\Backend\Submodules\Vote\Models\Item;
use App\Backend\Submodules\System\Models\Activity;

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
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['activity'] = array(
            'name' => '活动',
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
                'items' => function ()
                {
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
        
        $schemas['subject'] = array(
            'name' => '投票主题',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelSubject->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'subject_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['item'] = array(
            'name' => '投票选项',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelItem->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'item_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['vote_num'] = array(
            'name' => '投票次数',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['vote_time'] = array(
            'name' => '投票时间',
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
                'is_show' => false
            )
        );
        
        $schemas['identity'] = array(
            'name' => '身份',
            'data' => array(
                'type' => 'string',
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
                'is_show' => false
            )
        );
        
        $schemas['ip'] = array(
            'name' => 'IP',
            'data' => array(
                'type' => 'string',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['session_id'] = array(
            'name' => '会话ID',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
            )
        );
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1000
            ),
            'validation' => array(
                'required' => 1
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

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $subjectList = $this->modelSubject->getAll();
        $activityList = $this->modelActivity->getAll();
        $itemList = $this->modelItem->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = $activityList[$item['activity']];
            $item['subject_name'] = $subjectList[$item['subject']];
            $item['item_name'] = $itemList[$item['item']];
            $item['vote_time'] = date("Y-m-d H:i:s", $item['vote_time']->sec);
        }
        return $list;
    }
}