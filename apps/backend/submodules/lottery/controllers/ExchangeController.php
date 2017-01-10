<?php
namespace App\Backend\Submodules\Lottery\Controllers;

use App\Backend\Submodules\Lottery\Models\Exchange;
use App\Backend\Submodules\Prize\Models\Prize;
use App\Backend\Submodules\Prize\Models\Category;
use App\Backend\Submodules\Activity\Models\Activity;
use App\Backend\Submodules\System\Models\Source;

/**
 * @title({name="抽奖中奖管理"})
 *
 * @name 抽奖中奖管理
 */
class ExchangeController extends \App\Backend\Controllers\FormController
{

    private $modelExchange;

    private $modelPrize;

    private $modelPrizeCategory;

    private $modelActivity;

    private $modelSource;

    public function initialize()
    {
        $this->modelExchange = new Exchange();
        $this->modelPrize = new Prize();
        $this->modelActivity = new Activity();
        $this->modelSource = new Source();
        $this->modelPrizeCategory = new Category();
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
        
        $schemas['user_id'] = array(
            'name' => '用户ID',
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
        
        $schemas['user_name'] = array(
            'name' => '用户名',
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
        $schemas['user_headimgurl'] = array(
            'name' => '用户头像',
            'data' => array(
                'type' => 'string',
                'length' => 300
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['prize_id'] = array(
            'name' => '奖品ID',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelPrize->getAll()
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['is_valid'] = array(
            'name' => '是否有效',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
            ),
            'validation' => array(
                'required' => 1
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
        $schemas['got_time'] = array(
            'name' => '获取时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'datetimepicker',
                'is_show' => false,
                'condition_type' => 'period' // single
            )
        );
        $schemas['source'] = array(
            'name' => '来源',
            'data' => array(
                'type' => 'string',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
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
        $schemas['prize_code'] = array(
            'name' => '奖品代号',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['prize_name'] = array(
            'name' => '奖品名',
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
        $schemas['prize_category'] = array(
            'name' => '奖品类别',
            'data' => array(
                'type' => 'integer',
                'length' => 5
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelPrizeCategory->getAll()
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['prize_virtual_currency'] = array(
            'name' => '奖品虚拟价值',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['prize_is_virtual'] = array(
            'name' => '是否虚拟奖',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['prize_virtual_code'] = array(
            'name' => '卡号',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        $schemas['prize_virtual_pwd'] = array(
            'name' => '卡密',
            'data' => array(
                'type' => 'string',
                'length' => 30
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['contact_name'] = array(
            'name' => '联系用户',
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
        $schemas['contact_mobile'] = array(
            'name' => '联系手机',
            'data' => array(
                'type' => 'string',
                'length' => 20
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
        $schemas['contact_address'] = array(
            'name' => '联系地址',
            'data' => array(
                'type' => 'string',
                'length' => 200
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
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => '1000'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => true
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '抽奖中奖';
    }

    protected function getModel()
    {
        return $this->modelExchange;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $prizeList = $this->modelPrize->getAll();
        $activityList = $this->modelActivity->getAll();
        $sourceList = $this->modelSource->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = $activityList[$item['activity_id']];
            // $item['prize_name'] = $prizeList[$item['prize_id']];
            // if (! empty($item['prize_info'])) {
            // $item['prize_name'] = $item['prize_info']['prize_name'];
            // }
            $item['source_name'] = $sourceList[$item['source']];
            // $item['identity_name'] = "";
            // if ($item['source'] == "weixin") {
            // if (! empty($item['identity_info'])) {
            // $item['identity_name'] = $item['identity_info']['nickname'];
            // }
            // }
            // $item['prize_code_info'] = "";
            // if (! empty($item['prize_code'])) {
            // $item['prize_code_info'] = $item['prize_code']['code'] . "<br/>" . $item['prize_code']['pwd'];
            // }
            
            // $item['identity_contact_info'] = array();
            // if (isset($item['identity_contact']['name'])) {
            // $item['identity_contact_info'][] = $item['identity_contact']['name'];
            // }
            // if (isset($item['identity_contact']['mobile'])) {
            // $item['identity_contact_info'][] = $item['identity_contact']['mobile'];
            // }
            // if (isset($item['identity_contact']['address'])) {
            // $item['identity_contact_info'][] = $item['identity_contact']['address'];
            // }
            // $item['identity_contact_info'] = implode('<br/>', $item['identity_contact_info']);
            $item['got_time'] = date('Y-m-d H:i:s', $item['got_time']->sec);
            if (! empty($item['memo'])) {
                $item['memo'] = json_encode($item['memo']);
            }
        }
        return $list;
    }
}