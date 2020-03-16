<?php
class WeixinnotificationprepareTask  extends \Phalcon\CLI\Task
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weixin2:notification_task_prepare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '消息推送任务预备处理';

    // 监控任务
    private $activity_id = 6;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixinnotificationprepare handle 5e46128f69dc0a0f8415fe8f.csv
     * @param array $params            
     */
    public function handleAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();

        try {
            // 1 获取未推送的任务并且进行锁住
            $modelTask = new \App\Weixin2\Models\Notification\Task();
            $this->modelTask->begin();

            $taskInfo = $modelTask->getAndLockOneTask4ByPushStatus(\App\Weixin2\Models\Notification\TaskProcess::UNPUSH, $now);

            // 如果没有 直接退出
            if (empty($taskInfo)) {
                throw new \Exception("未找到推送任务");
            }

            // 进行锁定处理 ???
            $lock = new \iLock('task_id:' . $taskInfo['id']);
            if ($lock->lock()) {
                throw new \Exception("taskid为{$taskInfo['id']}所对应的任务在锁定中，请等待");
            }

            // 检查是否在推送任务处理表中存在
            $modelTaskProcess = new \App\Weixin2\Models\Notification\TaskProcess();
            $taskProcessItem = $modelTaskProcess->getInfoByTaskId($taskInfo['id']);
            // 只要有生成过一条记录 就报错
            if (!empty($taskProcessItem)) {
                throw new \Exception("任务ID:{$taskInfo['id']}的推送任务在推送任务处理表中已登录");
            }

            // 2 登录推动任务处理表中
            $name = date("YmdHis", $now);
            $taskProcessItem = $modelTaskProcess->logon($taskInfo['name'] . "_{$name}_" . \uniqid(), $taskInfo['id'], 0, $now);
            // 登录失败
            if (empty($taskProcessItem)) {
                throw new \Exception("任务ID:{$taskInfo['id']}的推送任务在推送任务处理表中登录失败");
            }

            // 3 更新成推送中
            $modelTask->updatePushState($taskInfo['id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);

            // 4 获取该任务下的所有的任务内容
            $modelTaskContent = new \App\Weixin2\Models\Notification\TaskContent();
            $taskContentList = $modelTaskContent->getAndLockListByTaskId($taskInfo['id']);
            // 如果没有 说明设置有问题
            if (empty($taskContentList)) {
                throw new \Exception("未找到任务ID:{$taskInfo['id']}的推送任务所对应的推送任务内容");
            }

            // 5 循环处理任务内容生成对应的日志记录
            $modelTaskLog = new \App\Weixin2\Models\Notification\TaskLog();

            // 检查是否生成发送日志
            $logItem = $modelTaskLog->getInfoByTaskId($taskInfo['id']);
            // 只要有生成过一条日志记录 就报错
            if (!empty($logItem)) {
                throw new \Exception("任务ID:{$taskInfo['id']}的推送任务日志记录已生成");
            }

            $j = 0;
            foreach ($taskContentList as $taskContent) {
                $i = 0;
                // 推送方式
                if ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_TEMPLATEMSG) { // 1:模板消息

                    if (empty($taskContent['openids'])) {
                        throw new \Exception("任务ID:{$taskInfo['id']},任务内容ID:{$taskContent['id']}和名称:{$taskContent['name']}的推送任务内容记录的openids字段值为空");
                    }

                    $openids = explode(',', $taskContent['openids']);
                    if (empty($openids)) {
                        throw new \Exception("任务ID:{$taskInfo['id']},任务内容ID:{$taskContent['id']}和名称:{$taskContent['name']}的推送任务内容记录的openids字段格式不正确");
                    }

                    // 根据模板消息记录ID获取模板消息配置
                    $modelTemplateMsg = new \App\Weixin2\Models\TemplateMsg\TemplateMsg();
                    $templateMsgInfo = $modelTemplateMsg->getInfoById($taskInfo['template_msg_id']);
                    if (empty($templateMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['id']},模板消息记录ID:{$taskInfo['template_msg_id']}所对应的记录不存在");
                    }

                    // 对应每一个openid都生成一条对应的发送日志
                    foreach ($openids as $openid) {
                        $modelTaskLog->log($taskInfo['component_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['id'], $taskInfo['id'], $taskInfo['name'], $taskInfo['notification_method'], $taskInfo['mass_msg_send_method_id'], $taskInfo['template_msg_id'], $taskInfo['mass_msg_id'], $taskInfo['custom_msg_id'], $taskContent['id'], $taskContent['name'], $taskContent['openids'], $openid, $taskContent['tag_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
                        $i++;
                    }
                } elseif ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_MASSMSG) { // 2:群发消息

                    // 获取群发消息发送方式信息
                    $modelMassMsgSendMethod = new \App\Weixin2\Models\MassMsg\SendMethod();
                    $sendMethodInfo = $modelMassMsgSendMethod->getInfoById($taskInfo['mass_msg_send_method_id']);
                    if (empty($sendMethodInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['id']},群发消息发送方式记录ID:{$taskInfo['mass_msg_send_method_id']}所对应的记录不存在");
                    }

                    // 按照tag_id发送的话
                    if ($sendMethodInfo['send_method'] == \App\Weixin2\Models\MassMsg\SendMethod::SEND_BY_TAGID) {
                        if (empty($taskContent['tag_id'])) {
                            throw new \Exception("任务ID:{$taskInfo['id']},任务内容ID:{$taskContent['id']}和名称:{$taskContent['name']}的推送任务内容记录的tag_id字段值为空");
                        }
                    } elseif ($sendMethodInfo['send_method'] == \App\Weixin2\Models\MassMsg\SendMethod::SEND_BY_OPENIDS) { // 按照openids列表发送
                        if (empty($taskContent['openids'])) {
                            throw new \Exception("任务ID:{$taskInfo['id']},任务内容ID:{$taskContent['id']}和名称:{$taskContent['name']}的推送任务内容记录的openids字段值为空");
                        }

                        $openids = explode(',', $taskContent['openids']);
                        if (empty($openids)) {
                            throw new \Exception("任务ID:{$taskInfo['id']},任务内容ID:{$taskContent['id']}和名称:{$taskContent['name']}的推送任务内容记录的openids字段格式不正确");
                        }
                    }

                    // 根据群发消息记录ID获取群发消息配置
                    $modelMassMsg = new \App\Weixin2\Models\MassMsg\MassMsg();
                    $massMsgInfo = $modelMassMsg->getInfoById($taskInfo['mass_msg_id']);
                    if (empty($massMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['id']},群发消息记录ID:{$taskInfo['mass_msg_id']}所对应的记录不存在");
                    }
                    $modelTaskLog->log($taskInfo['component_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['id'], $taskInfo['id'], $taskInfo['name'], $taskInfo['notification_method'], $taskInfo['mass_msg_send_method_id'], $taskInfo['template_msg_id'], $taskInfo['mass_msg_id'], $taskInfo['custom_msg_id'], $taskContent['id'], $taskContent['name'], $taskContent['openids'], "", $taskContent['tag_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
                    $i++;
                } elseif ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_CUSTOMMSG) { // 3:客服消息

                    if (empty($taskContent['openids'])) {
                        throw new \Exception("任务ID:{$taskInfo['id']},任务内容ID:{$taskContent['id']}和名称:{$taskContent['name']}的推送任务内容记录的openids字段值为空");
                    }

                    $openids = explode(',', $taskContent['openids']);
                    if (empty($openids)) {
                        throw new \Exception("任务ID:{$taskInfo['id']},任务内容ID:{$taskContent['id']}和名称:{$taskContent['name']}的推送任务内容记录的openids字段格式不正确");
                    }

                    // 根据客服消息记录ID获取客服消息配置
                    $modelCustomMsg = new \App\Weixin2\Models\CustomMsg\CustomMsg();
                    $customMsgInfo = $modelCustomMsg->getInfoById($taskInfo['custom_msg_id']);
                    if (empty($customMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['id']},客服消息记录ID:{$taskInfo['custom_msg_id']}所对应的记录不存在");
                    }
                    foreach ($openids as $openid) {
                        $modelTaskLog->log($taskInfo['component_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['id'], $taskInfo['id'], $taskInfo['name'], $taskInfo['notification_method'], $taskInfo['mass_msg_send_method_id'], $taskInfo['template_msg_id'], $taskInfo['mass_msg_id'], $taskInfo['custom_msg_id'], $taskContent['id'], $taskContent['name'], $taskContent['openids'], $openid, $taskContent['tag_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
                        $i++;
                    }
                }
                $j += $i;
                // 6 更新成推送中
                $modelTaskContent->updatePushState($taskContent['id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);

                // 7 更新总处理件数
                $modelTaskContent->updateTaskProcessTotal($taskContent['id'], $i);
            }

            // 8 更新总处理件数
            $modelTask->updateTaskProcessTotal($taskInfo['id'], $j);

            // 9 更新总处理件数
            $modelTaskProcess->updateTaskProcessTotal($taskProcessItem['id'], $j);

            // 10 更新处理状态为开始
            $modelTaskProcess->updatePushState($taskProcessItem['id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);

            $this->modelTask->commit();
        } catch (\Exception $e) {
            $this->modelTask->rollback();
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }
    }
}
