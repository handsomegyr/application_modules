<?php

class WeixinnotificationsendretryTask extends \Phalcon\CLI\Task
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weixin2:notification_task_send_retry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '消息推送任务尝试再次发送处理';

    // 监控任务
    private $activity_id = 6;

    // 最大尝试次数
    private $max_try_num = 5;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixinnotificationsendretry handle 5e46128f69dc0a0f8415fe8f.csv
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
            $modelTaskProcess = new \App\Weixin2\Models\Notification\TaskProcess();
            $modelTask = new \App\Weixin2\Models\Notification\Task();
            $modelTaskContent = new \App\Weixin2\Models\Notification\TaskContent();

            // 1 获取所有推送失败的任务日志
            $modelTaskLog = new \App\Weixin2\Models\Notification\TaskLog();
            $taskLogList = $modelTaskLog->getRetryList($this->max_try_num, \App\Weixin2\Models\Notification\TaskProcess::PUSH_FAIL);

            // 如果没有的话直接返回
            if (empty($taskLogList)) {
                return;
            }

            // 2 循环处理任务日志记录
            if (!empty($taskLogList)) {

                foreach ($taskLogList as $taskLogItem) {

                    // 进行锁定处理 ???
                    $lock = new \iLock('task_log_id:' . $taskLogItem['_id']);
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
                            $status = \App\Weixin2\Models\Notification\TaskProcess::PUSH_FAIL;
                            $ret = array();
                            $ret['is_ok'] = true;
                            $ret['api_ret'] = array();
                        } else {
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

                                // 发送模板消息
                                $ret = $weixinopenService->sendTemplateMsg($taskLog['openid'], "", $templateMsgInfo, $match);
                            } elseif ($taskLog['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_MASSMSG) { // 2:群发消息

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
                                    if (empty($taskLog['openids'])) {
                                        throw new \Exception("任务日志记录ID:{$taskLog['_id']},openids字段的值为空");
                                    }
                                    $openids = explode(',', $taskLog['openids']);
                                    if (empty($openids)) {
                                        throw new \Exception("任务日志记录ID:{$taskLog['_id']},openids字段的值为空");
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
                                    throw new \Exception("任务日志记录ID:{$taskLog['_id']},客服消息记录ID:{$taskLog['custom_msg_id']}所对应的记录不存在");
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
                                // 要根据错误码来决定 比如说accesstoken过期了这种错误是临时的,可以进行重试 还有一种错误是该openid不存在或已取消关注 这种错误就不需要进行重试
                                $status = \App\Weixin2\Models\Notification\TaskProcess::PUSH_FAIL;
                            }
                        }

                        // 更新状态
                        $modelTaskLog->updatePushState($taskLog['_id'], $status, $now, $ret['is_ok'], $ret['api_ret'], 1);

                        // 如果成功了或失败了
                        if ($status == \App\Weixin2\Models\Notification\TaskProcess::PUSH_SUCCESS) {
                            // 推送任务内容 更新成功处理件数
                            $modelTaskContent->incSuccessNum($taskLog['notification_task_content_id'], 1);
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
