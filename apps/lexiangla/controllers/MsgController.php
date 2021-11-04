<?php

namespace App\Lexiangla\Controllers;

/**
 * 事件回调
 * https://lexiangla.com/wiki/api/20000.html
 */
class MsgController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;

    /**
     * @var \App\Lexiangla\Services\LexianglaService
     */
    private $serviceLexiangla = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        // 创建service
        $config = $this->getDI()->get('config');
        $lexianglaSettings = $config['lexiangla'];
        $this->serviceLexiangla = new \App\Lexiangla\Services\LexianglaService($lexianglaSettings['AppKey'], $lexianglaSettings['AppSecret']);
    }

    /**
     * 回调
     * 接入指南
     * # 第一步：填写服务器配置
     * 由于目前这个功能处于内测阶段，不对外全开放。需要开通的企业请联系腾讯乐享客服，并提供该企业用于接收腾讯乐享回调请求的服务器URL，由腾讯乐享客服统一处理。 开通成功后，企业会得到一个 secret 用于服务器接收请求时验证消息来源合法性。
     *
     * # 第二步：验证消息是否来自腾讯乐享
     * 腾讯乐享回调请求的数据格式为 JSON 结构，如文档新增评论的回调请求数据如下：
     *
     * {
     * "nonce":"3072f432-bfc5-4e79-a24d-8fe4741da471",
     * "timestamp":1540368608,
     * "sign":"7b3d8492887e6c79b16cab7f093102d6dae5de5b",
     * "action":"doc/comment/add",
     * "attributes":{
     * "doc_id":"b252f800d76311e89ca37589846814b7",
     * "comment_id":"39080d18d76411e897a87ba398858639",
     * "content":"乐享内文档评论的内容"
     * },
     * "operator":"StaffID",
     * "operator_info":{"name":"StaffID"}
     * }
     * 每个回调请求都会带上 nonce, timestamp, sign 三个参数用于验证消息来源是否合法。结合第一步得到的 secret，可使用以下方法验证：
     *
     * 判断 sign == sha1(nonce . secret . timestamp)，若返回 true 则消息来源合法。
     *
     * # 第三步：结合业务参数和文档处理请求
     * 回调请求的业务参数包括：action、 attributes、 operator，部分业务回调会包含 is_anonymous
     *
     * # 参数说明：
     * 参数 说明
     * action 员工在腾讯乐享平台上触发的事件类型
     * 并非所有事件都会回调，以目前文档提供的为准
     * attributes 用户触发的事件具体的内容和属性
     * operator 触发者StaffID，是否明文展示取决于is_anonymous
     * is_anonymous 用户是否匿名操作
     */
    public function callbackAction()
    {
        // http://www.myapplicationmodule.com/lexiangla/api/msg/callback
        try {
            $postStr = file_get_contents('php://input');
            $msgArr = json_decode($postStr, true);
            if (empty($msgArr)) {
                return $this->result("success");
            }
            $nonce  = $msgArr['nonce'];
            $timestamp  = $msgArr['timestamp'];
            $operator  = $msgArr['operator'];
            $action  = $msgArr['action'];

            $cacheKey1 = 'lexiangla:msg_callback:' . "{$operator}|{$action}|{$timestamp}|{$nonce}";
            $objLock = new \iLock(md5($cacheKey1), false);
            if ($objLock->lock()) {
                return $this->result("success");
            }

            // 每个回调请求都会带上 nonce, timestamp, sign 三个参数用于验证消息来源是否合法。结合第一步得到的 secret，可使用以下方法验证：
            // 判断 sign == sha1(nonce . secret . timestamp)，若返回 true 则消息来源合法。
            if ($action == 'message/push') {
                $applicationInfo = $this->serviceLexiangla->getApplicationInfo();
                $this->secret = $applicationInfo['secret'];
                if (sha1($msgArr['nonce'] . $this->secret . $msgArr['timestamp']) == $msgArr['sign']) {
                    // TaskModel::log(TaskModel::TASK_LEXIANGLA_MESSAGE_PUSH, $msgArr, []);                    
                }
            }
            return $this->result("success");
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }
}
