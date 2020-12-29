<?php
namespace App\Backend\Submodules\Weixinredpack\Controllers;

use App\Backend\Submodules\Weixinredpack\Models\Limit;
use App\Backend\Submodules\Weixinredpack\Models\Redpack;
use App\Backend\Submodules\Weixinredpack\Models\Customer;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="红包限制管理"})
 *
 * @name 红包限制管理
 */
class LimitController extends \App\Backend\Controllers\FormController
{

    private $modelLimit;

    private $modelRedpack;

    private $modelCustomer;

    private $modelActivity;

    public function initialize()
    {
        $this->modelLimit = new Limit();
        $this->modelRedpack = new Redpack();
        $this->modelCustomer = new Customer();
        $this->modelActivity = new Activity();
        parent::initialize();
    }
    
    protected function getSchemas2($schemas)
    {        
        $schemas['activity'] = array(
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
                'items' => function () {
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
        
        $schemas['customer'] = array(
            'name' => '客户',
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
                'items' => function () {
                    return $this->modelCustomer->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'customer_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['redpack'] = array(
            'name' => '红包',
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
                'items' => function () {
                    return $this->modelRedpack->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'redpack_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['personal_got_num_limit'] = array(
            'name' => '每人获取数量限制',
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
        return '红包限制';
    }

    protected function getModel()
    {
        return $this->modelLimit;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $customerList = $this->modelCustomer->getAll();
        $redpackList = $this->modelRedpack->getAll();
        $activityList = $this->modelActivity->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = $activityList[$item['activity']];
            $item['customer_name'] = $customerList[$item['customer']];
            $item['redpack_name'] = $redpackList[$item['redpack']];
            $item['start_time'] = $this->adjustDataTime4Show($item['start_time']);
            $item['end_time'] = $this->adjustDataTime4Show($item['end_time']);
        }
        return $list;
    }
}