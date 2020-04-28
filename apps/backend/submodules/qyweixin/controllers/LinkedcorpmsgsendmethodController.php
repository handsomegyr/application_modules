<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\SendMethod;

/**
 * @title({name="互联企业消息发送方式"})
 *
 * @name 互联企业消息发送方式
 */
class LinkedcorpmsgsendmethodController extends \App\Backend\Controllers\FormController
{
    private $modelSendMethod;
    public function initialize()
    {
        $this->modelSendMethod = new SendMethod();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['name'] = array(
            'name' => '互联企业消息发送方式名称',
            'data' => array(
                'type' => 'string',
                'length' => 50,
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

        //互联企业消息发送方式 0:发送给应用可见范围内的所有人 1:按成员ID列表发送 2:按部门ID列表发送 3:按标签ID列表发送
        $sendMethodOptions = array();
        $sendMethodOptions[0] = "发送给应用可见范围内的所有人";
        $sendMethodOptions[1] = "按成员ID列表发送";
        $sendMethodOptions[2] = "按部门ID列表发送";
        $sendMethodOptions[3] = "按标签ID列表发送";
        $schemas['send_method'] = array(
            'name' => '互联企业消息发送方式',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $sendMethodOptions,
                'help' => '互联企业消息发送方式 0:发送给应用可见范围内的所有人 1:按成员ID列表发送 2:按部门ID列表发送 3:按标签ID列表发送',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $sendMethodOptions,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $sendMethodOptions,
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '互联企业消息发送方式';
    }

    protected function getModel()
    {
        return $this->modelSendMethod;
    }
}
