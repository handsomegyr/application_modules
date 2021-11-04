<?php

namespace App\Backend\Submodules\Member\Controllers;

use App\Backend\Submodules\Member\Models\BehaviorStat;

/**
 * @title({name="会员行为统计管理"})
 *
 * @name 会员行为统计管理
 */
class BehaviorstatController extends \App\Backend\Controllers\FormController
{
    // 是否只读
    protected $readonly = false;
    private $modelBehaviorStat;
    private $actTypeOptions = array();

    public function initialize()
    {
        $this->modelBehaviorStat = new BehaviorStat();
        $this->actTypeOptions = \App\Member\Services\MemberService::getActTypeOptions();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['member_id'] = array(
            'name' => '会员ID',
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
        $schemas['mobile'] = array(
            'name' => '手机号',
            'data' => array(
                'type' => 'string',
                'length' => 20,
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
        $schemas['openid'] = array(
            'name' => '微信用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 190,
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
        $schemas['act_type'] = array(
            'name' => '行为类型',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->actTypeOptions
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->actTypeOptions
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'select',
                'items' => $this->actTypeOptions
            ),
            'export' => array(
                'is_show' => true,
                'items' => $this->actTypeOptions
            )
        );
        $schemas['total_num'] = array(
            'name' => '行为发生次数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
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
        $schemas['restricted_total_num'] = array(
            'name' => '受限制行为发生次数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
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
        $schemas['stat_time'] = array(
            'name' => '统计时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => false
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
        return '会员行为统计管理';
    }

    protected function getModel()
    {
        return $this->modelBehaviorStat;
    }
}
