<?php
namespace App\Backend\Controllers\Prize;

use App\Backend\Models\Prize\Code;
use App\Backend\Models\Prize\Prize;
use App\Backend\Models\System\Activity;

/**
 * @title({name="奖品券码管理"})
 *
 * @name 奖品券码管理
 */
class CodeController extends \App\Backend\Controllers\FormController
{

    private $modelCode;

    private $modelPrize;

    private $modelActivity;

    public function initialize()
    {
        $this->modelCode = new Code();
        $this->modelPrize = new Prize();
        $this->modelActivity = new Activity();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['prize_id'] = array(
            'name' => '奖品名称',
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
        
        $schemas['code'] = array(
            'name' => '券码',
            'data' => array(
                'type' => 'string',
                'length' => '10'
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
        
        $schemas['pwd'] = array(
            'name' => '券密码',
            'data' => array(
                'type' => 'string',
                'length' => '10'
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
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['start_time'] = array(
            'name' => '开始有效期',
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
            'name' => '截止有效期',
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
        
        $schemas['is_used'] = array(
            'name' => '是否使用',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => 1
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
                'required' => false
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '奖品券码';
    }

    protected function getModel()
    {
        return $this->modelCode;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $prizeList = $this->modelPrize->getAll();
        $activityList = $this->modelActivity->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = isset($activityList[$item['activity_id']]) ? $activityList[$item['activity_id']] : "--";
            $item['prize_name'] = isset($prizeList[$item['prize_id']]) ? $prizeList[$item['prize_id']] : "--";
            $item['start_time'] = date("Y-m-d H:i:s", $item['start_time']->sec);
            $item['end_time'] = date("Y-m-d H:i:s", $item['end_time']->sec);
        }
        
        return $list;
    }
}