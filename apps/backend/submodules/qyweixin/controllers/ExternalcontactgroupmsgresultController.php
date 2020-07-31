<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupMsgResult;

/**
 * @title({name="企业微信"})
 *
 * @name 企业微信
 */
class ExternalcontactgroupmsgresultController extends \App\Backend\Controllers\FormController
{
    private $modelExternalcontactGroupMsgResult;

    public function initialize()
    {
        $this->modelExternalcontactGroupMsgResult = new GroupMsgResult();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['provider_appid'] = array(
            'name' => '第三方服务商应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 32,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['authorizer_appid'] = array(
            'name' => '授权方应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 32,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['msgid'] = array(
            'name' => '企业群发消息的id，可用于获取群发消息发送结果',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['external_userid'] = array(
            'name' => '外部联系人userid',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['chat_id'] = array(
            'name' => '外部客户群id',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['userid'] = array(
            'name' => '企业服务人员的userid',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['status'] = array(
            'name' => '发送状态 0-未发送 1-已发送 2-因客户不是好友导致发送失败 3-因客户已经收到其他群发消息导致发送失败',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => statusOptions,
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => statusOptions,
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'select',
                'items' => statusOptions,
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['send_time'] = array(
            'name' => '发送时间，发送状态为1时返回',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime(),
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '企业微信';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactGroupMsgResult;
    }
}
