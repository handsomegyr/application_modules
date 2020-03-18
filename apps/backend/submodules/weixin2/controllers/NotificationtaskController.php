<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Notification\Task;
use App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer;
use App\Backend\Submodules\Weixin2\Models\Component\Component;

use App\Backend\Submodules\Weixin2\Models\MassMsg\SendMethod;
use App\Backend\Submodules\Weixin2\Models\MassMsg\MassMsg;
use App\Backend\Submodules\Weixin2\Models\TemplateMsg\TemplateMsg;
use App\Backend\Submodules\Weixin2\Models\CustomMsg\CustomMsg;
use App\Backend\Submodules\Weixin2\Models\User\Tag;


/**
 * @title({name="推送任务"})
 *
 * @name 推送任务
 */
class NotificationtaskController extends \App\Backend\Controllers\FormController
{
    private $modelTask;
    private $modelAuthorizer;
    private $modelComponent;

    private $modelSendMethod;
    private $modelMassMsg;
    private $modelTemplateMsg;
    private $modelCustomMsg;

    public function initialize()
    {
        $this->modelTask = new Task();
        $this->modelAuthorizer = new Authorizer();
        $this->modelComponent = new Component();

        $this->modelSendMethod = new SendMethod();
        $this->modelMassMsg = new MassMsg();
        $this->modelTemplateMsg = new TemplateMsg();
        $this->modelCustomMsg = new CustomMsg();
        $this->modelUserTag = new Tag();

        $this->componentItems = $this->modelComponent->getAll();
        $this->authorizerItems = $this->modelAuthorizer->getAll();


        $this->sendMethodItems = $this->modelSendMethod->getAll();
        $this->massMsgItems = $this->modelMassMsg->getAllByType("", "_id");
        $this->templateMsgItems = $this->modelTemplateMsg->getAll();
        $this->customMsgItems = $this->modelCustomMsg->getAllByType("", "_id");
        $this->userTagItems = $this->modelUserTag->getAllByType("tag_id");

        parent::initialize();
    }
    protected $componentItems = null;
    protected $authorizerItems = null;

    protected $sendMethodItems = null;
    protected $massMsgItems = null;
    protected $templateMsgItems = null;
    protected $customMsgItems = null;
    protected $userTagItems = null;

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['component_appid'] = array(
            'name' => '第三方平台应用ID',
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
                'items' => $this->componentItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->componentItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems
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
        $schemas['name'] = array(
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
        $notificationMethodOptions['1'] = "模板消息";
        $notificationMethodOptions['2'] = "群发消息";
        $notificationMethodOptions['3'] = "客服消息";

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
                'help' => '推送方式 1:模板消息 2:群发消息 3:客服消息',
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
        $schemas['mass_msg_send_method_id'] = array(
            'name' => '群发消息发送方式记录ID',
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
                'items' => $this->sendMethodItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->sendMethodItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->sendMethodItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['template_msg_id'] = array(
            'name' => '模板消息记录ID',
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
                'items' => $this->templateMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->templateMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->templateMsgItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['mass_msg_id'] = array(
            'name' => '群发消息记录ID',
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
                'items' => $this->massMsgItems,
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->massMsgItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->massMsgItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['custom_msg_id'] = array(
            'name' => '客服消息记录ID',
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
                'items' => $this->customMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->customMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->customMsgItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['scheduled_push_time'] = array(
            'name' => '预定推送时间',
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

        $schemas['tag_id'] = array(
            'name' => '群发到的标签的tag_id',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->userTagItems,
                'help' => '群发到的标签的tag_id，参见用户管理中用户分组接口',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->userTagItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->userTagItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['openids_sql'] = array(
            'name' => '获取openid的sql文',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '当不是按照tag_id进行群发消息时必须指定',
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['openids_file'] = array(
            'name' => 'openid列表CSV文件',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelTask->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true,
                'items' => '',
                'help' => '当不是按照tag_id进行群发消息时必须指定',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => ''
            ),
            'search' => array(
                'is_show' => false
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
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $pushStatusOptions,
                'help' => '推送状态 0:待推送 1:推送中 2:推送完成',
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
                'required' => false
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
        $schemas['task_process_total'] = array(
            'name' => '总处理件数',
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
        $schemas['processed_num'] = array(
            'name' => '已处理件数',
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
        $schemas['success_num'] = array(
            'name' => '成功件数',
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
        return '推送任务';
    }

    protected function getModel()
    {
        return $this->modelTask;
    }
}
