<?php
namespace App\Backend\Submodules\Lottery\Controllers;

use App\Backend\Submodules\Lottery\Models\Limit;
use App\Backend\Submodules\Prize\Models\Prize;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="抽奖限制管理"})
 *
 * @name 抽奖限制管理
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
        
        $schemas['prize_id'] = array(
            'name' => '奖品名称（为空表示限制整个活动）',
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
                    return $this->modelPrize->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'prize_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['limit'] = array(
            'name' => '中奖次数限制',
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '抽奖限制';
    }

    protected function getModel()
    {
        return $this->modelLimit;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $prizeList = $this->modelPrize->getAll();
        $activityList = $this->modelActivity->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = $activityList[$item['activity_id']];
            $item['prize_name'] = $prizeList[$item['prize_id']];
            $item['start_time'] = date("Y-m-d H:i:s", $item['start_time']->sec);
            $item['end_time'] = date("Y-m-d H:i:s", $item['end_time']->sec);
        }
        return $list;
    }
}