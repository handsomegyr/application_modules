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
    private $activity_id = 6;

    // 最大尝试次数
    private $max_try_num = 5;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixinnotificationsend handle 5e46128f69dc0a0f8415fe8f.csv
     * @param array $params            
     */
    public function handleAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();

        try {
            $modelTaskProcess = new \App\Weixin2\Models\Notification\TaskProcess();

            try {
                $modelTaskProcess->begin();

                // 1 在推送任务处理表中获取一条需要处理的推送任务记录并且进行锁住
                $taskProcessInfo = $modelTaskProcess->getAndLockOneTask4ByPushStatus(\App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
                // 没有的话
                if (empty($taskProcessInfo)) {
                    return;
                }

                $modelTaskProcess->commit();
            } catch (\Exception $e) {
                $modelTaskProcess->rollback();
                $modelActivityErrorLog->log($this->activity_id, $e, $now);
            }

            // 进行锁定处理 ???
            $lock = new \iLock('task_id:' . $taskProcessInfo['notification_task_id']);
            $lock->setExpire(3600);
            if ($lock->lock()) {
                throw new \Exception("taskid为{$taskProcessInfo['notification_task_id']}所对应的推送中的任务在处理中，请等待");
            }

            // 2 获取推送任务信息
            $modelTask = new \App\Weixin2\Models\Notification\Task();
            $taskInfo = $modelTask->getInfoById($taskProcessInfo['notification_task_id']);
            // 如果没有找到的话报错
            if (empty($taskInfo)) {
                throw new \Exception("未找到taskid为{$taskProcessInfo['notification_task_id']}所对应的推送任务");
            }

            // 3 获取该任务下的所有的任务内容
            $modelTaskContent = new \App\Weixin2\Models\Notification\TaskContent();
            $taskContentList = $modelTaskContent->getAndLockListByTaskId($taskInfo['id']);
            // 如果没有 说明设置有问题
            if (empty($taskContentList)) {
                throw new \Exception("未找到任务ID:{$taskInfo['id']}的推送任务所对应的推送任务内容");
            }

            // 4 获取该任务下的所有推送中的任务日志
            $modelTaskLog = new \App\Weixin2\Models\Notification\TaskLog();
            $taskLogList = $modelTaskLog->getAndLockListByTaskId($taskInfo['id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING);
            if (empty($taskLogList)) {
                throw new \Exception("未找到任务ID:{$taskInfo['id']}的推送任务所对应的推送任务日志内容");
            }

            // 5 循环处理任务日志记录
            if (!empty($taskLogList)) {

                foreach ($taskLogList as $taskLogItem) {

                    $match = array();
                    $match['id'] = 0;
                    $match['keyword'] = "";

                    try {
                        $modelTaskLog->begin();

                        // 锁定记录ID
                        $taskLog = $modelTaskLog->lockLog($taskLogItem['id']);

                        // 创建service
                        $weixinopenService = new \App\Components\Weixinopen\Services\WeixinopenService($taskLog['authorizer_appid'], $taskLog['component_appid']);

                        // 推送方式
                        if ($taskLog['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_TEMPLATEMSG) { // 1:模板消息

                            // 根据模板消息记录ID获取模板消息配置
                            $modelTemplateMsg = new \App\Weixin2\Models\TemplateMsg\TemplateMsg();
                            $templateMsgInfo = $modelTemplateMsg->getInfoById($taskLog['template_msg_id']);
                            if (empty($templateMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['id']},模板消息记录ID:{$taskLog['template_msg_id']}所对应的记录不存在");
                            }

                            // 发送模板消息
                            $ret = $weixinopenService->sendTemplateMsg($taskLog['openid'], "", $templateMsgInfo, $match);
                        } elseif ($taskLog['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_MASSMSG) { // 2:群发消息

                            // 获取群发消息发送方式信息
                            $modelMassMsgSendMethod = new \App\Weixin2\Models\MassMsg\SendMethod();
                            $sendMethodInfo = $modelMassMsgSendMethod->getInfoById($taskLog['mass_msg_send_method_id']);
                            if (empty($sendMethodInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['id']},群发消息发送方式记录ID:{$taskLog['mass_msg_send_method_id']}所对应的记录不存在");
                            }

                            // 根据群发消息记录ID获取群发消息配置
                            $modelMassMsg = new \App\Weixin2\Models\MassMsg\MassMsg();
                            $massMsgInfo = $modelMassMsg->getInfoById($taskLog['mass_msg_id']);
                            if (empty($massMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['id']},群发消息记录ID:{$taskLog['mass_msg_id']}所对应的记录不存在");
                            }

                            $tag_id = "";
                            $openids = array();

                            // 按照tag_id发送的话
                            if ($sendMethodInfo['send_method'] == \App\Weixin2\Models\MassMsg\SendMethod::SEND_BY_TAGID) {
                                if (empty($taskLog['tag_id'])) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['id']},tag_id字段的值为空");
                                }
                                $tag_id = $taskLog['tag_id'];
                            } elseif ($sendMethodInfo['send_method'] == \App\Weixin2\Models\MassMsg\SendMethod::SEND_BY_OPENIDS) {
                                // 按照openids列表发送
                                if (empty($taskLog['openids'])) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['id']},openids字段的值为空");
                                }
                                $openids = explode(',', $taskLog['openids']);
                                if (empty($openids)) {
                                    throw new \Exception("任务日志记录ID:{$taskLog['id']},openids字段的值为空");
                                }
                            }

                            // 发送群发消息
                            $match['mass_msg_type'] = $massMsgInfo['msg_type'];
                            $ret = $weixinopenService->sendMassMsg($tag_id, $openids, $massMsgInfo, $sendMethodInfo, $match, true);
                        } elseif ($taskLog['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_CUSTOMMSG) { // 4:客服消息

                            // 根据客服消息记录ID获取客服消息配置
                            $modelCustomMsg = new \App\Weixin2\Models\CustomMsg\CustomMsg();
                            $customMsgInfo = $modelCustomMsg->getInfoById($taskLog['custom_msg_id']);
                            if (empty($customMsgInfo)) {
                                throw new \Exception("任务日志记录ID:{$taskLog['id']},客服消息记录ID:{$taskLog['custom_msg_id']}所对应的记录不存在");
                            }

                            // 发送客服消息
                            $match['custom_msg_type'] = $customMsgInfo['msg_type'];
                            $ret = $weixinopenService->sendCustomMsg($taskLog['openid'], "", $customMsgInfo, $match);
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
                        $modelTaskLog->updatePushState($taskLog['id'], $status, $now, $ret['is_ok'], $ret['api_ret'], 1);

                        // 是否成功了
                        $is_success = ($status == \App\Weixin2\Models\Notification\TaskProcess::PUSH_SUCCESS);
                        // 推送任务内容 更新成功处理件数和已处理件数
                        $modelTaskContent->incProcessedNum($taskLog['notification_task_content_id'], 1, $is_success);
                        // 推送任务 更新成功处理件数和已处理件数
                        $modelTask->incProcessedNum($taskLog['notification_task_id'], 1, $is_success);
                        // 推送任务处理 更新成功处理件数和已处理件数
                        $modelTaskProcess->incProcessedNum($taskLog['notification_task_process_id'], 1, $is_success);
                        $modelTaskLog->commit();
                    } catch (\Exception $e) {
                        $modelTaskLog->rollback();
                        $modelActivityErrorLog->log($this->activity_id, $e, $now);
                    }
                }
            }

            // 4 修改处理状态为已完成
            try {
                $modelTask->begin();
                // 处理状态已完成
                $modelTaskProcess->updatePushState($taskProcessInfo['id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_OVER, $now);
                // 推送状态已完成
                $modelTask->updatePushState($taskInfo['id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_OVER, $now);

                // 推送任务内容的推送状态已完成
                foreach ($taskContentList as $taskContent) {
                    $modelTaskContent->updatePushState($taskContent['id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_OVER, $now);
                }
                $modelTask->commit();
            } catch (\Exception $e) {
                $modelTask->rollback();
                $modelActivityErrorLog->log($this->activity_id, $e, $now);
            }
        } catch (\Exception $e) {
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }
    }
}
