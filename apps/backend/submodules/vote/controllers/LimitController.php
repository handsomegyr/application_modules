<?php
namespace Webcms\Backend\Controllers\Vote;

use Webcms\Backend\Models\Vote\Limit;
use Webcms\Backend\Models\Vote\Subject;
use Webcms\Backend\Models\Vote\Item;
use Webcms\Backend\Models\Vote\LimitCategory;
use Webcms\Backend\Models\System\Activity;

/**
 * @title({name="投票限制管理"})
 *
 * @name 投票限制管理
 */
class LimitController extends \Webcms\Backend\Controllers\FormController
{

    private $modelLimit;

    private $modelSubject;

    private $modelItem;

    private $modelActivity;

    private $modelLimitCategory;

    public function initialize()
    {
        $this->modelLimitCategory = new LimitCategory();
        $this->modelLimit = new Limit();
        $this->modelSubject = new Subject();
        $this->modelItem = new Item();
        $this->modelActivity = new Activity();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['category'] = array(
            'name' => '投票限制类型',
            'data' => array(
                'type' => 'integer',
                'length' => '1'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelLimitCategory->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'category_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['limit_count'] = array(
            'name' => '次数限制',
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
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['start_time'] = array(
            'name' => '开始时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
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
        
        $schemas['end_time'] = array(
            'name' => '截止时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 2 - 1)
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '投票限制';
    }

    protected function getModel()
    {
        return $this->modelLimit;
    }

    protected function getList4Show(\Webcms\Backend\Models\Input $input, array $list)
    {
        $categoryList = $this->modelLimitCategory->getAll();
        $subjectList = $this->modelSubject->getAll();
        $activityList = $this->modelActivity->getAll();
        $itemList = $this->modelItem->getAll();
        foreach ($list['data'] as &$item) {
            $item['category_name'] = isset($categoryList[$item['category']]) ? $categoryList[$item['category']] : "--";
            $item['activity_name'] = $activityList[$item['activity']];
            $item['subject_name'] = $subjectList[$item['subject']];
            $item['item_name'] = $itemList[$item['item']];
            $item['start_time'] = date("Y-m-d H:i:s", $item['start_time']->sec);
            $item['end_time'] = date("Y-m-d H:i:s", $item['end_time']->sec);
        }
        return $list;
    }
}