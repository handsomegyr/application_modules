<?php
class WeixinnotificationsendTask extends \Phalcon\CLI\Task
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weixin2:notification_task_send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '消息推送任务发送处理';

    // 监控任务
    private $activity_id = 1;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixinnotificationsend handle 5e46128f69dc0a0f8415fe8f.csv
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
            $modelTaskProcess = new \App\Weixin2\Models\Notification\TaskProcess();

            try {
                $modelTaskProcess->begin();

                // 1 在推送任务处理表中获取一条需要处理的推送任务记录并且进行锁住
                if (empty($task_id)) {
                    $taskProcessInfo = $modelTaskProcess->getAndLockOneTask4ByPushStatus(\App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
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
            $lock = new \iLock('weixin2:task_id:' . $taskProcessInfo['notification_task_id']);
            $lock->setExpire(3600);
            if ($lock->lock()) {
                throw new \Exception("taskid为{$taskProcessInfo['notification_task_id']}所对应的推送中的任务在处理中，请等待");
            }

            // 2 获取推送任务信息
            $modelTask = new \App\Weixin2\Models\Notification\Task();
            $taskInfo = $modelTask->getInfoById($taskProcessInfo['notification_task_id']);
            // 如果没有找到的话报错
            if (empty($taskInfo)) {
                // 处理状态已完成
                $modelTaskProcess->updatePushState($taskProcessInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_FAIL, $now);
                throw new \Exception("未找到taskid为{$taskProcessInfo['notification_task_id']}所对应的推送任务");
            }
            // 检查推送状态
            if ($taskInfo['push_status'] != \App\Weixin2\Models\Notification\TaskProcess::PUSHING) {
                // 处理状态已完成
                $modelTaskProcess->updatePushState($taskProcessInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_OVER, $now);
                throw new \Exception("taskid为{$taskProcessInfo['notification_task_id']}所对应的推送任务的推送状态不在推送中");
            }

            // 4 获取该任务下的所有推送中的任务日志
            $modelTaskLog = new \App\Weixin2\Models\Notification\TaskLog();
            $taskLogList = $modelTaskLog->getAndLockListByTaskId($taskInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING);
            if (empty($taskLogList)) {
                // 处理状态已完成
                $modelTaskProcess->updatePushState($taskProcessInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_OVER, $now);
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
                        if ($taskLog['push_status'] != \App\Weixin2\Models\Notification\TaskProcess::PUSHING) {
                            $modelTaskProcess->rollback();
                            continue;
                        }

                        // 检查任务的推送状态
                        $taskPushState4InCache = $cache->get('weixin2:notification:notification_task_id:' . $taskInfo['_id']);
                        $taskPushState = intval($taskPushState4InCache);
                        if (!empty($taskPushState)) {
                            // 如果是关闭状态的话就直接返回了
                            if ($taskPushState == \App\Weixin2\Models\Notification\TaskProcess::PUSH_CLOSE) {
                                $modelTaskProcess->rollback();
                                continue;
                            }
                        }

                        // 创建service
                        $weixinopenService = new \App\Weixin2\Services\WeixinService($taskLog['authorizer_appid'], $taskLog['component_appid']);

                        // 推送方式
                        if ($taskLog['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_TEMPLATEMSG) { // 1:模板消息

                            // 根据模板消息记录ID获取模板消息配置
                            $modelTemplateMsg = new \App\Weixin2\Models\TemplateMsg\TemplateMsg();
                            $templateMsgInfo = $modelTemplateMsg->getInfoById($taskLog['template_msg_id']);
                            if (empty($templateMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},模板消息记录ID:{$taskLog['template_msg_id']}所对应的记录不存在");
                            }
                            $templateMsgInfo = $modelTaskLog->changeMsgInfo($taskLog, $templateMsgInfo);

                            // 发送模板消息
                            $ret = $weixinopenService->sendTemplateMsg("", $taskLog['openid'], $templateMsgInfo, $match);
                        } elseif ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_MASSMSG) { // 2:群发消息

                            // 获取群发消息发送方式信息
                            $modelMassMsgSendMethod = new \App\Weixin2\Models\MassMsg\SendMethod();
                            $sendMethodInfo = $modelMassMsgSendMethod->getInfoById($taskLog['mass_msg_send_method_id']);
                            if (empty($sendMethodInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},群发消息发送方式记录ID:{$taskLog['mass_msg_send_method_id']}所对应的记录不存在");
                            }

                            // 根据群发消息记录ID获取群发消息配置
                            $modelMassMsg = new \App\Weixin2\Models\MassMsg\MassMsg();
                            $massMsgInfo = $modelMassMsg->getInfoById($taskLog['mass_msg_id']);
                            if (empty($massMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},群发消息记录ID:{$taskLog['mass_msg_id']}所对应的记录不存在");
                            }

                            $tag_id = "";
                            $openids = array();

                            // 按照tag_id发送的话
                            if ($sendMethodInfo['send_method'] == \App\Weixin2\Models\MassMsg\SendMethod::SEND_BY_TAGID) {
                                if (empty($taskLog['tag_id'])) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['_id']},tag_id字段的值为空");
                                }
                                $tag_id = $taskLog['tag_id'];
                            } elseif ($sendMethodInfo['send_method'] == \App\Weixin2\Models\MassMsg\SendMethod::SEND_BY_OPENIDS) {
                                // 按照openids列表发送
                                if (empty($taskLog['openids']) && empty($taskLog['openid'])) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['_id']},openids字段和openid字段的值都为空");
                                }
                                if (!empty($taskLog['openids'])) {
                                    $openids = explode(',', $taskLog['openids']);
                                } else {
                                    $openids[] = $taskLog['openid'];
                                }
                                if (empty($openids)) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['_id']},openids字段的值为空");
                                }
                            }

                            // 发送群发消息
                            $match['mass_msg_type'] = $massMsgInfo['msg_type'];
                            $ret = $weixinopenService->sendMassMsg($tag_id, $openids, $massMsgInfo, $sendMethodInfo, $match, true);
                        } elseif ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_CUSTOMMSG) { // 3:客服消息

                            // 根据客服消息记录ID获取客服消息配置
                            $modelCustomMsg = new \App\Weixin2\Models\CustomMsg\CustomMsg();
                            $customMsgInfo = $modelCustomMsg->getInfoById($taskLog['custom_msg_id']);
                            if (empty($customMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},客服消息记录ID:{$taskLog['custom_msg_id']}所对应的记录不存在");
                            }
                            $customMsgInfo = $modelTaskLog->changeMsgInfo($taskLog, $customMsgInfo);

                            // 发送客服消息
                            $match['custom_msg_type'] = $customMsgInfo['msg_type'];
                            $ret = $weixinopenService->sendCustomMsg("", $taskLog['openid'], $customMsgInfo, $match);
                        } elseif ($taskLog['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_SUBSCRIBETEMPLATEMSG4MINIPROGRAM) { // 4:小程序订阅消息

                            // 根据订阅模板消息记录ID获取订阅模板消息配置
                            $modelSubscribeMsg = new \App\Weixin2\Models\Miniprogram\SubscribeMsg\Msg();
                            $subscribeMsgInfo = $modelSubscribeMsg->getInfoById($taskInfo['subscribe_msg_id']);
                            if (empty($subscribeMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},订阅消息记录ID:{$taskLog['subscribe_msg_id']}所对应的记录不存在");
                            }
                            $subscribeMsgInfo = $modelTaskLog->changeMsgInfo($taskLog, $subscribeMsgInfo);

                            // 发送订阅模板消息
                            $ret = $weixinopenService->sendMicroappSubscribeMsg("", $taskLog['openid'], $subscribeMsgInfo, $match);
                        } elseif ($taskLog['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_UNIFORMMSG4MINIPROGRAM) { // 5:小程序统一服务消息

                            // 根据模板消息记录ID获取模板消息配置
                            $modelTemplateMsg = new \App\Weixin2\Models\TemplateMsg\TemplateMsg();
                            $templateMsgInfo = $modelTemplateMsg->getInfoById($taskLog['template_msg_id']);
                            if (empty($templateMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['_id']},公众号模板消息记录ID:{$taskLog['template_msg_id']}所对应的记录不存在");
                            }
                            $templateMsgInfo = $modelTaskLog->changeMsgInfo($taskLog, $templateMsgInfo);

                            // 发送模板消息
                            $ret = $weixinopenService->sendMicroappUniformMsg("", $taskLog['openid'], $templateMsgInfo, $match);
                        }

                        // 记录发送结果
                        if ($ret['is_ok']) {
                            // 成功的话
                            $status = \App\Weixin2\Models\Notification\TaskProcess::PUSH_SUCCESS;
                        } else {
                            // 失败的话
                            $status = \App\Weixin2\Models\Notification\TaskProcess::PUSH_FAIL;
                        }

                        // 更新状态
                        $modelTaskLog->updatePushState($taskLog['_id'], $status, $now, $ret['is_ok'], $ret['api_ret'], 1);

                        // 是否成功了
                        $is_success = ($status == \App\Weixin2\Models\Notification\TaskProcess::PUSH_SUCCESS);
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
                $taskLogList = $modelTaskLog->getAndLockListByTaskId($taskInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING);
                // 没有的话就算完成推送
                if (empty($taskLogList)) {
                    // 处理状态已完成
                    $modelTaskProcess->updatePushState($taskProcessInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_OVER, $now);
                    // 推送状态已完成
                    $modelTask->updatePushState($taskInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_OVER, $now);
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
