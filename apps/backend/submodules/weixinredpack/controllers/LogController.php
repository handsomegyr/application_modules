<?php
namespace Webcms\Backend\Controllers\WeixinRedpack;

use Webcms\Backend\Models\WeixinRedpack\GotLog;
use Webcms\Backend\Models\System\Activity;
use Webcms\Backend\Models\WeixinRedpack\Redpack;
use Webcms\Backend\Models\WeixinRedpack\Customer;

/**
 * @title({name="抽奖日志管理"})
 *
 * @name 抽奖日志管理
 */
class LogController extends \Webcms\Backend\Controllers\FormController
{

    private $modelGotLog;

    private $modelActivity;

    private $modelRedpack;

    private $modelCustomer;

    public function initialize()
    {
        $this->modelGotLog = new GotLog();
        $this->modelActivity = new Activity();
        $this->modelRedpack = new Redpack();
        $this->modelCustomer = new Customer();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['mch_billno'] = array(
            'name' => '商户订单号',
            'data' => array(
                'type' => 'string',
                'length' => '50'
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
        
        $schemas['user_id'] = array(
            'name' => '红包用户ID',
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
        
        $schemas['re_openid'] = array(
            'name' => '用户openid',
            'data' => array(
                'type' => 'string',
                'length' => '50'
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
        
        $schemas['re_nickname'] = array(
            'name' => '微信昵称',
            'data' => array(
                'type' => 'string',
                'length' => '50'
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
        
        $schemas['re_headimgurl'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => '300'
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
        
        $schemas['client_ip'] = array(
            'name' => 'IP地址',
            'data' => array(
                'type' => 'string',
                'length' => '15'
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
        
        $schemas['total_num'] = array(
            'name' => '数量',
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
        
        $schemas['total_num'] = array(
            'name' => '金额(分)',
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
        
        $schemas['got_time'] = array(
            'name' => '开始时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
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
        
        $schemas['isOK'] = array(
            'name' => '是否OK',
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
                'list_type' => 1,
                'ajax' => 'toggleisshow'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['error_logs'] = array(
            'name' => '错误日志记录',
            'data' => array(
                'type' => 'json',
                'length' => 1000
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['memo'] = array(
            'name' => '备注信息',
            'data' => array(
                'type' => 'json',
                'length' => 1000
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
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
        return $this->modelGotLog;
    }

    protected function getList4Show(\Webcms\Backend\Models\Input $input, array $list)
    {
        $activityList = $this->modelActivity->getAll();
        $sourceList = $this->modelSource->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = $activityList[$item['activity']];
            $item['source_name'] = $sourceList[$item['source']];
        }
        return $list;
    }
}