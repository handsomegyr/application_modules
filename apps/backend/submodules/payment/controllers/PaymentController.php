<?php
namespace App\Backend\Submodules\Payment\Controllers;

use App\Backend\Submodules\Payment\Models\Payment;

/**
 * @title({name="支付方式管理"})
 *
 * @name 支付方式管理
 */
class PaymentController extends \App\Backend\Controllers\FormController
{

    private $modelPayment;

    public function initialize()
    {
        $this->modelPayment = new Payment();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['code'] = array(
            'name' => '支付方式',
            'data' => array(
                'type' => 'string',
                'length' => 10
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
        $schemas['name'] = array(
            'name' => '支付名称',
            'data' => array(
                'type' => 'string',
                'length' => 10
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
        $schemas['config'] = array(
            'name' => '接口配置信息',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['state'] = array(
            'name' => '是否启用',
            'data' => array(
                'type' => 'boolean',
                'length' => 1
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
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '支付方式';
    }

    protected function getModel()
    {
        return $this->modelPayment;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            $item['config'] = json_encode($item['config']);
        }
        return $list;
    }
}