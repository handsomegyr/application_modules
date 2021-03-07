<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\Notification\Task;
use App\Backend\Submodules\Qyweixin\Models\AgentMsg\AgentMsg;
use App\Backend\Submodules\Qyweixin\Models\AppchatMsg\AppchatMsg;
use App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\LinkedcorpMsg;
use App\Backend\Submodules\Qyweixin\Models\ExternalContact\MsgTemplate;

/**
 * @title({name="企业推送任务"})
 *
 * @name 企业推送任务
 */
class NotificationtaskController extends BaseController
{
    private $modelTask;
    private $modelAppchatMsg;
    private $modelAgentMsg;
    private $modelLinkedcorpMsg;
    private $modelMsgTemplate;

    public function initialize()
    {
        $this->modelTask = new Task();
        $this->modelAgentMsg = new AgentMsg();
        $this->modelAppchatMsg = new AppchatMsg();
        $this->modelLinkedcorpMsg = new LinkedcorpMsg();
        $this->modelMsgTemplate = new MsgTemplate();

        $this->agentMsgItems = $this->modelAgentMsg->getAllByType("", "_id");
        $this->appchatMsgItems = $this->modelAppchatMsg->getAllByType("", "_id");
        $this->linkedcorpMsgItems = $this->modelLinkedcorpMsg->getAllByType("", "_id");
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

    protected function getHeaderTools2($tools)
    {
        $tools['addtask1'] = array(
            'title' => '追加按应用消息发送任务',
            'action' => 'addtask1',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        $tools['addtask2'] = array(
            'title' => '追加按群聊会话消息发送任务',
            'action' => 'addtask2',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        $tools['addtask3'] = array(
            'title' => '追加按互联企业消息发送任务',
            'action' => 'addtask3',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['modifytask1'] = array(
            'title' => '修改按应用消息发送任务',
            'action' => 'modifytask1',
            'is_show' => function ($row) {
                if (intval($row['push_status']) === 0) {
                    if ($row['notification_method'] == 1) {
                        return true;
                    }
                }
                return false;
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['modifytask2'] = array(
            'title' => '修改按群聊会话消息发送任务',
            'action' => 'modifytask2',
            'is_show' => function ($row) {
                if (intval($row['push_status']) === 0) {
                    if ($row['notification_method'] == 2) {
                        return true;
                    }
                }
                return false;
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['modifytask3'] = array(
            'title' => '修改按互联企业消息发送任务',
            'action' => 'modifytask3',
            'is_show' => function ($row) {
                if (intval($row['push_status']) === 0) {
                    if ($row['notification_method'] == 3) {
                        return true;
                    }
                }
                return false;
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['closetask'] = array(
            'title' => '关闭发送任务',
            'action' => 'closetask',
            'is_show' => function ($row) {

                $pushStatusExcludeList = array(
                    \App\Qyweixin\Models\Notification\TaskProcess::PUSH_OVER,
                    \App\Qyweixin\Models\Notification\TaskProcess::PUSH_SUCCESS,
                    \App\Qyweixin\Models\Notification\TaskProcess::PUSH_FAIL,
                    \App\Qyweixin\Models\Notification\TaskProcess::PUSH_CLOSE,
                );

                if (!in_array(intval($row['push_status']), $pushStatusExcludeList)) {
                    return true;
                }
                return false;
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="追加按应用消息发送任务"})
     *
     * @name 追加按应用消息发送任务
     */
    public function addtask1Action()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/notificationtask/addtask1?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                $fields['name'] = array(
                    'name' => '任务名',
                    'data' => array(
                        'type' => 'string',
                        'length' => 100,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                // 预定推送时间
                $fields['scheduled_push_time'] = array(
                    'name' => '预定推送时间',
                    'data' => array(
                        'type' => 'datetime',
                        'length' => 19
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $fields['agent_msg_id'] = array(
                    'name' => '应用消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->agentMsgItems,
                    ),
                );
                $fields['changemsginfo_callback'] = array(
                    'name' => '消息内容修改回调函数',
                    'data' => array(
                        'type' => 'string',
                        'length' => 190
                    ),
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => '',
                        'help' => '以json格式指定类名和方法名，类名可以为空 eg. {"className":"clsXxx","methodName":"changemsg"}',
                    )
                );

                $fields['userids'] = array(
                    'name' => 'userid列表，逗号分隔',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_sql'] = array(
                    'name' => '获取userid的sql文，在select语句中必须要有userid字段',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_file'] = array(
                    'name' => '请选择一个首列为userid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "追加按应用消息发送任务";
                $row = array();
                $row['scheduled_push_time'] = getCurrentTime();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('notificationtask_provider_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $agent_msg_id = trim($this->request->get('agent_msg_id')); // 应用消息记录ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $userids = trim($this->request->get('userids')); // userid列表
                $userids_sql = trim($this->request->get('userids_sql')); // 获取userid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($agent_msg_id)) {
                    return $this->makeJsonError("应用消息记录ID未指定");
                }
                $isValid = $this->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
                if (empty($isValid)) {
                    return $this->makeJsonError("消息内容修改回调函数不合法");
                }
                if (empty($scheduled_push_time)) {
                    return $this->makeJsonError("起始日期未设定");
                }
                $scheduled_push_time = strtotime($scheduled_push_time);
                if ($scheduled_push_time < time()) {
                    return $this->makeJsonError("预定推送时间小于当前日期");
                }
                // if (empty($provider_appid)) {
                //     return $this->makeJsonError("第三方服务商应用ID未设定");
                // }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                $uploadFiles = array();
                // 1 Check if the user has uploaded files
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        $uploadFiles[$file->getKey()] = $file;
                    }
                }
                // 下面的代码获取到上传的文件，然后使用`maatwebsite/excel`等包来处理上传你的文件，保存到数据库
                $userids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['userids_file'])) {
                    $is_uploadFile = false;
                }
                if ($is_uploadFile) {
                    // 如果是POST请求的话就是进行具体的处理
                    if (empty($exts)) {
                        $exts =  [
                            'csv'
                        ];
                    }
                    if (empty($sizes)) {
                        $sizes = array(
                            // 1024K
                            'max' => 1024 * 1024
                        );
                    }

                    $file = $uploadFiles['userids_file'];
                    $fileError = $file->getError();
                    if (!empty($fileError)) {
                        return $this->makeJsonError("导入文件上传失败，错误码：{$fileError}");
                    }
                    if (!$file->isUploadedFile()) {
                        return $this->makeJsonError("导入文件还未成功上传");
                    }

                    // 2 先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片后缀的数组内
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $exts)) {
                        return $this->makeJsonError("文件类型不合法");
                    }
                    // 3 文件大小 字节单位
                    $fSize = $file->getSize();
                    if ($fSize > $sizes['max']) {
                        return $this->makeJsonError("文件大小过大{$fSize}");
                    }
                    // 4.将文件取一个新的名字
                    $newName = $file->getName();
                    // 5.数据上传导入处理 上传导入表中
                    $uploadPath = $this->modelTask->getUploadPath();
                    $userids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $userids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($userids) && empty($userids_sql) && empty($userids_file)) {
                    return $this->makeJsonError("userid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    // 应用消息发送
                    $data['notification_method'] = 1;
                    $data['agent_msg_id'] = $agent_msg_id;
                    $data['appchat_msg_id'] = '';
                    $data['linkedcorp_msg_id'] = '';
                    $data['externalcontact_msg_template_chat_type'] = '';
                    $data['externalcontact_msg_template_id'] = '';

                    $data['changemsginfo_callback'] = $changemsginfo_callback;
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['userids'] = $userids;
                    $data['userids_sql'] = $userids_sql;
                    $data['userids_file'] = $userids_file;
                    $data['push_status'] = 0;
                    $data['push_time'] = \App\Common\Utils\Helper::getCurrentTime('0001-01-01 00:00:00');
                    $data['task_process_total'] = 0;
                    $data['processed_num'] = 0;
                    $data['success_num'] = 0;
                    $this->modelTask->insert($data);

                    // DB::commit();
                    $this->modelTask->commit();

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    //return $this->response()->success('已成功上传,上传记录数:' . $data_num)->refresh();
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')));
                } catch (\Exception $e) {
                    // DB::rollback();
                    $this->modelTask->rollback();
                    return $this->makeJsonError($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="追加按群聊会话消息发送任务"})
     *
     * @name 追加按群聊会话消息发送任务
     */
    public function addtask2Action()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/notificationtask/addtask2?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                $fields['name'] = array(
                    'name' => '任务名',
                    'data' => array(
                        'type' => 'string',
                        'length' => 100,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                // 预定推送时间
                $fields['scheduled_push_time'] = array(
                    'name' => '预定推送时间',
                    'data' => array(
                        'type' => 'datetime',
                        'length' => 19
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $fields['appchat_msg_id'] = array(
                    'name' => '群聊会话消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->appchatMsgItems,
                    ),
                );
                $fields['changemsginfo_callback'] = array(
                    'name' => '消息内容修改回调函数',
                    'data' => array(
                        'type' => 'string',
                        'length' => 190
                    ),
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => '',
                        'help' => '以json格式指定类名和方法名，类名可以为空 eg. {"className":"clsXxx","methodName":"changemsg"}',
                    )
                );
                $fields['userids'] = array(
                    'name' => 'userid列表，逗号分隔',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_sql'] = array(
                    'name' => '获取userid的sql文，在select语句中必须要有userid字段',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_file'] = array(
                    'name' => '请选择一个首列为userid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "追加按群聊会话消息发送任务";
                $row = array();
                $row['scheduled_push_time'] = getCurrentTime();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('notificationtask_provider_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $appchat_msg_id = trim($this->request->get('appchat_msg_id')); // 群聊会话消息ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $userids = trim($this->request->get('userids')); // userid列表
                $userids_sql = trim($this->request->get('userids_sql')); // 获取userid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($appchat_msg_id)) {
                    return $this->makeJsonError("群聊会话消息ID未指定");
                }
                $isValid = $this->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
                if (empty($isValid)) {
                    return $this->makeJsonError("消息内容修改回调函数不合法");
                }
                if (empty($scheduled_push_time)) {
                    return $this->makeJsonError("起始日期未设定");
                }
                $scheduled_push_time = strtotime($scheduled_push_time);
                if ($scheduled_push_time < time()) {
                    return $this->makeJsonError("预定推送时间小于当前日期");
                }
                // if (empty($provider_appid)) {
                //     return $this->makeJsonError("第三方服务商应用ID未设定");
                // }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                $uploadFiles = array();
                // 1 Check if the user has uploaded files
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        $uploadFiles[$file->getKey()] = $file;
                    }
                }
                // 下面的代码获取到上传的文件，然后使用`maatwebsite/excel`等包来处理上传你的文件，保存到数据库
                $userids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['userids_file'])) {
                    $is_uploadFile = false;
                }
                if ($is_uploadFile) {
                    // 如果是POST请求的话就是进行具体的处理
                    if (empty($exts)) {
                        $exts =  [
                            'csv'
                        ];
                    }
                    if (empty($sizes)) {
                        $sizes = array(
                            // 1024K
                            'max' => 1024 * 1024
                        );
                    }

                    $file = $uploadFiles['userids_file'];
                    $fileError = $file->getError();
                    if (!empty($fileError)) {
                        return $this->makeJsonError("导入文件上传失败，错误码：{$fileError}");
                    }
                    if (!$file->isUploadedFile()) {
                        return $this->makeJsonError("导入文件还未成功上传");
                    }

                    // 2 先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片后缀的数组内
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $exts)) {
                        return $this->makeJsonError("文件类型不合法");
                    }
                    // 3 文件大小 字节单位
                    $fSize = $file->getSize();
                    if ($fSize > $sizes['max']) {
                        return $this->makeJsonError("文件大小过大{$fSize}");
                    }
                    // 4.将文件取一个新的名字
                    $newName = $file->getName();
                    // 5.数据上传导入处理 上传导入表中
                    $uploadPath = $this->modelTask->getUploadPath();
                    $userids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $userids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($userids) && empty($userids_sql) && empty($userids_file)) {
                    return $this->makeJsonError("userid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    // 群聊会话消息发送
                    $data['notification_method'] = 2;
                    $data['agent_msg_id'] = '';
                    $data['appchat_msg_id'] = $appchat_msg_id;
                    $data['linkedcorp_msg_id'] = '';
                    $data['externalcontact_msg_template_chat_type'] = '';
                    $data['externalcontact_msg_template_id'] = '';
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['userids'] = $userids;
                    $data['userids_sql'] = $userids_sql;
                    $data['userids_file'] = $userids_file;
                    $data['push_status'] = 0;
                    $data['push_time'] = \App\Common\Utils\Helper::getCurrentTime('0001-01-01 00:00:00');
                    $data['task_process_total'] = 0;
                    $data['processed_num'] = 0;
                    $data['success_num'] = 0;
                    $this->modelTask->insert($data);

                    // DB::commit();
                    $this->modelTask->commit();

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    //return $this->response()->success('已成功上传,上传记录数:' . $data_num)->refresh();
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')));
                } catch (\Exception $e) {
                    // DB::rollback();
                    $this->modelTask->rollback();
                    return $this->makeJsonError($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="追加按互联企业消息发送任务"})
     *
     * @name 追加按互联企业消息发送任务
     */
    public function addtask3Action()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/notificationtask/addtask3?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                $fields['name'] = array(
                    'name' => '任务名',
                    'data' => array(
                        'type' => 'string',
                        'length' => 100,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                // 预定推送时间
                $fields['scheduled_push_time'] = array(
                    'name' => '预定推送时间',
                    'data' => array(
                        'type' => 'datetime',
                        'length' => 19
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $fields['linkedcorp_msg_id'] = array(
                    'name' => '互联企业消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->linkedcorpMsgItems,
                    ),
                );
                $fields['changemsginfo_callback'] = array(
                    'name' => '消息内容修改回调函数',
                    'data' => array(
                        'type' => 'string',
                        'length' => 190
                    ),
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => '',
                        'help' => '以json格式指定类名和方法名，类名可以为空 eg. {"className":"clsXxx","methodName":"changemsg"}',
                    )
                );
                $fields['userids'] = array(
                    'name' => 'userid列表，逗号分隔',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_sql'] = array(
                    'name' => '获取userid的sql文，在select语句中必须要有userid字段',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_file'] = array(
                    'name' => '请选择一个首列为userid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "追加按互联企业消息发送任务";
                $row = array();
                $row['scheduled_push_time'] = getCurrentTime();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('notificationtask_provider_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $linkedcorp_msg_id = trim($this->request->get('linkedcorp_msg_id')); // 互联企业消息ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $userids = trim($this->request->get('userids')); // userid列表
                $userids_sql = trim($this->request->get('userids_sql')); // 获取userid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($linkedcorp_msg_id)) {
                    return $this->makeJsonError("互联企业消息ID未指定");
                }
                $isValid = $this->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
                if (empty($isValid)) {
                    return $this->makeJsonError("消息内容修改回调函数不合法");
                }
                if (empty($scheduled_push_time)) {
                    return $this->makeJsonError("起始日期未设定");
                }
                $scheduled_push_time = strtotime($scheduled_push_time);
                if ($scheduled_push_time < time()) {
                    return $this->makeJsonError("预定推送时间小于当前日期");
                }
                // if (empty($provider_appid)) {
                //     return $this->makeJsonError("第三方服务商应用ID未设定");
                // }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                $uploadFiles = array();
                // 1 Check if the user has uploaded files
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        $uploadFiles[$file->getKey()] = $file;
                    }
                }
                // 下面的代码获取到上传的文件，然后使用`maatwebsite/excel`等包来处理上传你的文件，保存到数据库
                $userids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['userids_file'])) {
                    $is_uploadFile = false;
                }
                if ($is_uploadFile) {
                    // 如果是POST请求的话就是进行具体的处理
                    if (empty($exts)) {
                        $exts =  [
                            'csv'
                        ];
                    }
                    if (empty($sizes)) {
                        $sizes = array(
                            // 1024K
                            'max' => 1024 * 1024
                        );
                    }

                    $file = $uploadFiles['userids_file'];
                    $fileError = $file->getError();
                    if (!empty($fileError)) {
                        return $this->makeJsonError("导入文件上传失败，错误码：{$fileError}");
                    }
                    if (!$file->isUploadedFile()) {
                        return $this->makeJsonError("导入文件还未成功上传");
                    }

                    // 2 先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片后缀的数组内
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $exts)) {
                        return $this->makeJsonError("文件类型不合法");
                    }
                    // 3 文件大小 字节单位
                    $fSize = $file->getSize();
                    if ($fSize > $sizes['max']) {
                        return $this->makeJsonError("文件大小过大{$fSize}");
                    }
                    // 4.将文件取一个新的名字
                    $newName = $file->getName();
                    // 5.数据上传导入处理 上传导入表中
                    $uploadPath = $this->modelTask->getUploadPath();
                    $userids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $userids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($userids) && empty($userids_sql) && empty($userids_file)) {
                    return $this->makeJsonError("userid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    // 互联企业消息发送
                    $data['notification_method'] = 3;
                    $data['agent_msg_id'] = '';
                    $data['appchat_msg_id'] = '';
                    $data['linkedcorp_msg_id'] = $linkedcorp_msg_id;
                    $data['externalcontact_msg_template_chat_type'] = '';
                    $data['externalcontact_msg_template_id'] = '';
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['userids'] = $userids;
                    $data['userids_sql'] = $userids_sql;
                    $data['userids_file'] = $userids_file;
                    $data['push_status'] = 0;
                    $data['push_time'] = \App\Common\Utils\Helper::getCurrentTime('0001-01-01 00:00:00');
                    $data['task_process_total'] = 0;
                    $data['processed_num'] = 0;
                    $data['success_num'] = 0;
                    $this->modelTask->insert($data);

                    // DB::commit();
                    $this->modelTask->commit();

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    //return $this->response()->success('已成功上传,上传记录数:' . $data_num)->refresh();
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')));
                } catch (\Exception $e) {
                    // DB::rollback();
                    $this->modelTask->rollback();
                    return $this->makeJsonError($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="修改按应用消息发送任务"})
     *
     * @name 修改按应用消息发送任务
     */
    public function modifytask1Action()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/notificationtask/modifytask1?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelTask->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );
                $fields['name'] = array(
                    'name' => '任务名',
                    'data' => array(
                        'type' => 'string',
                        'length' => 100
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                // 预定推送时间
                $fields['scheduled_push_time'] = array(
                    'name' => '预定推送时间',
                    'data' => array(
                        'type' => 'datetime',
                        'length' => 19
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $fields['agent_msg_id'] = array(
                    'name' => '应用消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->agentMsgItems
                    ),
                );
                $fields['changemsginfo_callback'] = array(
                    'name' => '消息内容修改回调函数',
                    'data' => array(
                        'type' => 'string',
                        'length' => 190
                    ),
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => '',
                        'help' => '以json格式指定类名和方法名，类名可以为空 eg. {"className":"clsXxx","methodName":"changemsg"}',
                    )
                );
                $fields['userids'] = array(
                    'name' => 'userid列表，逗号分隔',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_sql'] = array(
                    'name' => '获取userid的sql文，在select语句中必须要有userid字段',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_file'] = array(
                    'name' => '请选择一个首列为userid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "修改按应用消息发送任务";
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('notificationtask_provider_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $agent_msg_id = trim($this->request->get('agent_msg_id')); // 应用消息记录ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $userids = trim($this->request->get('userids')); // userid列表
                $userids_sql = trim($this->request->get('userids_sql')); // 获取userid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($agent_msg_id)) {
                    return $this->makeJsonError("应用消息记录ID未指定");
                }
                $isValid = $this->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
                if (empty($isValid)) {
                    return $this->makeJsonError("消息内容修改回调函数不合法");
                }
                if (empty($scheduled_push_time)) {
                    return $this->makeJsonError("起始日期未设定");
                }
                $scheduled_push_time = strtotime($scheduled_push_time);
                if ($scheduled_push_time < time()) {
                    return $this->makeJsonError("预定推送时间小于当前日期");
                }
                // if (empty($provider_appid)) {
                //     return $this->makeJsonError("第三方服务商应用ID未设定");
                // }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                $uploadFiles = array();
                // 1 Check if the user has uploaded files
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        $uploadFiles[$file->getKey()] = $file;
                    }
                }
                // 下面的代码获取到上传的文件，然后使用`maatwebsite/excel`等包来处理上传你的文件，保存到数据库
                $userids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['userids_file'])) {
                    $is_uploadFile = false;
                }
                if ($is_uploadFile) {
                    // 如果是POST请求的话就是进行具体的处理
                    if (empty($exts)) {
                        $exts =  [
                            'csv'
                        ];
                    }
                    if (empty($sizes)) {
                        $sizes = array(
                            // 1024K
                            'max' => 1024 * 1024
                        );
                    }

                    $file = $uploadFiles['userids_file'];
                    $fileError = $file->getError();
                    if (!empty($fileError)) {
                        return $this->makeJsonError("导入文件上传失败，错误码：{$fileError}");
                    }
                    if (!$file->isUploadedFile()) {
                        return $this->makeJsonError("导入文件还未成功上传");
                    }

                    // 2 先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片后缀的数组内
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $exts)) {
                        return $this->makeJsonError("文件类型不合法");
                    }
                    // 3 文件大小 字节单位
                    $fSize = $file->getSize();
                    if ($fSize > $sizes['max']) {
                        return $this->makeJsonError("文件大小过大{$fSize}");
                    }
                    // 4.将文件取一个新的名字
                    $newName = $file->getName();
                    // 5.数据上传导入处理 上传导入表中
                    $uploadPath = $this->modelTask->getUploadPath();
                    $userids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $userids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($userids) && empty($userids_sql) && empty($userids_file)) {
                    return $this->makeJsonError("userid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    $data['agent_msg_id'] = $agent_msg_id;
                    $data['changemsginfo_callback'] = $changemsginfo_callback;
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['userids'] = $userids;
                    $data['userids_sql'] = $userids_sql;
                    $data['userids_file'] = $userids_file;
                    $this->modelTask->update(array('_id' => $row['_id']), array('$set' => $data));

                    // DB::commit();
                    $this->modelTask->commit();

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')));
                } catch (\Exception $e) {
                    // DB::rollback();
                    $this->modelTask->rollback();
                    return $this->makeJsonError($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="修改按群聊会话消息发送任务"})
     *
     * @name 修改按群聊会话消息发送任务
     */
    public function modifytask2Action()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/notificationtask/modifytask2?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelTask->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );
                $fields['name'] = array(
                    'name' => '任务名',
                    'data' => array(
                        'type' => 'string',
                        'length' => 100
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                // 预定推送时间
                $fields['scheduled_push_time'] = array(
                    'name' => '预定推送时间',
                    'data' => array(
                        'type' => 'datetime',
                        'length' => 19
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $fields['appchat_msg_id'] = array(
                    'name' => '群聊会话消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->appchatMsgItems
                    ),
                );
                $fields['changemsginfo_callback'] = array(
                    'name' => '消息内容修改回调函数',
                    'data' => array(
                        'type' => 'string',
                        'length' => 190
                    ),
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => '',
                        'help' => '以json格式指定类名和方法名，类名可以为空 eg. {"className":"clsXxx","methodName":"changemsg"}',
                    )
                );
                $fields['userids'] = array(
                    'name' => 'userid列表，逗号分隔',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_sql'] = array(
                    'name' => '获取userid的sql文，在select语句中必须要有userid字段',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_file'] = array(
                    'name' => '请选择一个首列为userid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "修改按应用消息发送任务";
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('notificationtask_provider_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $appchat_msg_id = intval($this->request->get('appchat_msg_id')); // 群聊会话消息ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $userids = trim($this->request->get('userids')); // userid列表
                $userids_sql = trim($this->request->get('userids_sql')); // 获取userid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($appchat_msg_id)) {
                    return $this->makeJsonError("群聊会话消息ID未指定");
                }
                $isValid = $this->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
                if (empty($isValid)) {
                    return $this->makeJsonError("消息内容修改回调函数不合法");
                }
                if (empty($scheduled_push_time)) {
                    return $this->makeJsonError("起始日期未设定");
                }
                $scheduled_push_time = strtotime($scheduled_push_time);
                if ($scheduled_push_time < time()) {
                    return $this->makeJsonError("预定推送时间小于当前日期");
                }
                // if (empty($provider_appid)) {
                //     return $this->makeJsonError("第三方服务商应用ID未设定");
                // }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                $uploadFiles = array();
                // 1 Check if the user has uploaded files
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        $uploadFiles[$file->getKey()] = $file;
                    }
                }
                // 下面的代码获取到上传的文件，然后使用`maatwebsite/excel`等包来处理上传你的文件，保存到数据库
                $userids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['userids_file'])) {
                    $is_uploadFile = false;
                }
                if ($is_uploadFile) {
                    // 如果是POST请求的话就是进行具体的处理
                    if (empty($exts)) {
                        $exts =  [
                            'csv'
                        ];
                    }
                    if (empty($sizes)) {
                        $sizes = array(
                            // 1024K
                            'max' => 1024 * 1024
                        );
                    }

                    $file = $uploadFiles['userids_file'];
                    $fileError = $file->getError();
                    if (!empty($fileError)) {
                        return $this->makeJsonError("导入文件上传失败，错误码：{$fileError}");
                    }
                    if (!$file->isUploadedFile()) {
                        return $this->makeJsonError("导入文件还未成功上传");
                    }

                    // 2 先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片后缀的数组内
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $exts)) {
                        return $this->makeJsonError("文件类型不合法");
                    }
                    // 3 文件大小 字节单位
                    $fSize = $file->getSize();
                    if ($fSize > $sizes['max']) {
                        return $this->makeJsonError("文件大小过大{$fSize}");
                    }
                    // 4.将文件取一个新的名字
                    $newName = $file->getName();
                    // 5.数据上传导入处理 上传导入表中
                    $uploadPath = $this->modelTask->getUploadPath();
                    $userids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $userids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($userids) && empty($userids_sql) && empty($userids_file)) {
                    return $this->makeJsonError("userid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    $data['appchat_msg_id'] = $appchat_msg_id;
                    $data['changemsginfo_callback'] = $changemsginfo_callback;
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['userids'] = $userids;
                    $data['userids_sql'] = $userids_sql;
                    $data['userids_file'] = $userids_file;
                    $this->modelTask->update(array('_id' => $row['_id']), array('$set' => $data));

                    // DB::commit();
                    $this->modelTask->commit();

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')));
                } catch (\Exception $e) {
                    // DB::rollback();
                    $this->modelTask->rollback();
                    return $this->makeJsonError($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="修改按互联企业消息发送任务"})
     *
     * @name 修改按互联企业消息发送任务
     */
    public function modifytask3Action()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/notificationtask/modifytask3?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelTask->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );
                $fields['name'] = array(
                    'name' => '任务名',
                    'data' => array(
                        'type' => 'string',
                        'length' => 100
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                // 预定推送时间
                $fields['scheduled_push_time'] = array(
                    'name' => '预定推送时间',
                    'data' => array(
                        'type' => 'datetime',
                        'length' => 19
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $fields['linkedcorp_msg_id'] = array(
                    'name' => '互联企业消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->appchatMsgItems
                    ),
                );
                $fields['changemsginfo_callback'] = array(
                    'name' => '消息内容修改回调函数',
                    'data' => array(
                        'type' => 'string',
                        'length' => 190
                    ),
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => '',
                        'help' => '以json格式指定类名和方法名，类名可以为空 eg. {"className":"clsXxx","methodName":"changemsg"}',
                    )
                );
                $fields['userids'] = array(
                    'name' => 'userid列表，逗号分隔',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_sql'] = array(
                    'name' => '获取userid的sql文，在select语句中必须要有userid字段',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => ''
                    )
                );
                $fields['userids_file'] = array(
                    'name' => '请选择一个首列为userid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "修改按应用消息发送任务";
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('notificationtask_provider_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $linkedcorp_msg_id = intval($this->request->get('linkedcorp_msg_id')); // 互联企业消息ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $userids = trim($this->request->get('userids')); // userid列表
                $userids_sql = trim($this->request->get('userids_sql')); // 获取userid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($linkedcorp_msg_id)) {
                    return $this->makeJsonError("互联企业消息ID未指定");
                }
                $isValid = $this->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
                if (empty($isValid)) {
                    return $this->makeJsonError("消息内容修改回调函数不合法");
                }
                if (empty($scheduled_push_time)) {
                    return $this->makeJsonError("起始日期未设定");
                }
                $scheduled_push_time = strtotime($scheduled_push_time);
                if ($scheduled_push_time < time()) {
                    return $this->makeJsonError("预定推送时间小于当前日期");
                }
                // if (empty($provider_appid)) {
                //     return $this->makeJsonError("第三方服务商应用ID未设定");
                // }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                $uploadFiles = array();
                // 1 Check if the user has uploaded files
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        $uploadFiles[$file->getKey()] = $file;
                    }
                }
                // 下面的代码获取到上传的文件，然后使用`maatwebsite/excel`等包来处理上传你的文件，保存到数据库
                $userids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['userids_file'])) {
                    $is_uploadFile = false;
                }
                if ($is_uploadFile) {
                    // 如果是POST请求的话就是进行具体的处理
                    if (empty($exts)) {
                        $exts =  [
                            'csv'
                        ];
                    }
                    if (empty($sizes)) {
                        $sizes = array(
                            // 1024K
                            'max' => 1024 * 1024
                        );
                    }

                    $file = $uploadFiles['userids_file'];
                    $fileError = $file->getError();
                    if (!empty($fileError)) {
                        return $this->makeJsonError("导入文件上传失败，错误码：{$fileError}");
                    }
                    if (!$file->isUploadedFile()) {
                        return $this->makeJsonError("导入文件还未成功上传");
                    }

                    // 2 先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片后缀的数组内
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, $exts)) {
                        return $this->makeJsonError("文件类型不合法");
                    }
                    // 3 文件大小 字节单位
                    $fSize = $file->getSize();
                    if ($fSize > $sizes['max']) {
                        return $this->makeJsonError("文件大小过大{$fSize}");
                    }
                    // 4.将文件取一个新的名字
                    $newName = $file->getName();
                    // 5.数据上传导入处理 上传导入表中
                    $uploadPath = $this->modelTask->getUploadPath();
                    $userids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $userids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($userids) && empty($userids_sql) && empty($userids_file)) {
                    return $this->makeJsonError("userid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    $data['linkedcorp_msg_id'] = $linkedcorp_msg_id;
                    $data['changemsginfo_callback'] = $changemsginfo_callback;
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['userids'] = $userids;
                    $data['userids_sql'] = $userids_sql;
                    $data['userids_file'] = $userids_file;
                    $this->modelTask->update(array('_id' => $row['_id']), array('$set' => $data));

                    // DB::commit();
                    $this->modelTask->commit();

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')));
                } catch (\Exception $e) {
                    // DB::rollback();
                    $this->modelTask->rollback();
                    return $this->makeJsonError($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="关闭发送任务"})
     *
     * @name 关闭发送任务
     */
    public function closetaskAction()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/notificationtask/closetask?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelTask->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool(true);
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );
                $fields['name'] = array(
                    'name' => '任务名',
                    'data' => array(
                        'type' => 'string',
                        'length' => 100
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'items' => '',
                        'readonly' => true,
                    )
                );
                // 预定推送时间
                $fields['scheduled_push_time'] = array(
                    'name' => '预定推送时间',
                    'data' => array(
                        'type' => 'datetime',
                        'length' => 19
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );

                // 推送状态 0:待推送 1:推送中 2:推送完成 3:推送关闭
                $pushStatusOptions = array();
                $pushStatusOptions['0'] = "待推送";
                $pushStatusOptions['1'] = "推送中";
                $pushStatusOptions['2'] = "推送完成";
                $pushStatusOptions['5'] = "推送关闭";
                $fields['push_status'] = array(
                    'name' => '推送状态',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $pushStatusOptions,
                        'readonly' => true,
                    ),
                );

                $fields['userids'] = array(
                    'name' => 'userid列表，逗号分隔',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => '',
                        'readonly' => true,
                    )
                );
                $fields['userids_sql'] = array(
                    'name' => '获取userid的sql文，在select语句中必须要有userid字段',
                    'data' => array(
                        'type' => 'string',
                        'length' => 1024,
                    ),
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        'items' => '',
                        'readonly' => true,
                    )
                );
                $fields['userids_file'] = array(
                    'name' => '请选择一个首列为userid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );
                $title = "关闭发送任务";
                return $this->showModal($title, $fields, $row);
            } else {
                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $push_status = \App\Qyweixin\Models\Notification\TaskProcess::PUSH_CLOSE;
                    $pushStatusExcludeList = array(
                        \App\Qyweixin\Models\Notification\TaskProcess::PUSH_OVER,
                        \App\Qyweixin\Models\Notification\TaskProcess::PUSH_SUCCESS,
                        \App\Qyweixin\Models\Notification\TaskProcess::PUSH_FAIL,
                        \App\Qyweixin\Models\Notification\TaskProcess::PUSH_CLOSE,
                    );
                    $task_id = $row['_id'];
                    $data = array();
                    $data['push_status'] = $push_status; // 推送关闭
                    $this->modelTask->update(array('_id' => $task_id, 'push_status' => array('$nin' => $pushStatusExcludeList)), array('$set' => $data));

                    $data = array();
                    $data['push_status'] = $push_status; // 推送关闭
                    $modelTaskProcess = new \App\Qyweixin\Models\Notification\TaskProcess();
                    $modelTaskProcess->update(array('notification_task_id' => $task_id, 'push_status' => array('$nin' => $pushStatusExcludeList)), array('$set' => $data));

                    $data = array();
                    $data['push_status'] = $push_status; // 推送关闭
                    $modelTaskLog = new \App\Qyweixin\Models\Notification\TaskLog();
                    $modelTaskLog->update(array('notification_task_id' => $task_id, 'push_status' => array('$nin' => $pushStatusExcludeList)), array('$set' => $data));

                    $this->modelTask->commit();

                    $cache = $this->getDI()->get("cache");
                    // 加缓存处理
                    $cacheTime = 60 * 60 * 24; // 1天
                    $cache->save('qyweixin:notification:notification_task_id:' . $task_id, $push_status, $cacheTime);

                    // clear output buffer.
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')));
                } catch (\Exception $e) {
                    // DB::rollback();
                    $this->modelTask->rollback();
                    return $this->makeJsonError($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool($readonly = false)
    {
        $fields = array();
        $fields['notificationtask_provider_appid'] = array(
            'name' => '第三方服务商应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->providerItems,
                'readonly' => $readonly,
            ),
        );
        $fields['notificationtask_authorizer_appid'] = array(
            'name' => '授权方应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems,
                'readonly' => $readonly,
            ),
        );
        return $fields;
    }

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

        $schemas['userids'] = array(
            'name' => 'userid列表',
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
                'help' => 'userid的个数不要太大，尽量保持在一万个以内，并用逗号分隔',
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

        $schemas['userids_sql'] = array(
            'name' => '获取userid的sql文',
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
                'help' => '通过sql文获取userid列表',
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

        $schemas['userids_file'] = array(
            'name' => 'userid列表CSV文件',
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
                'help' => '通过上传csv文件的方式获取userid列表，适合userid个数比较大的场景使用',
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
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $pushStatusOptions,
                'help' => '推送状态 0:待推送 1:推送中 2:推送完成 5:推送关闭',
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
        return '企业推送任务';
    }

    protected function getModel()
    {
        return $this->modelTask;
    }

    protected function checkChangemsginfoCallbackIsValid($changemsginfo_callback)
    {
        // 如果没有设置回调函数的话 那么就直接返回
        if (empty($changemsginfo_callback)) {
            return true;
        } else {
            $changemsginfo_callback_info = \json_decode($changemsginfo_callback);
            // 如果不是有效合法的json格式的话就直接返回
            if (empty($changemsginfo_callback_info)) {
                return false;
            } else {
                $className = empty($changemsginfo_callback_info['class']) ? "" : trim($changemsginfo_callback_info['class']);
                $methodName = empty($changemsginfo_callback_info['method']) ? "" : trim($changemsginfo_callback_info['method']);

                if (empty($className)) {
                    return is_callable($methodName);
                } else {
                    $anObject  = new $className();
                    $methodVariable  = array($anObject,  $methodName);
                    return is_callable($methodVariable);
                }
            }
        }
    }
}
