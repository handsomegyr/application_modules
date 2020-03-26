<?php

namespace App\Backend\Submodules\Vote\Controllers;

use App\Backend\Submodules\Vote\Models\Limit;
use App\Backend\Submodules\Vote\Models\Subject;
use App\Backend\Submodules\Vote\Models\Item;
use App\Backend\Submodules\Vote\Models\LimitCategory;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="投票限制管理"})
 *
 * @name 投票限制管理
 */
class LimitController extends \App\Backend\Controllers\FormController
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

        $this->categoryList = $this->modelLimitCategory->getAll();
        $this->subjectList = $this->modelSubject->getAll();
        $this->activityList = $this->modelActivity->getAll();
        $this->itemList = $this->modelItem->getAll();

        parent::initialize();
    }

    private $categoryList = null;
    private $subjectList = null;
    private $activityList = null;
    private $itemList = null;

    protected function getSchemas2($schemas)
    {
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);

        $schemas['category'] = array(
            'name' => '限制类别',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['start_time'] = array(
            'name' => '开始时间',
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
        $schemas['end_time'] = array(
            'name' => '结束时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 2 - 1)
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
        $schemas['limit_count'] = array(
            'name' => '限制次数',
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
        $schemas['activity'] = array(
            'name' => '活动',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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
            'name' => '主题',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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
                'required' => false
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
}
