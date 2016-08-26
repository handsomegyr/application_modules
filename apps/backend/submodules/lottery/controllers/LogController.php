<?php
namespace App\Backend\Submodules\Lottery\Controllers;

use App\Backend\Submodules\Lottery\Models\Record;
use App\Backend\Submodules\Activity\Models\Activity;
use App\Backend\Submodules\System\Models\Source;

/**
 * @title({name="抽奖日志管理"})
 *
 * @name 抽奖日志管理
 */
class LogController extends \App\Backend\Controllers\FormController
{

    private $modelRecord;

    private $modelActivity;

    private $modelSource;

    public function initialize()
    {
        $this->modelRecord = new Record();
        $this->modelActivity = new Activity();
        $this->modelSource = new Source();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['result_id'] = array(
            'name' => '抽奖结果',
            'data' => array(
                'type' => 'integer',
                'length' => 6
            ),
            'validation' => array(
                'required' => 1
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
        $schemas['result_msg'] = array(
            'name' => '结果说明',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
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
        
        $schemas['source'] = array(
            'name' => '访问来源',
            'data' => array(
                'type' => 'string',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelSource->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'source_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => '24'
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
        return '抽奖日志';
    }

    protected function getModel()
    {
        return $this->modelRecord;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $activityList = $this->modelActivity->getAll();
        $sourceList = $this->modelSource->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = $activityList[$item['activity_id']];
            $item['source_name'] = $sourceList[$item['source']];
        }
        return $list;
    }
}