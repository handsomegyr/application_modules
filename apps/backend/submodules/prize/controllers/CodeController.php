<?php

namespace App\Backend\Submodules\Prize\Controllers;

use App\Backend\Submodules\Prize\Models\Code;
use App\Backend\Submodules\Prize\Models\Prize;
use App\Backend\Submodules\Activity\Models\Activity;

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

        $this->prizeList = $this->modelPrize->getAll();
        $this->activityList = $this->modelActivity->getAll();

        parent::initialize();
    }

    private $prizeList = null;
    private $activityList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['prize_id'] = array(
            'name' => '奖品ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
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
        $schemas['code'] = array(
            'name' => '虚拟卡编号',
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
        $schemas['pwd'] = array(
            'name' => '虚拟卡密码',
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

        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);

        $schemas['start_time'] = array(
            'name' => '开始有效期',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($this->now)
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
            'name' => '结束有效期',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($this->now)
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
        $schemas['is_used'] = array(
            'name' => '是否使用',
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
        $schemas['activity_id'] = array(
            'name' => '所属活动',
            'data' => array(
                'type' => 'string',
                'length' => 24,
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
}
