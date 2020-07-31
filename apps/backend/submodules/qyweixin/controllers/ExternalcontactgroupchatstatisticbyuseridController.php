<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupChatStatisticByUserid;

/**
 * @title({name="企业微信"})
 *
 * @name 企业微信
 */
class ExternalcontactgroupchatstatisticbyuseridController extends \App\Backend\Controllers\FormController
{
    private $modelExternalcontactGroupChatStatisticByuserid;

    public function initialize()
    {
        $this->modelExternalcontactGroupChatStatisticByuserid = new GroupChatStatisticByUserid();
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
        $schemas['day_begin_time'] = array(
            'name' => '开始时间',
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
        $schemas['owner'] = array(
            'name' => '群主ID',
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
        $schemas['new_chat_cnt'] = array(
            'name' => '新增客户群数量',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['chat_total'] = array(
            'name' => '截至当天客户群总数量',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['chat_has_msg'] = array(
            'name' => '截至当天有发过消息的客户群数量',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['new_member_cnt'] = array(
            'name' => '客户群新增群人数。',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['member_total'] = array(
            'name' => '截至当天客户群总人数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['member_has_msg'] = array(
            'name' => '截至当天有发过消息的群成员数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['msg_total'] = array(
            'name' => '截至当天客户群消息总数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['get_time'] = array(
            'name' => '获取时间',
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
        return $this->modelExternalcontactGroupChatStatisticByuserid;
    }
}
