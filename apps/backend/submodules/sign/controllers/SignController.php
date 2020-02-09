<?php
namespace App\Backend\Submodules\Sign\Controllers;

use App\Backend\Submodules\Sign\Models\Sign;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="签到管理"})
 *
 * @name 签到管理
 */
class SignController extends \App\Backend\Controllers\FormController
{

    private $modelSign;

    private $modelActivity;

    public function initialize()
    {
        $this->modelSign = new Sign();
        $this->modelActivity = new Activity();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['activity_id'] = array(
            'name' => '所属活动',
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
        
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 50
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
                'input_type' => 'text',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['nickname'] = array(
            'name' => '用户昵称',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 50
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'text',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['headimgurl'] = array(
            'name' => '用户头像',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 300
            ),
            'validation' => array(
                'required' => false
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['first_sign_time'] = array(
            'name' => '首次签到时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
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
                'input_type' => 'datetimepicker',
                'condition_type' => 'period',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['restart_sign_time'] = array(
            'name' => '重新开始签到时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
            ),
            'validation' => array(
                'required' => false
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['last_sign_time'] = array(
            'name' => '最终签到时间',
            'data' => array(
                'type' => 'datetime',
                'defaultValue' => getCurrentTime(),
                'length' => 19
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
                'input_type' => 'datetimepicker',
                'condition_type' => 'period',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['continue_sign_count'] = array(
            'name' => '连续签到次数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
                'length' => 11
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'number',
                'condition_type' => 'period',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['is_continue_sign'] = array(
            'name' => '是否连续签到',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => '0',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['total_sign_count'] = array(
            'name' => '总签到次数(同天累加)',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
                'length' => 11
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
                'input_type' => 'number',
                'condition_type' => 'period',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['insameperiod_sign_count'] = array(
            'name' => '同天签到次数',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
                'length' => 11
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
                'input_type' => 'number',
                'condition_type' => 'period',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['is_do'] = array(
            'name' => '是否完成签到',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => '0',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['total_sign_count2'] = array(
            'name' => '总签到次数(同天不累加)',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => '0',
                'length' => 11
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
                'input_type' => 'number',
                'condition_type' => 'period',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['lastip'] = array(
            'name' => 'IP',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 15
            ),
            'validation' => array(
                'required' => false
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['valid_log_id'] = array(
            'name' => '签到日志记录ID',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
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
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
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
        return '签到';
    }

    protected function getModel()
    {
        return $this->modelSign;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $activityList = $this->modelActivity->getAll();
        foreach ($list['data'] as &$item) {
            $item['activity_name'] = isset($activityList[$item['activity_id']]) ? $activityList[$item['activity_id']] : '--';
            if (! empty($item['first_sign_time'])) {
                $item['first_sign_time'] = date("Y-m-d H:i:s", $item['first_sign_time']->sec);
            }
            if (! empty($item['restart_sign_time'])) {
                $item['restart_sign_time'] = date("Y-m-d H:i:s", $item['restart_sign_time']->sec);
            }
            if (! empty($item['last_sign_time'])) {
                $item['last_sign_time'] = date("Y-m-d H:i:s", $item['last_sign_time']->sec);
            }
        }
        return $list;
    }
}