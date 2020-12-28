<?php

namespace App\Backend\Submodules\Exchange\Controllers;

use App\Backend\Submodules\Exchange\Models\Limit;
use App\Backend\Submodules\Prize\Models\Prize;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="兑换限制"})
 *
 * @name 兑换限制
 */
class LimitController extends \App\Backend\Controllers\FormController
{

    private $modelLimit;

    private $modelPrize;

    private $modelActivity;

    public function initialize()
    {
        $this->modelLimit = new Limit();
        $this->modelPrize = new Prize();
        $this->modelActivity = new Activity();

        $this->prizeList = $this->modelPrize->getAll();
        $this->activityList = $this->modelActivity->getAll();

        parent::initialize();
    }

    private $prizeList = null;
    private $activityList = null;

    protected function getSchemas2($schemas)
    {
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);

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
        $schemas['prize_id'] = array(
            'name' => '奖品ID',
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
                'items' => $this->prizeList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->prizeList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->prizeList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['limit'] = array(
            'name' => '限制数量',
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
        $schemas['start_time'] = array(
            'name' => '限制开始时间',
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
            'name' => '限制结束时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($now + 3600 * 24)
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
        return $schemas;
    }

    protected function getName()
    {
        return '兑换限制';
    }

    protected function getModel()
    {
        return $this->modelLimit;
    }
    
}
