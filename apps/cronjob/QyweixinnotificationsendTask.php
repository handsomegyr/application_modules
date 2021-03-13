<?php
class QyweixinnotificationsendTask extends \Phalcon\CLI\Task
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qyweixin:notification_task_send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '企业微信消息推送任务发送处理';

    // 监控任务
    private $activity_id = 1;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixinnotificationsend handle 5e46128f69dc0a0f8415fe8f.csv
     * @param array $params            
     */
    public function handleAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $task_id = empty($params[0]) ? '' : $params[0];
        if (empty($task_id)) {
            $task_id = "";
        }

        try {
            $modelTaskProcess = new \App\Qyweixin\Models\Notification\TaskProcess();

            try {
                $modelTaskProcess->begin();

                // 1 在推送任务处理表中获取一条需要处理的推送任务记录并且进行锁住
                if (empty($task_id)) {
                    $taskProcessInfo = $modelTaskProcess->getAndLockOneTask4ByPushStatus(\App\Qyweixin\Models\Notification\TaskProcess::PUSHING, $now);
                } else {
                    $taskProcessInfo = $modelTaskProcess->getAndLockOneTask4ByTaskid($task_id, $now);
                }

                // 没有的话
                if (empty($taskProcessInfo)) {
                    $modelTaskProcess->rollback();
                    return;
                }

                $modelTaskProcess->commit();
            } catch (\Exception $e) {
                $modelTaskProcess->rollback();
                $modelActivityErrorLog->log($this->activity_id, $e, $now);
            }

            // 进行锁定处理 ???
            $lock = new \iLock('qyweixin:task_id:' . $taskProcessInfo['notification_task_id']);
            $lock->setExpire(3600);
            if ($lock->lock()) {
                throw new \Exception("taskid为{$taskProcessInfo['notification_task_id']}所对应的推送中的任务在处理中，请等待");
            }

            // 2 获取推送任务信息
            $modelTask = new \App\Qyweixin\Models\Notification\Task();
            $taskInfo = $modelTask->getInfoById($taskProcessInfo['notification_task_id']);
            // 如果没有找到的话报错
            if (empty($taskInfo)) {
                // 处理状态已完成
                $modelTaskProcess->updatePushState($taskProcessInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSH_FAIL, $now);
                throw new \Exception("未找到taskid为{$taskProcessInfo['notification_task_id']}所对应的推送任务");
            }
            // 检查推送状态
            if ($taskInfo['push_status'] != \App\Qyweixin\Models\Notification\TaskProcess::PUSHING) {
                // 处理状态已完成
                $modelTaskProcess->updatePushState($taskProcessInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSH_OVER, $now);
                throw new \Exception("taskid为{$taskProcessInfo['notification_task_id']}所对应的推送任务的推送状态不在推送中");
            }

            // 4 获取该任务下的所有推送中的任务日志
            $modelTaskLog = new \App\Qyweixin\Models\Notification\TaskLog();
            $taskLogList = $modelTaskLog->getAndLockListByTaskId($taskInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSHING);
            if (empty($taskLogList)) {
                // 处理状态已完成
                $modelTaskProcess->updatePushState($taskProcessInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSH_OVER, $now);
                throw new \Exception("未找到任务ID:{$taskInfo['_id']}的推送任务所对应的推送任务日志内容");
            }

            $cache = $this->getDI()->get("cache");

            // 5 循环处理任务日志记录
            if (!empty($taskLogList)) {

                foreach ($taskLogList as $taskLogItem) {

                    $match = array();
                    $match['_id'] = 0;
                    $match['keyword'] = "";
                    $ret = array();
                    try {
                        $modelTaskProcess->begin();

                        // 锁定记录ID
                        $taskLog = $modelTaskLog->lockLog($taskLogItem['_id']);

                        // 再次检查推送状态 如果不是推送中的状态的话就不做处理
                        if ($taskLog['push_status'] != \App\Qyweixin\Models\Notification\TaskProcess::PUSHING) {
                            $modelTaskProcess->rollback();
                            continue;
                        }

                        // 检查任务的推送状态
                        $taskPushState4InCache = $cache->get('qyweixin:notification:notification_task_id:' . $taskInfo['_id']);
                        $taskPushState = intval($taskPushState4InCache);
                        if (!empty($taskPushState)) {
                            // 如果是关闭状态的话就直接返回了
                            if ($taskPushState == \App\Qyweixin\Models\Notification\TaskProcess::PUSH_CLOSE) {
                                $modelTaskProcess->rollback();
                                continue;
                            }
                        }

                        // 推送方式 1:发送应用消息 2:发送消息到群聊会话 3:发送互联企业消息 4:发送企业群发消息
                        if ($taskLog['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_AGENT_MESSAGE) { // 1:发送应用消息

                            // 根据应用消息记录ID获取应用消息配置
                            $modelAgentMsg = new \App\Qyweixin\Models\AgentMsg\AgentMsg();
                            $agentMsgInfo = $modelAgentMsg->getInfoById($taskInfo['agent_msg_id']);
                            if (empty($agentMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},应用消息记录ID:{$taskLog['agent_msg_id']}所对应的记录不存在");
                            }
                            $agentMsgInfo = $modelTaskLog->changeMsgInfo($taskLog, $agentMsgInfo);

                            // 发送应用消息
                            // 创建service
                            $QyweixinService = new \App\Qyweixin\Services\QyService($taskLog['authorizer_appid'], $taskLog['provider_appid'], $agentMsgInfo['agentid']);
                            $ret = $QyweixinService->sendAgentMsg("", $taskLog['userid'], $agentMsgInfo, $match);
                        } elseif ($taskInfo['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_APPCHAT) { // 2:发送消息到群聊会话

                            // 根据消息到群聊会话记录ID获取消息到群聊会话配置
                            $modelAppchatMsg = new \App\Qyweixin\Models\AppchatMsg\AppchatMsg();
                            $appchatInfo = $modelAppchatMsg->getInfoById($taskLog['appchat_msg_id']);
                            if (empty($appchatInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},消息到群聊会话记录ID:{$taskLog['appchat_msg_id']}所对应的记录不存在");
                            }
                            $appchatInfo = $modelTaskLog->changeMsgInfo($taskLog, $appchatInfo);

                            $QyweixinService = new \App\Qyweixin\Services\QyService($taskLog['authorizer_appid'], $taskLog['provider_appid'], $appchatInfo['agentid']);
                            $ret = $QyweixinService->sendAppchatMsg("", $taskLog['userid'], $appchatInfo, $match);
                        } elseif ($taskInfo['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_LINKEDCORP_MESSAGE) { // 3:发送互联企业消息

                            // 根据互联企业消息记录ID获取互联企业消息配置
                            $modelLinkedcorpMsg = new \App\Qyweixin\Models\LinkedcorpMsg\LinkedcorpMsg();
                            $linkedcorpMsgInfo = $modelLinkedcorpMsg->getInfoById($taskLog['linkedcorp_msg_id']);
                            if (empty($linkedcorpMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},互联企业消息记录ID:{$taskLog['linkedcorp_msg_id']}所对应的记录不存在");
                            }
                            $linkedcorpMsgInfo = $modelTaskLog->changeMsgInfo($taskLog, $linkedcorpMsgInfo);

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
                            $QyweixinService = new \App\Qyweixin\Services\QyService($taskLog['authorizer_appid'], $taskLog['provider_appid'], $msgTemplateInfo['agentid']);
                            // $ret = $QyweixinService->sendMicroappSubscribeMsg("", $taskLog['userid'], $msgTemplateInfo, $match);
                        }

                        // 记录发送结果
                        if ($ret['is_ok']) {
                            // 成功的话
                            $status = \App\Qyweixin\Models\Notification\TaskProcess::PUSH_SUCCESS;
                        } else {
                            // 失败的话
                            $status = \App\Qyweixin\Models\Notification\TaskProcess::PUSH_FAIL;
                        }

                        // 更新状态
                        $modelTaskLog->updatePushState($taskLog['_id'], $status, $now, $ret['is_ok'], $ret['api_ret'], 1);

                        // 是否成功了
                        $is_success = ($status == \App\Qyweixin\Models\Notification\TaskProcess::PUSH_SUCCESS);
                        // 推送任务 更新成功处理件数和已处理件数
                        $modelTask->incProcessedNum($taskLog['notification_task_id'], 1, $is_success);
                        // 推送任务处理 更新成功处理件数和已处理件数
                        $modelTaskProcess->incProcessedNum($taskLog['notification_task_process_id'], 1, $is_success);
                        $modelTaskProcess->commit();
                    } catch (\Exception $e) {
                        $modelTaskProcess->rollback();
                        $modelActivityErrorLog->log($this->activity_id, $e, $now);
                    }
                }
            }

            // 4 修改处理状态为已完成
            try {
                $modelTaskProcess->begin();

                // 是否还有未处理的数据
                $taskLogList = $modelTaskLog->getAndLockListByTaskId($taskInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSHING);
                // 没有的话就算完成推送
                if (empty($taskLogList)) {
                    // 处理状态已完成
                    $modelTaskProcess->updatePushState($taskProcessInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSH_OVER, $now);
                    // 推送状态已完成
                    $modelTask->updatePushState($taskInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSH_OVER, $now);
                }

                $modelTaskProcess->commit();
            } catch (\Exception $e) {
                $modelTaskProcess->rollback();
                $modelActivityErrorLog->log($this->activity_id, $e, $now);
            }
        } catch (\Exception $e) {
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }
    }
}
