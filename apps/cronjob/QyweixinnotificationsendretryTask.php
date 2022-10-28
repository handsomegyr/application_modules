<?php

class QyweixinnotificationsendretryTask extends \Phalcon\CLI\Task
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qyweixin:notification_task_send_retry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '企业微信消息推送任务尝试再次发送处理';

    // 监控任务
    private $activity_id = 6;

    // 最大尝试次数
    private $max_try_num = 5;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixinnotificationsendretry handle 5e46128f69dc0a0f8415fe8f.csv
     * @param array $params
     */
    public function handleAction()
    {
        $params = $this->dispatcher->getParams();

        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $task_id = empty($params[0]) ? '' : $params[0];
        if (empty($task_id)) {
            $task_id = "";
        }

        try {
            $modelTaskProcess = new \App\Qyweixin\Models\Notification\TaskProcess();
            $modelTask = new \App\Qyweixin\Models\Notification\Task();

            // 1 获取所有推送失败的任务日志
            $modelTaskLog = new \App\Qyweixin\Models\Notification\TaskLog();
            $taskLogList = $modelTaskLog->getRetryList($this->max_try_num, \App\Qyweixin\Models\Notification\TaskProcess::PUSH_FAIL);

            // 如果没有的话直接返回
            if (empty($taskLogList)) {
                return;
            }

            // 2 循环处理任务日志记录
            if (!empty($taskLogList)) {

                foreach ($taskLogList as $taskLogItem) {

                    // 进行锁定处理 ???
                    $lock = new \iLock('qyweixin:task_log_id:' . $taskLogItem['_id']);
                    $lock->setExpire(3600);
                    if ($lock->lock()) {
                        throw new \Exception("task_log_id为{$taskLogItem['_id']}所对应的任务日志在处理中，请等待");
                    }

                    $match = array();
                    $match['_id'] = 0;
                    $match['keyword'] = "";

                    try {
                        $modelTaskLog->begin();

                        // 锁定记录ID
                        $taskLog = $modelTaskLog->lockLog($taskLogItem['_id']);

                        // 如果处理次数超过了某个阈值的话 就不用处理了
                        if ($taskLog['process_num'] >= $this->max_try_num) {
                            // 直接设置失败
                            $status = \App\Qyweixin\Models\Notification\TaskProcess::PUSH_FAIL;
                            $ret = array();
                            $ret['is_ok'] = true;
                            $ret['api_ret'] = array();
                        } else {

                            // 推送方式 1:发送应用消息 2:发送消息到群聊会话 3:发送互联企业消息 4:发送企业群发消息
                            if ($taskLog['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_AGENT_MESSAGE) { // 1:发送应用消息

                                // 根据应用消息记录ID获取应用消息配置
                                $modelAgentMsg = new \App\Qyweixin\Models\AgentMsg\AgentMsg();
                                $agentMsgInfo = $modelAgentMsg->getInfoById($taskLog['agent_msg_id']);
                                if (empty($agentMsgInfo)) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['_id']},应用消息记录ID:{$taskLog['agent_msg_id']}所对应的记录不存在");
                                }
                                $agentMsgInfo = $modelTaskLog->changeMsgInfo($taskLog, $agentMsgInfo);
                                // 发送应用消息
                                // 创建service
                                $match['agent_msg_type'] = $agentMsgInfo['msg_type'];
                                $QyweixinService = new \App\Qyweixin\Services\QyService($taskLog['authorizer_appid'], $taskLog['provider_appid'], $agentMsgInfo['agentid']);
                                $ret = $QyweixinService->sendAgentMsg("", $taskLog['userid'], $agentMsgInfo, $match);
                            } elseif ($taskLog['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_APPCHAT) { // 2:发送消息到群聊会话

                                // 根据消息到群聊会话记录ID获取消息到群聊会话配置
                                $modelAppchatMsg = new \App\Qyweixin\Models\AppchatMsg\AppchatMsg();
                                $appchatInfo = $modelAppchatMsg->getInfoById($taskLog['appchat_msg_id']);
                                if (empty($sendMethodInfo)) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['_id']},消息到群聊会话记录ID:{$taskLog['appchat_msg_id']}所对应的记录不存在");
                                }
                                $appchatInfo = $modelTaskLog->changeMsgInfo($taskLog, $appchatInfo);
                                // 创建service
                                $match['appchat_msg_type'] = $appchatInfo['msg_type'];
                                $QyweixinService = new \App\Qyweixin\Services\QyService($taskLog['authorizer_appid'], $taskLog['provider_appid'], $appchatInfo['agentid']);
                                $ret = $QyweixinService->sendAppchatMsg("", $taskLog['userid'], $appchatInfo, $match);
                            } elseif ($taskLog['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_LINKEDCORP_MESSAGE) { // 3:发送互联企业消息

                                // 根据互联企业消息记录ID获取互联企业消息配置
                                $modelLinkedcorpMsg = new \App\Qyweixin\Models\LinkedcorpMsg\LinkedcorpMsg();
                                $linkedcorpMsgInfo = $modelLinkedcorpMsg->getInfoById($taskLog['linkedcorp_msg_id']);
                                if (empty($linkedcorpMsgInfo)) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['_id']},客服消息记录ID:{$taskLog['custom_msg_id']}所对应的记录不存在");
                                }
                                $linkedcorpMsgInfo = $modelTaskLog->changeMsgInfo($taskLog, $linkedcorpMsgInfo);

                                // 创建service
                                $match['linkedcorp_msg_type'] = $linkedcorpMsgInfo['msg_type'];
                                $QyweixinService = new \App\Qyweixin\Services\QyService($taskLog['authorizer_appid'], $taskLog['provider_appid'], $linkedcorpMsgInfo['agentid']);
                                $ret = $QyweixinService->sendLinkedcorpMsg("", $taskLog['userid'], $linkedcorpMsgInfo, $match);
                            } elseif ($taskLog['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_EXTERNALCONTACT_ADD_MSG_TEMPLATE) { // 4:发送企业群发消息

                                // 根据企业群发消息记录ID获取企业群发消息配置
                                $modelMsgTemplate = new \App\Qyweixin\Models\ExternalContact\MsgTemplate();
                                $msgTemplateInfo = $modelMsgTemplate->getInfoById($taskLog['externalcontact_msg_template_id']);
                                if (empty($msgTemplateInfo)) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['_id']},企业群发消息记录ID:{$taskLog['externalcontact_msg_template_id']}所对应的记录不存在");
                                }
                                $msgTemplateInfo = $modelTaskLog->changeMsgInfo($taskLog, $msgTemplateInfo);
                                // 发送企业群发消息
                                $match['msg_template_chat_type'] = $msgTemplateInfo['chat_type'];
                                $QyweixinService = new \App\Qyweixin\Services\QyService($taskLog['authorizer_appid'], $taskLog['provider_appid'], $msgTemplateInfo['agentid']);
                                $ret = $QyweixinService->addMsgTemplate("", $taskLog['userid'], $msgTemplateInfo, $match);
                            }

                            // 记录发送结果
                            if ($ret['is_ok']) {
                                // 成功的话
                                $status = \App\Qyweixin\Models\Notification\TaskProcess::PUSH_SUCCESS;
                            } else {
                                // 失败的话
                                // 要根据错误码来决定 比如说accesstoken过期了这种错误是临时的,可以进行重试 还有一种错误是该userid不存在或已取消关注 这种错误就不需要进行重试
                                $status = \App\Qyweixin\Models\Notification\TaskProcess::PUSH_FAIL;
                            }
                        }

                        // 更新状态
                        $modelTaskLog->updatePushState($taskLog['_id'], $status, $now, $ret['is_ok'], $ret['api_ret'], 1);

                        // 如果成功了或失败了
                        if ($status == \App\Qyweixin\Models\Notification\TaskProcess::PUSH_SUCCESS) {
                            // 推送任务 更新成功处理件数
                            $modelTask->incSuccessNum($taskLog['notification_task_id'], 1);
                            // 推送任务处理 更新成功处理件数
                            $modelTaskProcess->incSuccessNum($taskLog['notification_task_process_id'], 1);
                        }
                        $modelTaskLog->commit();
                    } catch (\Exception $e) {
                        $modelTaskLog->rollback();
                        $modelActivityErrorLog->log($this->activity_id, $e, $now);
                    }
                }
            }
        } catch (\Exception $e) {
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }
    }
}
