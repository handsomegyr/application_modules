<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\Notification\TaskLog;
use App\Backend\Submodules\Qyweixin\Models\Notification\Task;
use App\Backend\Submodules\Qyweixin\Models\AgentMsg\AgentMsg;
use App\Backend\Submodules\Qyweixin\Models\AppchatMsg\AppchatMsg;
use App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\LinkedcorpMsg;
use App\Backend\Submodules\Qyweixin\Models\ExternalContact\MsgTemplate;

/**
 * @title({name="企业推送任务日志"})
 *
 * @name 企业推送任务日志
 */
class NotificationtasklogController extends BaseController
{
    // 是否只读
    protected $readonly = true;

    private $modelTaskLog;
    private $modelTask;
    private $modelAppchatMsg;
    private $modelAgentMsg;
    private $modelLinkedcorpMsg;
    private $modelMsgTemplate;

    public function initialize()
    {
        $this->modelTaskLog = new TaskLog();
        $this->modelTask = new Task();
        $this->modelAgentMsg = new AgentMsg();
        $this->modelAppchatMsg = new AppchatMsg();
        $this->modelLinkedcorpMsg = new LinkedcorpMsg();
        $this->modelMsgTemplate = new MsgTemplate();

        $this->agentMsgItems = $this->modelAgentMsg->getAllByType("", "id");
        $this->appchatMsgItems = $this->modelAppchatMsg->getAllByType("", "id");
        $this->linkedcorpMsgItems = $this->modelLinkedcorpMsg->getAllByType("", "id");
        $this->msgTemplateItems = $this->modelMsgTemplate->getAll();
        // 默认为single，表示发送给客户，group表示发送给客户群
        $this->msgTemplateChatType = array(
            'single' => '发送给客户',
            'group' => '发送给客户群'
        );

        parent::initialize();
    }

    protected $msgTemplateChatType = null;
    protected $agentMsgItems = null;
    protected $appchatMsgItems = null;
    protected $linkedcorpMsgItems = null;
    protected $msgTemplateItems = null;

    protected function getSchemas2($schemas)
    {
        $schemas['provider_appid'] = array(
            'name' => '第三方服务商应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->providerItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->providerItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->providerItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['authorizer_appid'] = array(
            'name' => '授权方应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->authorizerItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['notification_task_process_id'] = array(
            'name' => '推送任务处理ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->taskProcessItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->taskProcessItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->taskProcessItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['notification_task_id'] = array(
            'name' => '所属推送任务',
            'data' => array(
                'type' => 'string',
                'length' => 24,
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
        $schemas['notification_task_name'] = array(
            'name' => '任务名',
            'data' => array(
                'type' => 'string',
                'length' => 100,
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

        // 推送方式
        $notificationMethodOptions = array();
        $notificationMethodOptions['1'] = "发送应用消息";
        $notificationMethodOptions['2'] = "发送消息到群聊会话";
        $notificationMethodOptions['3'] = "发送互联企业消息";
        $notificationMethodOptions['4'] = "发送企业群发消息";

        $schemas['notification_method'] = array(
            'name' => '推送方式',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 2
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $notificationMethodOptions,
                'help' => '推送方式 1:发送应用消息 2:发送消息到群聊会话 3:发送互联企业消息 4:发送企业群发消息',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $notificationMethodOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $notificationMethodOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['agent_msg_id'] = array(
            'name' => '应用消息记录ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->agentMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->agentMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->agentMsgItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['appchat_msg_id'] = array(
            'name' => '群聊会话消息记录ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->appchatMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->appchatMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->appchatMsgItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['linkedcorp_msg_id'] = array(
            'name' => '互联企业消息记录ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->linkedcorpMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->linkedcorpMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->linkedcorpMsgItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['externalcontact_msg_template_chat_type'] = array(
            'name' => '群发任务的类型',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->msgTemplateChatType
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->msgTemplateChatType
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->msgTemplateChatType
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['externalcontact_msg_template_id'] = array(
            'name' => '企业群发消息记录ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->msgTemplateItems,
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->msgTemplateItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->msgTemplateItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['changemsginfo_callback'] = array(
            'name' => '消息内容修改回调函数',
            'data' => array(
                'type' => 'string',
                'length' => 190,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '以json格式指定类名和方法名，类名可以为空 eg. {"className":"clsXxx","methodName":"changemsg"}',
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

        $schemas['notification_task_content_id'] = array(
            'name' => '所属推送任务内容',
            'data' => array(
                'type' => 'string',
                'length' => 24,
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
        $schemas['notification_task_content_name'] = array(
            'name' => '任务内容名',
            'data' => array(
                'type' => 'string',
                'length' => 100,
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
        $schemas['userids'] = array(
            'name' => '推送用户userid列表',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
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
        $schemas['userid'] = array(
            'name' => '推送用户的userid',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
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

        // 推送状态
        $pushStatusOptions = array();
        $pushStatusOptions['0'] = "待推送";
        $pushStatusOptions['1'] = "推送中";
        $pushStatusOptions['2'] = "推送完成";
        $pushStatusOptions['3'] = "推送成功";
        $pushStatusOptions['4'] = "推送失败";
        $pushStatusOptions['5'] = "推送关闭";

        $schemas['push_status'] = array(
            'name' => '推送状态',
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
                'items' => $pushStatusOptions,
                'help' => '推送状态 0:待推送 1:推送中 2:推送完成 3:推送成功 4:推送失败 5:推送关闭',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $pushStatusOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $pushStatusOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['push_time'] = array(
            'name' => '推送时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['log_time'] = array(
            'name' => '日志时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['is_ok'] = array(
            'name' => '是否成功推送',
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
                'items' => $this->trueOrFalseDatas
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
        $schemas['errors'] = array(
            'name' => '错误信息',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
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
        $schemas['process_num'] = array(
            'name' => '已处理次数',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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

        return $schemas;
    }

    protected function getName()
    {
        return '企业推送任务日志';
    }

    protected function getModel()
    {
        return $this->modelTaskLog;
    }
}
