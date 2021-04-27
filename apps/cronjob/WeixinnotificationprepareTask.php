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

        $modelTask = new \App\Weixin2\Models\Notification\Task();

        try {
            $modelTask->begin();

            // 1 获取未推送的任务并且进行锁住
            if (empty($task_id)) {
                $taskInfo = $modelTask->getAndLockOneTask4ByPushStatus(\App\Weixin2\Models\Notification\TaskProcess::UNPUSH, $now);
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
            $lock = new \iLock('weixin2:task_id:' . $taskInfo['_id']);
            $lock->setExpire(3600);
            if ($lock->lock()) {
                throw new \Exception("taskid为{$taskInfo['_id']}所对应的任务在锁定中，请等待");
            }

            // 检查推送状态
            if (intval($taskInfo['push_status']) != \App\Weixin2\Models\Notification\TaskProcess::UNPUSH) {
                throw new \Exception("taskid为{$taskInfo['_id']}所对应的任务的推送状态不是待推送");
            }

            // 检查是否在推送任务处理表中存在
            $modelTaskProcess = new \App\Weixin2\Models\Notification\TaskProcess();
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
            $modelTask->updatePushState($taskInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);

            // 5 循环处理任务内容生成对应的日志记录
            $modelTaskLog = new \App\Weixin2\Models\Notification\TaskLog();

            // 检查是否生成发送日志
            $logItem = $modelTaskLog->getInfoByTaskId($taskInfo['_id']);
            // 只要有生成过一条日志记录 就报错
            if (!empty($logItem)) {
                throw new \Exception("任务ID:{$taskInfo['_id']}的推送任务日志记录已生成");
            }

            $errorException = null;
            try {
                // 是否检查openids
                $isCheckOpenids = true;
                // 推送方式
                if ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_TEMPLATEMSG) { // 1:模板消息

                    // 根据模板消息记录ID获取模板消息配置
                    $modelTemplateMsg = new \App\Weixin2\Models\TemplateMsg\TemplateMsg();
                    $templateMsgInfo = $modelTemplateMsg->getInfoById($taskInfo['template_msg_id']);
                    if (empty($templateMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},模板消息记录ID:{$taskInfo['template_msg_id']}所对应的记录不存在");
                    }
                } elseif ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_MASSMSG) { // 2:群发消息
                    // 根据群发消息记录ID获取群发消息配置
                    $modelMassMsg = new \App\Weixin2\Models\MassMsg\MassMsg();
                    $massMsgInfo = $modelMassMsg->getInfoById($taskInfo['mass_msg_id']);
                    if (empty($massMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},群发消息记录ID:{$taskInfo['mass_msg_id']}所对应的记录不存在");
                    }

                    // 获取群发消息发送方式信息
                    $modelMassMsgSendMethod = new \App\Weixin2\Models\MassMsg\SendMethod();
                    $sendMethodInfo = $modelMassMsgSendMethod->getInfoById($taskInfo['mass_msg_send_method_id']);
                    if (empty($sendMethodInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},群发消息发送方式记录ID:{$taskInfo['mass_msg_send_method_id']}所对应的记录不存在");
                    }

                    // 按照tag_id发送的话
                    if ($sendMethodInfo['send_method'] == \App\Weixin2\Models\MassMsg\SendMethod::SEND_BY_TAGID) {
                        if (empty($taskInfo['tag_id'])) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},的推送任务的tag_id字段值为空");
                        }
                        // 不需要检查openids
                        $isCheckOpenids = false;
                    } elseif ($sendMethodInfo['send_method'] == \App\Weixin2\Models\MassMsg\SendMethod::SEND_BY_OPENIDS) { // 按照openids列表发送
                    }
                } elseif ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_CUSTOMMSG) { // 3:客服消息

                    // 根据客服消息记录ID获取客服消息配置
                    $modelCustomMsg = new \App\Weixin2\Models\CustomMsg\CustomMsg();
                    $customMsgInfo = $modelCustomMsg->getInfoById($taskInfo['custom_msg_id']);
                    if (empty($customMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},客服消息记录ID:{$taskInfo['custom_msg_id']}所对应的记录不存在");
                    }
                } elseif ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_SUBSCRIBETEMPLATEMSG4MINIPROGRAM) { // 4:小程序订阅消息

                    // 根据订阅模板消息记录ID获取订阅模板消息配置
                    $modelSubscribeMsg = new \App\Weixin2\Models\Miniprogram\SubscribeMsg\Msg();
                    $subscribeMsgInfo = $modelSubscribeMsg->getInfoById($taskInfo['subscribe_msg_id']);
                    if (empty($subscribeMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},订阅消息记录ID:{$taskInfo['subscribe_msg_id']}所对应的记录不存在");
                    }
                } elseif ($taskInfo['notification_method'] == \App\Weixin2\Models\Notification\Task::NOTIFY_BY_UNIFORMMSG4MINIPROGRAM) { // 5:小程序统一服务消息

                    // 根据模板消息记录ID获取模板消息配置
                    $modelTemplateMsg = new \App\Weixin2\Models\TemplateMsg\TemplateMsg();
                    $templateMsgInfo = $modelTemplateMsg->getInfoById($taskInfo['template_msg_id']);
                    if (empty($templateMsgInfo)) {
                        throw new \Exception("任务ID:{$taskInfo['_id']},公众号模板消息记录ID:{$taskInfo['template_msg_id']}所对应的记录不存在");
                    }
                }

                $j = 0;
                // 如果要检查openids的话
                if ($isCheckOpenids) {
                    $line_no = 0;
                    $i = 0;
                    // 排重用
                    $openidList4Log = array();
                    // 如果任务信息中配置了openids字段的话就用openids字段中的值进行发送
                    if (!empty($taskInfo['openids'])) {

                        $openids = explode(',', $taskInfo['openids']);
                        if (empty($openids)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']}的推送任务的openids字段格式不正确");
                        }
                        // 对应每一个openid都生成一条对应的发送日志
                        foreach ($openids as $content) {
                            $line_no++;
                            $openid = trim($content);
                            if (empty($openid)) {
                                continue;
                            }
                            if (isset($openidList4Log[$openid])) {
                                continue;
                            }
                            $modelTaskLog->log($taskInfo['component_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['_id'], $taskInfo['_id'], $taskInfo['name'], $taskInfo['notification_method'], $taskInfo['mass_msg_send_method_id'], $taskInfo['subscribe_msg_id'], $taskInfo['template_msg_id'], $taskInfo['mass_msg_id'], $taskInfo['custom_msg_id'], $taskInfo['changemsginfo_callback'], 0, '按openids列表发送消息', $openid, $openid, 0, \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
                            $openidList4Log[$openid] = $openid;
                            $i++;
                        }
                    } elseif (!empty($taskInfo['openids_file'])) { // 如果任务信息中上传了openids文件的话 那么就用上传文件中的openid进行发送
                        $filePath = trim($taskInfo['openids_file']);
                        // 如果文件不存在
                        if (!file_exists($filePath)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},文件路径:{$filePath}的文件不存在");
                        }

                        // 加载csv数据
                        $csv = file_get_contents($filePath);
                        if (empty($csv)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},文件路径:{$filePath}的文件内容为空");
                        }

                        $openids = $this->csv2arr($csv);
                        unset($csv); // 释放内存

                        if (empty($openids)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},文件路径:{$filePath}的openids字段格式不正确");
                        }

                        // 对应每一个openid都生成一条对应的发送日志
                        foreach ($openids as $content) {
                            $line_no++;
                            $openid = trim($content[0]);
                            if (empty($openid)) {
                                continue;
                            }
                            if (isset($openidList4Log[$openid])) {
                                continue;
                            }
                            $modelTaskLog->log($taskInfo['component_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['_id'], $taskInfo['_id'], $taskInfo['name'], $taskInfo['notification_method'], $taskInfo['mass_msg_send_method_id'], $taskInfo['subscribe_msg_id'], $taskInfo['template_msg_id'], $taskInfo['mass_msg_id'], $taskInfo['custom_msg_id'], $taskInfo['changemsginfo_callback'], 0, '按上传文件发送消息', $openid, $openid, 0, \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
                            $openidList4Log[$openid] = $openid;
                            $i++;
                        }
                    } elseif (!empty($taskInfo['openids_sql'])) { // 如果任务信息中配置了openids_sql字段的话 那么就用openids_sql字段获取的openid列表进行发送
                        $openids_sql = trim($taskInfo['openids_sql']);
                        // 如果为空
                        if (empty($openids_sql)) {
                            throw new \Exception("任务ID:{$taskInfo['_id']},openids_sql字段为空");
                        }
                        // 加载openids数据
                        $connection = $modelTask->getDb();
                        $result1 = $connection->query($openids_sql, array());
                        $result1->setFetchMode(MYDB_FETCH_ASSOC);
                        $openids = $result1->fetchAll();
                        foreach ($openids as $col) {
                            $openid = trim($col['openid']);
                            $line_no++;
                            if (empty($openid)) {
                                continue;
                            }
                            if (isset($openidList4Log[$openid])) {
                                continue;
                            }
                            $modelTaskLog->log($taskInfo['component_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['_id'], $taskInfo['_id'], $taskInfo['name'], $taskInfo['notification_method'], $taskInfo['mass_msg_send_method_id'], $taskInfo['subscribe_msg_id'], $taskInfo['template_msg_id'], $taskInfo['mass_msg_id'], $taskInfo['custom_msg_id'], $taskInfo['changemsginfo_callback'], 0, '按sql文发送消息', $openid, $openid, 0, \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
                            $openidList4Log[$openid] = $openid;
                            $i++;
                        }
                    }
                } else {
                    $modelTaskLog->log($taskInfo['component_appid'], $taskInfo['authorizer_appid'], $taskProcessItem['_id'], $taskInfo['_id'], $taskInfo['name'], $taskInfo['notification_method'], $taskInfo['mass_msg_send_method_id'], $taskInfo['subscribe_msg_id'], $taskInfo['template_msg_id'], $taskInfo['mass_msg_id'], $taskInfo['custom_msg_id'], $taskInfo['changemsginfo_callback'], 0, "按tagid发送消息", '', '', $taskInfo['tag_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now);
                    $i = 1;
                }

                if (empty($i)) {
                    throw new \Exception("任务ID:{$taskInfo['_id']},推送的用户openid数据为空");
                }
                $j += $i;

                // 8 更新总处理件数
                $modelTask->updateTaskProcessTotal($taskInfo['_id'], $j);

                // 9 更新处理状态为开始和更新总处理件数
                $modelTaskProcess->updatePushState($taskProcessItem['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSHING, $now, $j);
            } catch (\Exception $th) {
                $errorException = $th;
                // 关闭该任务
                $modelTask->updatePushState($taskInfo['_id'], \App\Weixin2\Models\Notification\TaskProcess::PUSH_CLOSE, $now, 0);
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
