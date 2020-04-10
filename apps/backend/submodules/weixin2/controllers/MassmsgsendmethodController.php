<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\MassMsg\SendMethod;

/**
 * @title({name="群发消息发送方式"})
 *
 * @name 群发消息发送方式
 */
class MassmsgsendmethodController extends \App\Backend\Controllers\FormController
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
            'name' => '群发消息发送方式名称',
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

        //群发消息发送方式 0:全部发送 1:按tag_id发送 2:按openid列表
        $sendMethodOptions = array();
        $sendMethodOptions[0] = "全部发送";
        $sendMethodOptions[1] = "按tag_id发送";
        $sendMethodOptions[2] = "按openid列表发送";
        $schemas['send_method'] = array(
            'name' => '群发消息发送方式',
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
                'help' => '群发消息发送方式 0:全部发送 1:按tag_id发送 2:按openid列表',
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
        $schemas['is_to_all'] = array(
            'name' => '用于设定是否向全部用户发送',
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
                'items' => $this->trueOrFalseDatas,
                'help' => '用于设定是否向全部用户发送，值为true或false，选择true该消息群发给所有用户，选择false可根据tag_id发送给指定群组的用户',
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
        $schemas['send_ignore_reprint'] = array(
            'name' => '是否继续群发',
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
                'items' => $this->trueOrFalseDatas,
                'help' => '图文消息被判定为转载时，是否继续群发。 1为继续群发（转载），0为停止群发。 该参数默认为0。',
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

        return $schemas;
    }

    protected function getName()
    {
        return '群发消息发送方式';
    }

    protected function getModel()
    {
        return $this->modelSendMethod;
    }
}
