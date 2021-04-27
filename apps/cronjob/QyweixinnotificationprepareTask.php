<?php
class QyweixinnotificationprepareTask  extends \Phalcon\CLI\Task
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qyweixin:notification_task_prepare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '企业微信消息推送任务预备处理';

    // 监控任务
    private $activity_id = 1;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php weixinnotificationprepare handle
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

        $modelTask = new \App\Qyweixin\Models\Notification\Task();

        try {
            $modelTask->begin();

            // 1 获取未推送的任务并且进行锁住
            if (empty($task_id)) {
                $taskInfo = $modelTask->getAndLockOneTask4ByPushStatus(\App\Qyweixin\Models\Notification\TaskProcess::UNPUSH, $now);
            } else {
                $taskInfo = $modelTask->getAndLockOneTask4ById($task_id, $now);
            }

            // 如果没有 直接退出
            if (empty($taskInfo)) {
                $modelTask->rollback();
                return;
                throw new \Exception("未找到推送任务");
            }

            // 进行锁定处理
            $lock = new \iLock('qyweixin:task_id:' . $taskInfo['_id']);
            $lock->setExpire(3600);
            if ($lock->lock()) {
                throw new \Exception("taskid为{$taskInfo['_id']}所对应的任务在锁定中，请等待");
            }

            // 检查推送状态
            if (intval($taskInfo['push_status']) != \App\Qyweixin\Models\Notification\TaskProcess::UNPUSH) {
                throw new \Exception("taskid为{$taskInfo['_id']}所对应的任务的推送状态不是待推送");
            }

            // 检查是否在推送任务处理表中存在
            $modelTaskProcess = new \App\Qyweixin\Models\Notification\TaskProcess();
            $taskProcessItem = $modelTaskProcess->getInfoByTaskId($taskInfo['_id']);
            // 只要有生成过一条记录 就报错
            if (!empty($taskProcessItem)) {
                throw new \Exception("任务ID:{$taskInfo['_id']}的推送任务在推送任务处理表中已登录");
            }

            // 2 登录推动任务处理表中
            $name = date("YmdHis", $now);
            $taskProcessItem = $modelTaskProcess->logon($taskInfo['name'] . "_{$name}_" . \uniqid(), $taskInfo['_id'], 0, $now);
            // 登录失败
            if (empty($taskProcessItem)) {
                throw new \Exception("任务ID:{$taskInfo['_id']}的推送任务在推送任务处理表中登录失败");
            }

            // 3 更新成推送中
            $modelTask->updatePushState($taskInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSHING, $now);

            // 5 循环处理任务内容生成对应的日志记录
            $modelTaskLog = new \App\Qyweixin\Models\Notification\TaskLog();

            // 检查是否生成发送日志
            $logItem = $modelTaskLog->getInfoByTaskId($taskInfo['_id']);
            // 只要有生成过一条日志记录 就报错
            if (!empty($logItem)) {
                throw new \Exception("任务ID:{$taskInfo['_id']}的推送任务日志记录已生成");
            }

            $errorException = null;
            try {

                // 是否检查userids
                $isCheckuserids = true;
                // 推送方式 1:发送应用消息 2:发送消息到群聊会话 3:发送互联企业消息 4:发送企业消息到群聊会话
                if ($taskInfo['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_AGENT_MESSAGE) { // 1:发送应用消息

                    // 根据应用消息记录ID获取应用消息配置
                    $modelAgentMsg = new \App\Qyweixin\Models\AgentMsg\AgentMsg();
                    $agentMsgInfo = $modelAgentMsg->getInfoById($taskInfo['agent_msg_id']);
                    if (empty($agentMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},应用消息记录ID:{$taskInfo['agent_msg_id']}所对应的记录不存在");
                    }
                } elseif ($taskInfo['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_APPCHAT) { // 2:发送消息到群聊会话
                    // 根据消息到群聊会话记录ID获取消息到群聊会话配置
                    $modelAppchatMsg = new \App\Qyweixin\Models\AppchatMsg\AppchatMsg();
                    $appchatInfo = $modelAppchatMsg->getInfoById($taskInfo['appchat_msg_id']);
                    if (empty($appchatInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},消息到群聊会话记录ID:{$taskInfo['appchat_msg_id']}所对应的记录不存在");
                    }
                } elseif ($taskInfo['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_LINKEDCORP_MESSAGE) { // 3:发送互联企业消息

                    // 根据互联企业消息记录ID获取互联企业消息配置
                    $modelLinkedcorpMsg = new \App\Qyweixin\Models\LinkedcorpMsg\LinkedcorpMsg();
                    $linkedcorpMsgInfo = $modelLinkedcorpMsg->getInfoById($taskInfo['linkedcorp_msg_id']);
                    if (empty($linkedcorpMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},互联企业消息记录ID:{$taskInfo['linkedcorp_msg_id']}所对应的记录不存在");
                    }
                } elseif ($taskInfo['notification_method'] == \App\Qyweixin\Models\Notification\Task::NOTIFY_BY_EXTERNALCONTACT_ADD_MSG_TEMPLATE) { // 4:发送企业消息到群聊会话

                    // 根据企业群发消息记录ID获取企业群发消息配置
                    $modelMsgTemplate = new \App\Qyweixin\Models\ExternalContact\MsgTemplate();
                    $msgTemplateInfo = $modelMsgTemplate->getInfoById($taskInfo['externalcontact_msg_template_id']);
                    if (empty($msgTemplateInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},企业群发消息记录ID:{$taskInfo['externalcontact_msg_template_id']}所对应的记录不存在");
                    }
                }

                $j = 0;
                // 如果要检查userids的话
                if ($isCheckuserids) {
                    $line_no = 0;
                    $i = 0;
                    // 排重用
                    $useridList4Log = array();
                    // 如果任务信息中配置了userids字段的话就用userids字段中的值进行发送
                    if (!empty($taskInfo['userids'])) {

                        $userids = explode(',', $taskInfo['userids']);
                        if (empty($userids)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']}的推送任务的userids字段格式不正确");
                        }
                        // 对应每一个userid都生成一条对应的发送日志
                        foreach ($userids as $content) {
                            $line_no++;
                            $userid = trim($content);
                            if (empty($userid)) {
                                continue;
                            }
                            if (isset($useridList4Log[$userid])) {
                                continue;
                            }
                            $modelTaskLog->log($taskInfo['provider_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['_id'], $taskInfo['_id'], $taskInfo['name'], $taskInfo['notification_method'],  $taskInfo['externalcontact_msg_template_chat_type'], $taskInfo['agent_msg_id'], $taskInfo['appchat_msg_id'], $taskInfo['externalcontact_msg_template_id'], $taskInfo['linkedcorp_msg_id'], $taskInfo['changemsginfo_callback'], 0, '按userids列表发送消息', $userid, $userid, \App\Qyweixin\Models\Notification\TaskProcess::PUSHING, $now);
                            $useridList4Log[$userid] = $userid;
                            $i++;
                        }
                    } elseif (!empty($taskInfo['userids_file'])) { // 如果任务信息中上传了userids文件的话 那么就用上传文件中的userid进行发送
                        $filePath = trim($taskInfo['userids_file']);
                        // 如果文件不存在
                        if (!file_exists($filePath)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},文件路径:{$filePath}的文件不存在");
                        }

                        // 加载csv数据
                        $csv = file_get_contents($filePath);
                        if (empty($csv)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},文件路径:{$filePath}的文件内容为空");
                        }

                        $userids = $this->csv2arr($csv);
                        unset($csv); // 释放内存

                        if (empty($userids)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},文件路径:{$filePath}的userids字段格式不正确");
                        }

                        // 对应每一个userid都生成一条对应的发送日志
                        foreach ($userids as $content) {
                            $line_no++;
                            $userid = trim($content[0]);
                            if (empty($userid)) {
                                continue;
                            }
                            if (isset($useridList4Log[$userid])) {
                                continue;
                            }
                            $modelTaskLog->log($taskInfo['provider_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['_id'], $taskInfo['_id'], $taskInfo['name'], $taskInfo['notification_method'],  $taskInfo['externalcontact_msg_template_chat_type'], $taskInfo['agent_msg_id'], $taskInfo['appchat_msg_id'], $taskInfo['externalcontact_msg_template_id'], $taskInfo['linkedcorp_msg_id'], $taskInfo['changemsginfo_callback'], 0, '按上传文件发送消息', $userid, $userid, \App\Qyweixin\Models\Notification\TaskProcess::PUSHING, $now);
                            $useridList4Log[$userid] = $userid;
                            $i++;
                        }
                    } elseif (!empty($taskInfo['userids_sql'])) { // 如果任务信息中配置了userids_sql字段的话 那么就用userids_sql字段获取的userid列表进行发送
                        $userids_sql = trim($taskInfo['userids_sql']);
                        // 如果为空
                        if (empty($userids_sql)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},userids_sql字段为空");
                        }
                        // 加载userids数据
                        // 加载userids数据
                        $connection = $modelTask->getDb();
                        $result1 = $connection->query($userids_sql, array());
                        $result1->setFetchMode(MYDB_FETCH_ASSOC);
                        $userids = $result1->fetchAll();
                        foreach ($userids as $col) {
                            $userid = trim($col['userid']);
                            $line_no++;
                            if (empty($userid)) {
                                continue;
                            }
                            if (isset($useridList4Log[$userid])) {
                                continue;
                            }
                            $modelTaskLog->log($taskInfo['provider_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['_id'], $taskInfo['_id'], $taskInfo['name'], $taskInfo['notification_method'],  $taskInfo['externalcontact_msg_template_chat_type'], $taskInfo['agent_msg_id'], $taskInfo['appchat_msg_id'], $taskInfo['externalcontact_msg_template_id'], $taskInfo['linkedcorp_msg_id'], $taskInfo['changemsginfo_callback'], 0, '按sql文发送消息', $userid, $userid, \App\Qyweixin\Models\Notification\TaskProcess::PUSHING, $now);
                            $useridList4Log[$userid] = $userid;
                            $i++;
                        }
                    }
                } else {
                    $modelTaskLog->log($taskInfo['provider_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['_id'], $taskInfo['_id'], $taskInfo['name'], $taskInfo['notification_method'], $taskInfo['externalcontact_msg_template_chat_type'], $taskInfo['agent_msg_id'], $taskInfo['appchat_msg_id'], $taskInfo['externalcontact_msg_template_id'], $taskInfo['linkedcorp_msg_id'], $taskInfo['changemsginfo_callback'], 0, "按tagid发送消息", '', '', $taskInfo['tag_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSHING, $now);
                    $i = 1;
                }

                if (empty($i)) {
                    throw new \Exception("任务ID:{$taskInfo['_id']},推送的用户userid数据为空");
                }
                $j += $i;

                // 8 更新总处理件数
                $modelTask->updateTaskProcessTotal($taskInfo['_id'], $j);

                // 9 更新处理状态为开始和更新总处理件数
                $modelTaskProcess->updatePushState($taskProcessItem['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSHING, $now, $j);
            } catch (\Exception $th) {
                $errorException = $th;
                // 关闭该任务
                $modelTask->updatePushState($taskInfo['_id'], \App\Qyweixin\Models\Notification\TaskProcess::PUSH_CLOSE, $now, 0);
            }
            $modelTask->commit();

            // 如果有错误的发生的话就记录一下
            if (!empty($errorException)) {
                $modelActivityErrorLog->log($this->activity_id, $errorException, $now);
            }
        } catch (\Exception $e) {
            $modelTask->rollback();
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }

        $ret = array();
        $ret['task_id'] = $task_id;
        if (!empty($taskInfo)) {
            $ret['taskInfo'] = $taskInfo;
        }
        if (!empty($taskProcessItem)) {
            $ret['taskProcessInfo'] = $taskProcessItem;
        }
        print_r($ret);
    }

    /**
     * 转化为数组
     *
     * @param string $CsvString            
     * @return array
     */
    protected function csv2arr($csvString)
    {
        // 去掉bom
        $csvString = ltrim($csvString, "\xEF\xBB\xBF");
        $csvString = $this->convertCharacet($csvString);
        $data = str_getcsv($csvString, "\n"); // parse the rows
        foreach ($data as &$row) {
            $row = str_getcsv($row, ",");
        }
        return $data;
    }

    protected function convertCharacet($data)
    {
        if (!empty($data)) {
            $fileType = mb_detect_encoding($data, array('UTF-8', 'GBK', 'LATIN1', 'BIG5'));
            if ($fileType != 'UTF-8') {
                $data = mb_convert_encoding($data, 'utf-8', $fileType);
            }
        }
        return $data;
    }
}
