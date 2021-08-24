<?php

namespace App\Backend\Submodules\Weixinredpack\Controllers;

use App\Backend\Submodules\Weixinredpack\Models\Rule;
use App\Backend\Submodules\Weixinredpack\Models\Customer;
use App\Backend\Submodules\Weixinredpack\Models\Redpack;
use App\Backend\Submodules\Activity\Models\Activity;

/**
 * @title({name="红包发放规则管理"})
 *
 * @name 红包发放规则管理
 */
class RuleController extends \App\Backend\Controllers\FormController
{

    private $modelRule;

    private $modelRedpack;

    private $modelCustomer;

    private $modelActivity;

    public function initialize()
    {
        $this->modelRule = new Rule();
        $this->modelRedpack = new Redpack();
        $this->modelCustomer = new Customer();
        $this->modelActivity = new Activity();
        parent::initialize();
    }

    /**
     * @title({name="复制规则"})
     *
     * @name 复制规则
     */
    public function copyAction()
    {
        try {

            $input = $this->getFilterInput();

            if ($input->isValid("id")) {
            } else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            $ruleInfo = $this->modelRule->getInfoById($input->id);
            unset($ruleInfo['_id']);
            unset($ruleInfo['__CREATE_TIME__']);
            unset($ruleInfo['__MODIFY_TIME__']);
            unset($ruleInfo['__REMOVED__']);
            $this->modelRule->insert($ruleInfo);
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
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
                'items' => function () {
                    return $this->modelActivity->getAll();
                }
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
                'items' => function () {
                    return $this->modelCustomer->getAll();
                }
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
                'items' => function () {
                    return $this->modelRedpack->getAll();
                }
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

        $schemas['amount'] = array(
            'name' => '红包发放总金额(分)',
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

        $schemas['quantity'] = array(
            'name' => '红包发放总数量',
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

        $schemas['min_cash'] = array(
            'name' => '最小金额(分)',
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

        $schemas['max_cash'] = array(
            'name' => '最大金额(分)',
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

        $schemas['personal_can_get_num'] = array(
            'name' => '最大数量(人)',
            'data' => array(
                'type' => 'integer',
                'length' => '10',
                'defaultValue' => 1
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

        $schemas['allow_probability'] = array(
            'name' => '概率(N/10000)',
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

        $schemas['nick_name'] = array(
            'name' => '提供方名称',
            'data' => array(
                'type' => 'string',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['send_name'] = array(
            'name' => '商户名称',
            'data' => array(
                'type' => 'string',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['wishing'] = array(
            'name' => '红包祝福',
            'data' => array(
                'type' => 'string',
                'length' => 255
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
            )
        );

        $schemas['remark'] = array(
            'name' => '红包备注',
            'data' => array(
                'type' => 'string',
                'length' => 255
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
            )
        );

        $schemas['logo_imgurl'] = array(
            'name' => '商户logo',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['share_content'] = array(
            'name' => '分享文案',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => true
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

        $schemas['share_url'] = array(
            'name' => '分享链接 ',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['share_imgurl'] = array(
            'name' => '分享图片',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '红包发放规则';
    }

    protected function getModel()
    {
        return $this->modelRule;
    }
    
}
