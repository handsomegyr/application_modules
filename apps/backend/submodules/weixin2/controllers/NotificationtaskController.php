<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Notification\Task;

use App\Backend\Submodules\Weixin2\Models\MassMsg\SendMethod;
use App\Backend\Submodules\Weixin2\Models\MassMsg\MassMsg;
use App\Backend\Submodules\Weixin2\Models\TemplateMsg\TemplateMsg;
use App\Backend\Submodules\Weixin2\Models\CustomMsg\CustomMsg;
use App\Backend\Submodules\Weixin2\Models\User\Tag;
use App\Backend\Submodules\Weixin2\Models\Miniprogram\SubscribeMsg\Msg;

/**
 * @title({name="推送任务"})
 *
 * @name 推送任务
 */
class NotificationtaskController extends BaseController
{
    private $modelTask;

    private $modelSendMethod;
    private $modelMassMsg;
    private $modelTemplateMsg;
    private $modelCustomMsg;
    private $modelSubscribeMsg;

    public function initialize()
    {
        $this->modelTask = new Task();
        $this->modelSendMethod = new SendMethod();
        $this->modelMassMsg = new MassMsg();
        $this->modelTemplateMsg = new TemplateMsg();
        $this->modelCustomMsg = new CustomMsg();
        $this->modelUserTag = new Tag();
        $this->modelSubscribeMsg = new Msg();

        $this->sendMethodItems = $this->modelSendMethod->getAll();
        $this->massMsgItems = $this->modelMassMsg->getAllByType("", "_id");
        $this->templateMsgItems = $this->modelTemplateMsg->getAll();
        $this->customMsgItems = $this->modelCustomMsg->getAllByType("", "_id");
        $this->userTagItems = $this->modelUserTag->getAllByType("tag_id");
        $this->subscribeMsgItems = $this->modelSubscribeMsg->getAll();

        parent::initialize();
    }

    protected $sendMethodItems = null;
    protected $massMsgItems = null;
    protected $templateMsgItems = null;
    protected $customMsgItems = null;
    protected $userTagItems = null;
    protected $subscribeMsgItems = null;


    protected function getHeaderTools2($tools)
    {
        $tools['addtask4'] = array(
            'title' => '追加按小程序订阅消息发送任务',
            'action' => 'addtask4',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        $tools['addtask5'] = array(
            'title' => '追加按小程序统一服务消息发送任务',
            'action' => 'addtask5',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['modifytask4'] = array(
            'title' => '修改按小程序订阅消息发送任务',
            'action' => 'modifytask4',
            'is_show' => function ($row) {
                if (intval($row['push_status']) === 0) {
                    if ($row['notification_method'] == 4) {
                        return true;
                    }
                }
                return false;
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['modifytask5'] = array(
            'title' => '修改按小程序统一服务消息发送任务',
            'action' => 'modifytask5',
            'is_show' => function ($row) {
                if (intval($row['push_status']) === 0) {
                    if ($row['notification_method'] == 5) {
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
                    \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_OVER,
                    \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_SUCCESS,
                    \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_FAIL,
                    \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_CLOSE,
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
     * @title({name="追加按小程序订阅消息发送任务"})
     *
     * @name 追加按小程序订阅消息发送任务
     */
    public function addtask4Action()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/notificationtask/addtask4?id=xxx
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
                $fields['subscribe_msg_id'] = array(
                    'name' => '订阅消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->subscribeMsgItems,
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

                $fields['openids'] = array(
                    'name' => 'openid列表，逗号分隔',
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
                $fields['openids_sql'] = array(
                    'name' => '获取openid的sql文，在select语句中必须要有openid字段',
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
                $fields['openids_file'] = array(
                    'name' => '请选择一个首列为openid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "追加按小程序订阅消息发送任务";
                $row = array();
                $row['scheduled_push_time'] = getCurrentTime();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('notificationtask_component_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $subscribe_msg_id = trim($this->request->get('subscribe_msg_id')); // 订阅消息记录ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $openids = trim($this->request->get('openids')); // openid列表
                $openids_sql = trim($this->request->get('openids_sql')); // 获取openid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($subscribe_msg_id)) {
                    return $this->makeJsonError("订阅消息记录ID未指定");
                }
                $isValid = $this->modelTask->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
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
                // if (empty($component_appid)) {
                //     return $this->makeJsonError("第三方平台应用ID未设定");
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
                $openids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['openids_file'])) {
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

                    $file = $uploadFiles['openids_file'];
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
                    $openids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $openids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($openids) && empty($openids_sql) && empty($openids_file)) {
                    return $this->makeJsonError("openid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['component_appid'] = $component_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    // 小程序订阅消息发送
                    $data['notification_method'] = 4;
                    $data['mass_msg_send_method_id'] = 0;
                    $data['subscribe_msg_id'] = $subscribe_msg_id;
                    $data['changemsginfo_callback'] = $changemsginfo_callback;
                    $data['template_msg_id'] = 0;
                    $data['mass_msg_id'] = 0;
                    $data['custom_msg_id'] = 0;
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['tag_id'] = 0;
                    $data['openids'] = $openids;
                    $data['openids_sql'] = $openids_sql;
                    $data['openids_file'] = $openids_file;
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
     * @title({name="追加按小程序统一服务消息发送任务"})
     *
     * @name 追加按小程序统一服务消息发送任务
     */
    public function addtask5Action()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/notificationtask/addtask5?id=xxx
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
                $fields['template_msg_id'] = array(
                    'name' => '公众号模板消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->templateMsgItems,
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
                $fields['openids'] = array(
                    'name' => 'openid列表，逗号分隔',
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
                $fields['openids_sql'] = array(
                    'name' => '获取openid的sql文，在select语句中必须要有openid字段',
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
                $fields['openids_file'] = array(
                    'name' => '请选择一个首列为openid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "追加按小程序统一服务消息发送任务";
                $row = array();
                $row['scheduled_push_time'] = getCurrentTime();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('notificationtask_component_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $template_msg_id = trim($this->request->get('template_msg_id')); // 公众号模板消息ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $openids = trim($this->request->get('openids')); // openid列表
                $openids_sql = trim($this->request->get('openids_sql')); // 获取openid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($template_msg_id)) {
                    return $this->makeJsonError("公众号模板消息ID未指定");
                }
                $isValid = $this->modelTask->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
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
                // if (empty($component_appid)) {
                //     return $this->makeJsonError("第三方平台应用ID未设定");
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
                $openids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['openids_file'])) {
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

                    $file = $uploadFiles['openids_file'];
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
                    $openids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $openids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($openids) && empty($openids_sql) && empty($openids_file)) {
                    return $this->makeJsonError("openid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['component_appid'] = $component_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    // 小程序统一服务消息发送
                    $data['notification_method'] = 5;
                    $data['mass_msg_send_method_id'] = 0;
                    $data['subscribe_msg_id'] = 0;
                    $data['template_msg_id'] = $template_msg_id;
                    $data['changemsginfo_callback'] = $changemsginfo_callback;
                    $data['mass_msg_id'] = 0;
                    $data['custom_msg_id'] = 0;
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['tag_id'] = 0;
                    $data['openids'] = $openids;
                    $data['openids_sql'] = $openids_sql;
                    $data['openids_file'] = $openids_file;
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
     * @title({name="修改按小程序订阅消息发送任务"})
     *
     * @name 修改按小程序订阅消息发送任务
     */
    public function modifytask4Action()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/notificationtask/modifytask4?id=xxx
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
                $fields['subscribe_msg_id'] = array(
                    'name' => '订阅消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->subscribeMsgItems
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
                $fields['openids'] = array(
                    'name' => 'openid列表，逗号分隔',
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
                $fields['openids_sql'] = array(
                    'name' => '获取openid的sql文，在select语句中必须要有openid字段',
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
                $fields['openids_file'] = array(
                    'name' => '请选择一个首列为openid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "修改按小程序订阅消息发送任务";
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('notificationtask_component_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $subscribe_msg_id = trim($this->request->get('subscribe_msg_id')); // 订阅消息记录ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $openids = trim($this->request->get('openids')); // openid列表
                $openids_sql = trim($this->request->get('openids_sql')); // 获取openid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($subscribe_msg_id)) {
                    return $this->makeJsonError("订阅消息记录ID未指定");
                }
                $isValid = $this->modelTask->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
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
                // if (empty($component_appid)) {
                //     return $this->makeJsonError("第三方平台应用ID未设定");
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
                $openids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['openids_file'])) {
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

                    $file = $uploadFiles['openids_file'];
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
                    $openids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $openids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($openids) && empty($openids_sql) && empty($openids_file)) {
                    return $this->makeJsonError("openid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['component_appid'] = $component_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    $data['subscribe_msg_id'] = $subscribe_msg_id;
                    $data['changemsginfo_callback'] = $changemsginfo_callback;
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['openids'] = $openids;
                    $data['openids_sql'] = $openids_sql;
                    $data['openids_file'] = $openids_file;
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
     * @title({name="修改按小程序统一服务消息发送任务"})
     *
     * @name 修改按小程序统一服务消息发送任务
     */
    public function modifytask5Action()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/notificationtask/modifytask5?id=xxx
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
                $fields['template_msg_id'] = array(
                    'name' => '公众号模板消息ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->templateMsgItems
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
                $fields['openids'] = array(
                    'name' => 'openid列表，逗号分隔',
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
                $fields['openids_sql'] = array(
                    'name' => '获取openid的sql文，在select语句中必须要有openid字段',
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
                $fields['openids_file'] = array(
                    'name' => '请选择一个首列为openid内容的CSV文件',
                    'validation' => array(
                        'required' => false
                    ),
                    'form' => array(
                        'input_type' => 'file',
                        'is_show' => true
                    ),
                );
                $title = "修改按小程序订阅消息发送任务";
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('notificationtask_component_appid'));
                $authorizer_appid = trim($this->request->get('notificationtask_authorizer_appid'));

                $name = trim($this->request->get('name')); // 任务名
                $template_msg_id = intval($this->request->get('template_msg_id')); // 公众号模板消息ID
                $changemsginfo_callback = trim($this->request->get('changemsginfo_callback'));
                $openids = trim($this->request->get('openids')); // openid列表
                $openids_sql = trim($this->request->get('openids_sql')); // 获取openid的sql文
                $scheduled_push_time = trim($this->request->get('scheduled_push_time')); // 预定推送时间

                if (empty($name)) {
                    return $this->makeJsonError("任务名未指定");
                }
                if (empty($template_msg_id)) {
                    return $this->makeJsonError("公众号模板消息ID未指定");
                }
                $isValid = $this->modelTask->checkChangemsginfoCallbackIsValid($changemsginfo_callback);
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
                // if (empty($component_appid)) {
                //     return $this->makeJsonError("第三方平台应用ID未设定");
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
                $openids_file = "";
                $is_uploadFile = true;
                if (empty($uploadFiles) || !isset($uploadFiles['openids_file'])) {
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

                    $file = $uploadFiles['openids_file'];
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
                    $openids_file = rtrim($$uploadPath, '/') . "/{$newName}";
                    makeDir($uploadPath);
                    $isOk = move_uploaded_file($file['tmp_name'], $openids_file);
                    if (!$isOk) {
                        return $this->makeJsonError("文件移动发生错误");
                    }
                }

                if (empty($openids) && empty($openids_sql) && empty($openids_file)) {
                    return $this->makeJsonError("openid列表未指定");
                }

                try {
                    // DB::beginTransaction();
                    $this->modelTask->begin();

                    $data = array();
                    $data['component_appid'] = $component_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['name'] = $name;
                    $data['template_msg_id'] = $template_msg_id;
                    $data['changemsginfo_callback'] = $changemsginfo_callback;
                    $data['scheduled_push_time'] = \App\Common\Utils\Helper::getCurrentTime($scheduled_push_time);
                    $data['openids'] = $openids;
                    $data['openids_sql'] = $openids_sql;
                    $data['openids_file'] = $openids_file;
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
        // http://www.myapplicationmodule.com/admin/weixin2/notificationtask/closetask?id=xxx
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

                $fields['openids'] = array(
                    'name' => 'openid列表，逗号分隔',
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
                $fields['openids_sql'] = array(
                    'name' => '获取openid的sql文，在select语句中必须要有openid字段',
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
                $fields['openids_file'] = array(
                    'name' => '请选择一个首列为openid内容的CSV文件',
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

                    $push_status = \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_CLOSE;
                    $pushStatusExcludeList = array(
                        \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_OVER,
                        \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_SUCCESS,
                        \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_FAIL,
                        \App\Weixin2\Services\Models\Notification\TaskProcess::PUSH_CLOSE,
                    );
                    $task_id = $row['_id'];
                    $data = array();
                    $data['push_status'] = $push_status; // 推送关闭
                    $this->modelTask->update(array('_id' => $task_id, 'push_status' => array('$nin' => $pushStatusExcludeList)), array('$set' => $data));

                    $data = array();
                    $data['push_status'] = $push_status; // 推送关闭
                    $modelTaskProcess = new \App\Weixin2\Models\Notification\TaskProcess();
                    $modelTaskProcess->update(array('notification_task_id' => $task_id, 'push_status' => array('$nin' => $pushStatusExcludeList)), array('$set' => $data));

                    $data = array();
                    $data['push_status'] = $push_status; // 推送关闭
                    $modelTaskLog = new \App\Weixin2\Models\Notification\TaskLog();
                    $modelTaskLog->update(array('notification_task_id' => $task_id, 'push_status' => array('$nin' => $pushStatusExcludeList)), array('$set' => $data));

                    $data = array();
                    $data['push_status'] = $push_status; // 推送关闭
                    $modelTaskContent = new \App\Weixin2\Models\Notification\TaskContent();
                    $modelTaskContent->update(array('notification_task_id' => $task_id, 'push_status' => array('$nin' => $pushStatusExcludeList)), array('$set' => $data));

                    $this->modelTask->commit();

                    $cache = $this->getDI()->get("cache");
                    // 加缓存处理
                    $cacheTime = 60 * 60 * 24; // 1天
                    $cache->save('weixin2:notification:notification_task_id:' . $task_id, $push_status, $cacheTime);

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
        $fields['notificationtask_component_appid'] = array(
            'name' => '第三方平台应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems,
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
        $schemas['component_appid'] = array(
            'name' => '第三方平台应用ID',
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
        $notificationMethodOptions['4'] = "小程序订阅消息";
        $notificationMethodOptions['5'] = "小程序统一服务消息";

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
                'help' => '推送方式 1:模板消息 2:群发消息 3:客服消息 4:小程序订阅消息 5:小程序统一服务消息',
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
                'required' => false
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

        $schemas['subscribe_msg_id'] = array(
            'name' => '小程序消息记录ID',
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
                'items' => $this->subscribeMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->subscribeMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->subscribeMsgItems
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

        $schemas['openids'] = array(
            'name' => 'openid列表',
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
                'help' => 'openid的个数不要太大，尽量保持在一万个以内，并用逗号分隔',
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
                'help' => '通过sql文获取openid列表',
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
                'help' => '通过上传csv文件的方式获取openid列表，适合openid个数比较大的场景使用',
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
        return '推送任务';
    }

    protected function getModel()
    {
        return $this->modelTask;
    }
}
