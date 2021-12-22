<?php
class LexianglaTask extends \Phalcon\CLI\Task
{
    // 监控任务
    private $activity_id = 7;

    /**
     * @var \App\Lexiangla\Models\Contact\Department
     */
    protected $modelLexianglaDepartment;

    /**
     * @var \App\Lexiangla\Models\Contact\DepartmentSync
     */
    protected $modelLexianglaDepartmentSync;

    /**
     * @var \App\Lexiangla\Models\Contact\Tag
     */
    protected $modelLexianglaTag;

    /**
     * @var \App\Lexiangla\Models\Contact\TagSync
     */
    protected $modelLexianglaTagSync;

    /**
     * @var \App\Lexiangla\Models\Contact\User
     */
    protected $modelLexianglaUser;

    // /**
    //  * @var \App\Lexiangla\Models\Contact\UserSync
    //  */
    // protected $modelLexianglaUserSync;

    /**
     * @var \App\Lexiangla\Models\Contact\TagUser
     */
    protected $modelLexianglaTagUser;
    /**
     * @var \App\Lexiangla\Models\Contact\TagParty
     */
    protected $modelLexianglaTagParty;

    protected $processFlags = array(
        'sync_department' => true,
        'sync_department_inner' => array(
            'qyweixin_department_sync' => true,
            'lexiangla_department_sync' => true,
            'qyweixin_lexiangla_department_sync' => true
        ),
        'sync_tag' => true,
        'sync_tag_inner' => array(
            'qyweixin_tag_sync' => true,
            'lexiangla_tag_sync' => true,
            'qyweixin_lexiangla_tag_sync' => true
        ),
        'sync_user' => true,
        'sync_user_inner' => array(
            'qyweixin_department_user_sync' => true,
            'qyweixin_user_sync' => true,
            'lexiangla_department_user_sync' => true,
            'lexiangla_user_sync' => true,
            'qyweixin_lexiangla_user_sync' => true
        ),
        'sync_taguser' => true,
        'sync_taguser_inner' => array(
            'qyweixin_taguser_sync' => true,
            'lexiangla_taguser_sync' => true,
            'qyweixin_lexiangla_taguser_sync' => true,
            'qyweixin_lexiangla_tagparty_sync' => true
        ),
    );

    /**
     * 获取乐享的accesstoken
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php lexiangla getaccesstoken
     * @param array $params            
     */
    public function getaccesstokenAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();

        try {
            $modelApplication = new \App\Lexiangla\Models\Application();
            $now = \App\Common\Utils\Helper::getCurrentTime($now);
            $query = array(
                'access_token_expire' => array(
                    '$lte' => $now
                )
            );
            $sort = array('access_token_expire' => 1, '_id' => 1);
            $applicationList = $modelApplication->findAll($query, $sort);
            if (!empty($applicationList)) {
                foreach ($applicationList as $applicationItem) {
                    try {
                        // 更新
                        $modelApplication->getTokenByAppid($applicationItem['AppKey']);
                    } catch (\Exception $e) {
                        $modelActivityErrorLog->log($this->activity_id, $e, $now);
                    }
                }
            }
        } catch (\Exception $e) {
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }
    }

    /**
     * 乐享消息通知
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php lexiangla sendmsg
     * @param array $params            
     */
    public function sendmsgAction(array $params)
    {
        $now = time();
        $params  = array(
            'name' => 'lexiangla:send_msg',
            'now' => date('Y-m-d H:i:s', $now)
        );
        print_r($params);

        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'lexiangla:send_msg_command1'));
        $lock->setExpire(5 * 60);
        if ($lock->lock()) {
            echo "locked";
            return;
        }

        $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
        $query = array('agentid' => '1000028');
        $sort = array('_id' => 1);
        $agentList = $modelAgent->findAll($query, $sort);
        if (empty($agentList)) {
            echo "no agentid";
            return;
        }

        $departmentSyncModel = new \App\Lexiangla\Models\Contact\DepartmentSync();
        $tagSyncModel = new \App\Lexiangla\Models\Contact\TagSync();
        $modelTask = new App\Cronjob\Models\Task();
        // 获取任务列表
        $query = array('type' => \App\Cronjob\Models\Task::TASK_LEXIANGLA_MESSAGE_PUSH);
        $query['is_done'] = 0;
        $sort = array('_id' => 1);
        $tasks = $modelTask->find($query, $sort, 0, 1000);

        foreach ($agentList as  $info) {
            $provider_appid = $info['provider_appid'];
            $authorizer_appid = $info['authorizer_appid'];
            $agentid = $info['agentid'];
            $objQyweixinService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
            foreach ($tasks['datas'] as $task) {
                try {
                    $msgArr = json_decode($task['content'], true);
                    $ToUserName = $msgArr['attributes']['to_user'];
                    $ToUserName = str_ireplace("ADA13661688373", "intone13661688373", $ToUserName);
                    if ($msgArr['attributes']['msg_type'] == 'text') {
                        // 文本消息
                        $objMsg = new \Qyweixin\Model\Message\Text($agentid, $msgArr['attributes']['text']['content'], $ToUserName);
                    } elseif ($msgArr['attributes']['msg_type'] == 'news') {
                        // 图文消息
                        $objMsg = new \Qyweixin\Model\Message\News($agentid, $msgArr['attributes']['news']['articles'], $ToUserName);
                    } elseif ($msgArr['attributes']['msg_type'] == 'textcard') {
                        // 文本卡片消息
                        $textcard = $msgArr['attributes']['textcard'];
                        $objMsg = new \Qyweixin\Model\Message\TextCard($agentid, $textcard['title'], $textcard['description'], $textcard['url'], $ToUserName);
                        $objMsg->btntxt = $textcard['btntxt'];
                    }
                    $objMsg->touser = $ToUserName;
                    $objMsg->toparty = $departmentSyncModel->getQyDeptIdStringByDeptId($msgArr['attributes']['to_department']);
                    $objMsg->totag = $tagSyncModel->getQyTagIdsStringByTagId($msgArr['attributes']['to_tag']);

                    // sleep(mt_rand(1, 9) / 10);
                    $agentmsg = $objQyweixinService->getQyWeixinObject()->getMessageManager()->send($objMsg);
                    $updateData = array();
                    // dump($agentmsg);
                    if (empty($agentmsg['errcode'])) {
                        // 如果成功
                        $updateData['is_done'] = 1;
                    } else {
                        // 如果失败
                        $updateData['is_done'] = 2;
                    }
                    $updateData['do_time'] = \App\Common\Utils\Helper::getCurrentTime();
                    $updateData['memo'] = \App\Common\Utils\Helper::myJsonEncode($agentmsg);
                    $modelTask->update(array('_id' => $task['_id']), array('$set' => $updateData, '$inc' => array('do_num' => 1)));
                } catch (\Exception $th) {
                    $updateData = array();
                    $updateData['is_done'] = 2;
                    $updateData['do_time'] = \App\Common\Utils\Helper::getCurrentTime();
                    $updateData['memo'] = \App\Common\Utils\Helper::myJsonEncode(array('error_code' => $th->getCode(), 'error_msg' => $th->getMessage()));
                    $modelTask->update(array('_id' => $task['_id']), array('$set' => $updateData, '$inc' => array('do_num' => 1)));
                }
            }
        }
    }

    /**
     * 同步乐享
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php lexiangla synclexiangla
     * @param array $params            
     */
    public function synclexianglaAction(array $params)
    {
        $this->modelLexianglaDepartment  = new \App\Lexiangla\Models\Contact\Department();
        $this->modelLexianglaDepartmentSync  = new \App\Lexiangla\Models\Contact\DepartmentSync();
        $this->modelLexianglaTag  = new \App\Lexiangla\Models\Contact\Tag();
        $this->modelLexianglaTagSync  = new \App\Lexiangla\Models\Contact\TagSync();
        $this->modelLexianglaUser  = new \App\Lexiangla\Models\Contact\User();
        // $this->modelLexianglaUserSync  = new \App\Lexiangla\Models\Contact\UserSync();
        $this->modelLexianglaTagUser  = new \App\Lexiangla\Models\Contact\TagUser();
        $this->modelLexianglaTagParty  = new \App\Lexiangla\Models\Contact\TagParty();

        $now = time();

        $params  = array(
            'name' => 'lexiangla:sync_lexiangla',
            'now' => date('Y-m-d H:i:s', $now)
        );
        print_r($params);

        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'lexiangla:sync_lexiangla_command5'));
        $lock->setExpire(5 * 60);
        if ($lock->lock()) {
            echo "locked";
            return;
        }

        // 活动
        $modelActivityErrorLog  = new \App\Activity\Models\ErrorLog();
        $yestderday = date('Y-m-d', $now) . ' 00:00:00';
        $query = array();
        $query['happen_time'] = array('$lt' => \App\Common\Utils\Helper::getCurrentTime($yestderday));
        $modelActivityErrorLog->physicalRemove($query);

        ini_set('memory_limit', '4096M');
        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array('agentid' => '9999998');
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (empty($agentList)) {
                return;
            }

            // 创建service
            $lexianglaSettings = array(
                'AppKey' => '',
                'AppSecret' => '',
            );
            $serviceLexiangla = new \App\Lexiangla\Services\LexianglaService($lexianglaSettings['AppKey'], $lexianglaSettings['AppSecret']);

            foreach ($agentList as  $info) {
                try {
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = $info['agentid'];

                    $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);

                    $e = new \Exception("同步乐享的处理开始", 100000);
                    $this->recordResult($e, time());

                    // 同步部门
                    if (!empty($this->processFlags['sync_department'])) {
                        // 记录一下
                        $e = new \Exception("同步部门的处理开始", 101000);
                        $this->recordResult($e, time());

                        // 同步部门的处理 大体需要2分钟
                        $isOk = $this->syncDepartmentList($weixinopenService, $serviceLexiangla);
                        gc_collect_cycles();

                        // 记录一下
                        $e = new \Exception("同步部门的处理结束", 102000);
                        $this->recordResult($e, time());

                        if (!$isOk) {
                            throw new \Exception("同步部门的处理中发生了错误");
                        }
                    }

                    // 同步标签
                    if (!empty($this->processFlags['sync_tag'])) {
                        // 记录一下
                        $e = new \Exception("同步标签的处理开始", 103000);
                        $this->recordResult($e, time());

                        // 同步标签的处理
                        $isOk = $this->syncTagList($weixinopenService, $serviceLexiangla);
                        gc_collect_cycles();

                        // 记录一下
                        $e = new \Exception("同步标签的处理结束", 104000);
                        $this->recordResult($e, time());

                        if (!$isOk) {
                            throw new \Exception("同步标签的处理中发生了错误");
                        }
                    }

                    // 同步成员
                    if (!empty($this->processFlags['sync_user'])) {
                        // 记录一下
                        $e = new \Exception("同步成员的处理开始", 105000);
                        $this->recordResult($e, time());

                        // 同步成员的处理
                        $isOk = $this->syncUserList($weixinopenService, $serviceLexiangla);
                        gc_collect_cycles();

                        // 记录一下
                        $e = new \Exception("同步成员的处理结束", 106000);
                        $this->recordResult($e, time());

                        if (!$isOk) {
                            throw new \Exception("同步成员的处理中发生了错误");
                        }
                    }

                    // 同步标签成员
                    if (!empty($this->processFlags['sync_taguser'])) {
                        // 记录一下
                        $e = new \Exception("同步标签成员的处理开始", 107000);
                        $this->recordResult($e, time());

                        // 同步标签成员的处理
                        $isOk = $this->syncTagUserList($weixinopenService, $serviceLexiangla);
                        gc_collect_cycles();

                        // 记录一下
                        $e = new \Exception("同步标签成员的处理结束", 108000);
                        $this->recordResult($e, time());

                        if (!$isOk) {
                            throw new \Exception("同步标签成员的处理中发生了错误");
                        }
                    }

                    $e = new \Exception("同步乐享的处理正常结束", 199000);
                    $this->recordResult($e, time());
                } catch (\Exception $th) {
                    $error_msg = $th->getMessage();
                    $e = new \Exception("同步乐享的处理异常结束，错误信息：{$error_msg}", 199000);
                    $this->recordResult($e, time());
                }
            }
        } catch (\Exception $e) {
            $error_msg = $e->getMessage();
            $e = new \Exception("同步乐享的处理中发生了错误，错误信息：{$error_msg}", 199001);
            $this->recordResult($e, time());
        }
    }

    // 同步所有的部门
    protected function syncDepartmentList(
        \App\Qyweixin\Services\QyService $weixinopenService,
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla
    ) {
        $error_msg = "";
        $is_success = true;
        if (!empty($this->processFlags['sync_department_inner']['qyweixin_department_sync'])) {
            // 记录一下
            $e = new \Exception("同步企业微信的所有的部门数据开始", 101001);
            $this->recordResult($e, time());

            // 同步企业微信的所有的部门数据
            $total_num = 0;
            try {
                $this->modelLexianglaDepartmentSync->begin();

                $res1 = $weixinopenService->getDepartmentList(0);
                if (!empty($res1['department'])) {
                    $total_num += count($res1['department']);
                }
                unset($res1);
                $this->modelLexianglaDepartmentSync->commit();
            } catch (\Exception $e) {
                $this->modelLexianglaDepartmentSync->rollback();
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步企业微信的所有的部门数据异常结束，错误信息：{$error_msg}", 101002);
            } else {
                $e = new \Exception("同步企业微信的所有的部门数据正常结束, 获取企业微信的部门个数:{$total_num}", 101002);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_department_inner']['lexiangla_department_sync'])) {
            // 记录一下
            $e = new \Exception("同步乐享的所有的部门数据开始", 101003);
            $this->recordResult($e, time());
            // 同步乐享的所有的部门数据
            $total_num = 0;
            try {
                $this->modelLexianglaDepartmentSync->begin();

                $res2 = $serviceLexiangla->getDepartmentList(1);
                if (!empty($res2['data'])) {
                    $total_num += $this->getLexianglaDepartmentCount(array($res2['data']));
                }
                unset($res2);
                $this->modelLexianglaDepartmentSync->commit();
            } catch (\Exception $e) {
                $this->modelLexianglaDepartmentSync->rollback();
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步乐享的所有的部门数据异常结束，错误信息：{$error_msg}", 101004);
            } else {
                $e = new \Exception("同步乐享的所有的部门数据正常结束, 获取乐享的部门个数:{$total_num}", 101004);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_department_inner']['qyweixin_lexiangla_department_sync'])) {

            // 记录一下
            $e = new \Exception("企业微信和乐享平台之间的部门数据同步处理开始", 101005);
            $this->recordResult($e, time());

            // 从企业微信部门表中获取所有的数据
            $modelQyweixinDepartment = new \App\Qyweixin\Models\Contact\Department();
            $query = array();
            $query['authorizer_appid'] = $weixinopenService->getAuthorizerAppid();
            $query['provider_appid'] = $weixinopenService->getProviderAppid();
            $query['deptid'] = array('$ne' => '');
            $sort = array('_id' => 1);
            $qyweixinDepartmentList = $modelQyweixinDepartment->findAll($query, $sort);

            // 循环处理
            $total_num = 0;
            $success_num = 0;
            $error_num = 0;
            $errorList = array();
            foreach ($qyweixinDepartmentList as $qyweixinDepartmentInfo) {
                $total_num++;
                try {
                    $this->modelLexianglaDepartmentSync->begin();

                    // 处理同步单个部门
                    $this->syncSingleDepartment($serviceLexiangla, $qyweixinDepartmentInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num++;
                    // $param = array();
                    // $param['qyweixinDepartmentInfo'] = $qyweixinDepartmentInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 101006);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinDepartmentInfo' => $qyweixinDepartmentInfo, 'error_msg' => $e->getMessage());
                    $errorList[] = $param;
                    $error_num++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 101006);
                    $this->recordResult($e1, time());
                }
            }
            unset($qyweixinDepartmentList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num'] = $total_num;
            $ret['success_num'] = $success_num;
            $ret['error_num'] = $error_num;
            if ($error_num < 10) {
                $ret['errorList'] = $errorList;
            }
            $e = new \Exception("企业微信和乐享平台之间的部门数据同步处理结束1，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 101007);
            $this->recordResult($e, time());

            // 如果有错误的话 那么再执行一次
            $total_num2 = 0;
            $success_num2 = 0;
            $error_num2 = 0;
            $errorList2 = array();
            foreach ($errorList as $errorInfo) {
                $total_num++;
                $qyweixinDepartmentInfo = $errorInfo['qyweixinDepartmentInfo'];
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个部门
                    $this->syncSingleDepartment($serviceLexiangla, $qyweixinDepartmentInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num2++;
                    // $param = array();
                    // $param['qyweixinDepartmentInfo'] = $qyweixinDepartmentInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 101008);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinDepartmentInfo' => $qyweixinDepartmentInfo, 'error_msg' => $e->getMessage());
                    $errorList2[] = $param;
                    $error_num2++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 101008);
                    $this->recordResult($e1, time());
                }
            }
            unset($errorList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num2'] = $total_num2;
            $ret['success_num2'] = $success_num2;
            $ret['error_num2'] = $error_num2;
            if ($error_num2 < 10) {
                $ret['errorList2'] = $errorList2;
            }
            $e = new \Exception("企业微信和乐享平台之间的部门数据同步处理结束，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 101009);
            $this->recordResult($e, time());
        }

        return true;
    }

    // 同步单个部门
    protected function syncSingleDepartment($serviceLexiangla, $qyweixinDepartmentInfo)
    {
        // 查找乐享部门的同步表记录
        $departmentSyncInfo = $this->modelLexianglaDepartmentSync->getInfoByQyDeptId($qyweixinDepartmentInfo['deptid']);
        // 如果有找到的话
        $lexiangDepartmentInfo = array();
        if (!empty($departmentSyncInfo)) {
            // 获取乐享部门信息
            $lexiangDepartmentInfo = $this->modelLexianglaDepartment->getInfoByDepartmentId($departmentSyncInfo['deptid']);
        }

        // 如果记录是存在的 也就是企业微信上有这个记录所对应的部门
        if (!empty($qyweixinDepartmentInfo['is_exist'])) {
            // 如果没有找到的话
            if (empty($departmentSyncInfo)) {
                // 创建乐享部门同步信息                            
                $departmentSyncInfo = $this->doCreateDepartment($serviceLexiangla, $qyweixinDepartmentInfo);
            } else {
                // 如果未找到
                if (empty($lexiangDepartmentInfo)) {
                    // 删除乐享部门同步信息
                    $this->modelLexianglaDepartmentSync->physicalRemove(array('_id' => $departmentSyncInfo['id']));
                    // 创建乐享部门同步信息
                    $this->doCreateDepartment($serviceLexiangla, $qyweixinDepartmentInfo);
                } else { // 如果找到的话
                    // 如果部门名称或顺序发生了改变的话
                    $isChanged = $this->modelLexianglaDepartment->isDepartmentInfoChanged($qyweixinDepartmentInfo, $lexiangDepartmentInfo);
                    if ($isChanged) {
                        // 调用乐享更新部门接口更新部门处理
                        $this->doUpdateDepartment(
                            $serviceLexiangla,
                            $qyweixinDepartmentInfo,
                            $lexiangDepartmentInfo
                        );
                    }
                }
            }
        } else { // 如果记录不存在
            // 如果没有找到的话
            if (empty($departmentSyncInfo)) {
                //
            } else {
                // 如果未找到
                if (empty($lexiangDepartmentInfo)) {
                    //
                } else {
                    // 该乐享部门不存在的话 就是在乐享平台上该部门的记录不存在的意思
                    if (empty($lexiangDepartmentInfo['is_exist'])) {
                        //
                    } else { // 存在
                        // 调用乐享删除部门接口删除部门处理
                        $retCode = $this->doDeleteDepartment($serviceLexiangla, $lexiangDepartmentInfo);
                        // 如果是这几个code的话就不用做下面的处理
                        if (in_array($retCode, array(1004, 1005))) {
                            return;
                        }
                    }
                    // 删除乐享部门信息
                    $this->modelLexianglaDepartment->physicalRemove(array('_id' => $lexiangDepartmentInfo['id']));
                }
                // 删除乐享部门同步信息
                $this->modelLexianglaDepartmentSync->physicalRemove(array('_id' => $departmentSyncInfo['id']));
            }
            // 企业微信部门表的记录删除
            $modelQyweixinDepartment = new \App\Qyweixin\Models\Contact\Department();
            $modelQyweixinDepartment->physicalRemove(array('_id' => $qyweixinDepartmentInfo['id'], 'is_exist' => 0));
        }
    }

    // 创建乐享部门
    protected function doCreateDepartment(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $qyweixinDepartmentInfo
    ) {
        $qyweixin_deptid = $qyweixinDepartmentInfo['deptid'];
        $qyweixin_parentid = $qyweixinDepartmentInfo['parentid'];
        $qyweixin_order = $qyweixinDepartmentInfo['order'];
        $qyweixin_name = $qyweixinDepartmentInfo['name'];
        // 根据企业微信部门的父部门ID来获取乐享部门的父部门ID
        $parent_id = $this->modelLexianglaDepartmentSync->getParentIdByQyDeptParentid($qyweixin_parentid);
        if (empty($parent_id)) {
            throw new \Exception("企业微信部门ID:{$qyweixin_parentid}的乐享部门同步记录未找到");
        }

        // 创建乐享部门
        $params = array(
            "name" => $qyweixin_name,
            "parent_id" => $parent_id,
            "order" => $qyweixin_order,
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/department/create", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok",
         *      "data": {
         *          "id": 2
         *      }
         *  }
         *  错误码说明：

         *  CODE	说明
         *  1001	部门名包含非法字符
         *  1003	参数错误（parent_id或name为空）
         *  1004	父部门不能为空
         *  1005	该部门名称已经存在
         *  1006	服务错误，创建部门失败
         *  1007	部门层级不能超过15层
         *  1008	指定的部门id已经存在
         *  1009	部门id不合法
         *  800401	仅自建账号企业类型可调用
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("创建部门失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!empty($retApi['code'])) {
            // 1005 不算错误
            if (intval($retApi['code']) == 1005) {

                if (empty($retApi['data']) || empty($retApi['data']['id'])) {
                    // 从乐享部门表中获取数据deptid
                    $lexianglaDepartmentInfo = $this->modelLexianglaDepartment->getInfoByDepartmentName($qyweixin_name);
                    if (!empty($lexianglaDepartmentInfo)) {
                        $retApi['data']['id'] = $lexianglaDepartmentInfo['deptid'];
                    }
                }
            } else {
                throw new \Exception("创建部门失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }
        if (empty($retApi['data']) || empty($retApi['data']['id'])) {
            throw new \Exception("创建部门失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        $deptid = $retApi['data']['id'];

        // 记录乐享部门同步信息
        $departmentSyncInfo = array();
        $departmentSyncInfo['qyweixin_deptid'] = $qyweixin_deptid;
        $departmentSyncInfo['qyweixin_parentid'] = $qyweixin_parentid;
        $departmentSyncInfo['deptid'] = $deptid;
        $departmentSyncInfo['parentid'] = $parent_id;
        $departmentSyncInfo['sync_time'] = \App\Common\Utils\Helper::getCurrentTime();
        $departmentSyncInfo['memo'] = \App\Common\Utils\Helper::myJsonEncode($retApi);
        $departmentSyncInfo = $this->modelLexianglaDepartmentSync->insert($departmentSyncInfo);

        // 记录乐享部门信息
        $lexianglaDepartmentInfo = $this->modelLexianglaDepartment->getInfoByDepartmentId($deptid);
        // 如果没有乐享部门信息 就创建一条
        if (empty($lexianglaDepartmentInfo)) {
            $lexianglaDepartmentInfo = array();
            $lexianglaDepartmentInfo['deptid'] = $deptid;
            $lexianglaDepartmentInfo['parent_id'] = $parent_id;
            $lexianglaDepartmentInfo['order'] = $qyweixin_order;
            $lexianglaDepartmentInfo['name'] = $qyweixin_name;
            $lexianglaDepartmentInfo['is_exist'] = 1;
            $lexianglaDepartmentInfo['sync_time'] = \App\Common\Utils\Helper::getCurrentTime();
            $lexianglaDepartmentInfo['memo'] = \App\Common\Utils\Helper::myJsonEncode($retApi);
            $lexianglaDepartmentInfo = $this->modelLexianglaDepartment->insert($lexianglaDepartmentInfo);
        }
        return $departmentSyncInfo;
    }

    // 删除乐享部门
    protected function doDeleteDepartment(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $lexiangDepartmentInfo
    ) {
        $deptid = $lexiangDepartmentInfo['deptid'];
        // 删除乐享部门
        $params = array(
            "id" => $deptid
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/department/delete", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok"
         *  }
         *  错误码说明：

         *  CODE	说明
         *  1003	参数错误（id为空）
         *  1002	该部门不存在
         *  1004	该部门下存在子部门，无法删除
         *  1005	该部门下存在用户，无法删除
         *  1006	该部门是根部门不能被删除
         *  800401	仅自建账号企业类型可调用
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("删除部门失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }

        if (!empty($retApi['code'])) {
            // 1002 不算错误 
            if (intval($retApi['code']) == 1002) {
            } elseif (intval($retApi['code']) == 1004) {
                return $retApi['code'];
            } elseif (intval($retApi['code']) == 1005) {
                return $retApi['code'];
            } else {
                throw new \Exception("删除部门失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }
    }

    // 更新乐享部门
    protected function doUpdateDepartment(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $qyweixinDepartmentInfo,
        $lexiangDepartmentInfo
    ) {
        $deptid = $lexiangDepartmentInfo['deptid'];
        // 更新乐享部门
        $params = array(
            "id" => $deptid,
            "name" => $qyweixinDepartmentInfo['name'],
            "order" => $qyweixinDepartmentInfo['order']
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/department/update", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok"
         *  }
         *  错误码说明：

         *  CODE	说明
         *  1001	部门名包含非法字符或部门名称大于32个字符
         *  1002	该部门不存在
         *  1003	参数错误
         *  1005	该部门名称已经存在
         *  1005	修改失败
         *  1006	order值不能为负数
         *  1007	部门层级不能超过15级
         *  800401	仅自建账号企业类型可调用
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("更新部门失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!empty($retApi['code'])) {
            throw new \Exception("更新部门失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        $this->modelLexianglaDepartment->update(array('_id' => $lexiangDepartmentInfo['_id']), array('$set' => array(
            "name" => $qyweixinDepartmentInfo['name'],
            "order" => $qyweixinDepartmentInfo['order'],
            "memo" => \App\Common\Utils\Helper::myJsonEncode($retApi)
        )));
    }

    // 同步所有的标签
    protected function syncTagList(
        \App\Qyweixin\Services\QyService $weixinopenService,
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla
    ) {
        $error_msg = "";
        $is_success = true;

        if (!empty($this->processFlags['sync_tag_inner']['qyweixin_tag_sync'])) {
            // 记录一下
            $e = new \Exception("同步企业微信的所有的标签数据开始", 103001);
            $this->recordResult($e, time());

            // 同步企业微信的所有的标签数据
            $total_num = 0;
            try {
                $this->modelLexianglaDepartmentSync->begin();
                $res1 = $weixinopenService->getTagList();
                if (!empty($res1['taglist'])) {
                    $total_num += count($res1['taglist']);
                }
                unset($res1);
                $this->modelLexianglaDepartmentSync->commit();
            } catch (\Exception $e) {
                $this->modelLexianglaDepartmentSync->rollback();
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步企业微信的所有的标签数据异常结束，错误信息：{$error_msg}", 103002);
            } else {
                $e = new \Exception("同步企业微信的所有的标签数据正常结束，获取企业微信的标签个数：{$total_num}", 103002);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_tag_inner']['lexiangla_tag_sync'])) {
            // 记录一下
            $e = new \Exception("同步乐享的所有的标签数据开始", 103003);
            $this->recordResult($e, time());
            // 同步乐享的所有的标签数据
            $total_num = 0;
            try {
                $this->modelLexianglaDepartmentSync->begin();
                $res2 = $serviceLexiangla->getTagList();
                if (!empty($res2['data']['list'])) {
                    $total_num += count($res2['data']['list']);
                }
                unset($res2);
                $this->modelLexianglaDepartmentSync->commit();
            } catch (\Exception $e) {
                $this->modelLexianglaDepartmentSync->rollback();
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步乐享的所有的标签数据异常结束，错误信息：{$error_msg}", 103004);
            } else {
                $e = new \Exception("同步乐享的所有的标签数据正常结束，获取乐享的标签个数：{$total_num}", 103004);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_tag_inner']['qyweixin_lexiangla_tag_sync'])) {
            // 记录一下
            $e = new \Exception("企业微信和乐享平台之间的标签数据同步处理开始", 103005);
            $this->recordResult($e, time());

            // 从企业微信标签表中获取所有的数据
            $modelQyweixinTag = new \App\Qyweixin\Models\Contact\Tag();
            $query = array();
            $query['authorizer_appid'] = $weixinopenService->getAuthorizerAppid();
            $query['provider_appid'] = $weixinopenService->getProviderAppid();
            $query['tagid'] = array('$ne' => '');
            $sort = array('_id' => 1);
            $qyweixinTagList = $modelQyweixinTag->findAll($query, $sort);

            // 循环处理
            $total_num = 0;
            $success_num = 0;
            $error_num = 0;
            $errorList = array();
            foreach ($qyweixinTagList as $qyweixinTagInfo) {
                $total_num++;
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个标签
                    $this->syncSingleTag($serviceLexiangla, $qyweixinTagInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num++;
                    // $param = array();
                    // $param['qyweixinTagInfo'] = $qyweixinTagInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 103006);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinTagInfo' => $qyweixinTagInfo, 'error_msg' => $e->getMessage());
                    $errorList[] = $param;
                    $error_num++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 103006);
                    $this->recordResult($e1, time());
                }
            }
            unset($qyweixinTagList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num'] = $total_num;
            $ret['success_num'] = $success_num;
            $ret['error_num'] = $error_num;
            if ($error_num < 10) {
                $ret['errorList'] = $errorList;
            }
            $e = new \Exception("企业微信和乐享平台之间的标签数据同步处理结束1，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 103007);
            $this->recordResult($e, time());

            // 如果有错误的话 那么再执行一次
            $total_num2 = 0;
            $success_num2 = 0;
            $error_num2 = 0;
            $errorList2 = array();
            foreach ($errorList as $errorInfo) {
                $total_num2++;
                $qyweixinTagInfo = $errorInfo['qyweixinTagInfo'];
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个标签
                    $this->syncSingleTag($serviceLexiangla, $qyweixinTagInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num2++;
                    // $param = array();
                    // $param['qyweixinTagInfo'] = $qyweixinTagInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 103008);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinTagInfo' => $qyweixinTagInfo, 'error_msg' => $e->getMessage());
                    $errorList2[] = $param;
                    $error_num2++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 103008);
                    $this->recordResult($e1, time());
                }
            }
            unset($errorList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num2'] = $total_num2;
            $ret['success_num2'] = $success_num2;
            $ret['error_num2'] = $error_num2;
            if ($error_num2 < 10) {
                $ret['errorList2'] = $errorList2;
            }
            $e = new \Exception("企业微信和乐享平台之间的标签数据同步处理结束2，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 103009);
            $this->recordResult($e, time());
        }

        return true;
    }

    // 同步单个标签
    protected function syncSingleTag($serviceLexiangla, $qyweixinTagInfo)
    {
        // 查找乐享标签的同步表记录
        $tagSyncInfo = $this->modelLexianglaTagSync->getInfoByQyTagId($qyweixinTagInfo['tagid']);

        // 如果有找到的话
        $lexiangTagInfo = array();
        if (!empty($tagSyncInfo)) {
            // 获取乐享标签信息
            $lexiangTagInfo = $this->modelLexianglaTag->getInfoByTagId($tagSyncInfo['tagid']);
        }

        // 如果记录是存在的 也就是企业微信上有这个记录所对应的标签
        if (!empty($qyweixinTagInfo['is_exist'])) {
            // 如果没有找到的话
            if (empty($tagSyncInfo)) {
                // 创建乐享标签同步信息                            
                $tagSyncInfo = $this->doCreateTag($serviceLexiangla, $qyweixinTagInfo);
            } else {
                // 如果未找到
                if (empty($lexiangTagInfo)) {
                    // 删除乐享标签同步信息
                    $this->modelLexianglaTagSync->physicalRemove(array('_id' => $tagSyncInfo['_id']));
                    // 创建乐享标签同步信息
                    $this->doCreateTag($serviceLexiangla, $qyweixinTagInfo);
                } else { // 如果找到的话
                    // 如果标签名称发生了改变的话
                    $isChanged = $this->modelLexianglaTag->isTagInfoChanged($qyweixinTagInfo, $lexiangTagInfo);
                    if ($isChanged) {
                        // 调用乐享更新标签接口更新标签处理
                        $this->doUpdateTag(
                            $serviceLexiangla,
                            $qyweixinTagInfo,
                            $lexiangTagInfo
                        );
                    }
                }
            }
        } else { // 如果记录不存在
            // 如果没有找到的话
            if (empty($tagSyncInfo)) {
                //
            } else {
                // 如果未找到
                if (empty($lexiangTagInfo)) {
                    //
                } else {
                    // 该乐享标签不存在的话 就是在乐享平台上该标签的记录不存在的意思
                    if (empty($lexiangTagInfo['is_exist'])) {
                        //
                    } else { // 存在
                        // 调用乐享删除标签接口删除标签处理
                        $this->doDeleteTag($serviceLexiangla, $lexiangTagInfo);
                    }
                    // 删除乐享标签信息
                    $this->modelLexianglaTag->physicalRemove(array('_id' => $lexiangTagInfo['_id']));
                }
                // 删除乐享标签同步信息
                $this->modelLexianglaTagSync->physicalRemove(array('_id' => $tagSyncInfo['_id']));
            }
            // 企业微信标签表的记录删除
            $modelQyweixinTag = new \App\Qyweixin\Models\Contact\Tag();
            $modelQyweixinTag->physicalRemove(array('_id' => $qyweixinTagInfo['_id'], 'is_exist' => 0));
        }
    }

    // 创建乐享标签
    protected function doCreateTag(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $qyweixinTagInfo
    ) {
        $qyweixin_tagid = $qyweixinTagInfo['tagid'];
        $qyweixin_name = $qyweixinTagInfo['tagname'];
        // 创建乐享标签
        $params = array(
            "name" => $qyweixin_name
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/tag/create", $params);
        /**
         * {
         *      "msg": "success",
         *       "code": 0,
         *       "data": {
         *           "id": "3e64cd98ff0511eb97d0623bfcb7341c"
         *       }
         *   }
         *  错误码说明：

         *  CODE	说明
         *  20011	标签已经存在
         */
        $params['retApi'] = $retApi;
        // print_r($params);
        // die('xxxxxxxx');

        if (empty($retApi)) {
            throw new \Exception("创建标签失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!empty($retApi['code'])) {
            // 20011 不算错误
            if (intval($retApi['code']) == 20011) {
                if (empty($retApi['data']) || empty($retApi['data']['id'])) {
                    // 从乐享标签表中获取数据tagid
                    $lexianglaTagInfo = $this->modelLexianglaTag->getInfoByTagName($qyweixin_name);
                    if (!empty($lexianglaTagInfo)) {
                        $retApi['data']['id'] = $lexianglaTagInfo['tagid'];
                    }
                }
            } else {
                throw new \Exception("创建标签失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }
        if (empty($retApi['data']) || empty($retApi['data']['id'])) {
            throw new \Exception("创建标签失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        $tagid = $retApi['data']['id'];

        // 记录乐享标签同步信息
        $tagSyncInfo = array();
        $tagSyncInfo['qyweixin_tagid'] = $qyweixin_tagid;
        $tagSyncInfo['tagid'] = $tagid;
        $tagSyncInfo['sync_time'] = \App\Common\Utils\Helper::getCurrentTime();
        $tagSyncInfo['memo'] = \App\Common\Utils\Helper::myJsonEncode($retApi);
        $tagSyncInfo = $this->modelLexianglaTagSync->insert($tagSyncInfo);

        // 记录乐享标签信息
        $lexianglaTagInfo = $this->modelLexianglaTag->getInfoByTagId($tagid);
        // 如果没有乐享标签信息 就创建一条
        if (empty($lexianglaTagInfo)) {
            $lexianglaTagInfo = array();
            $lexianglaTagInfo['tagid'] = $tagid;
            $lexianglaTagInfo['tagname'] = $qyweixin_name;
            $lexianglaTagInfo['is_exist'] = 1;
            $lexianglaTagInfo['sync_time'] = \App\Common\Utils\Helper::getCurrentTime();
            $lexianglaTagInfo['memo'] = \App\Common\Utils\Helper::myJsonEncode($retApi);
            $lexianglaTagInfo = $this->modelLexianglaTag->insert($lexianglaTagInfo);
        }
        return $tagSyncInfo;
    }

    // 删除乐享标签
    protected function doDeleteTag(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $lexiangTagInfo
    ) {
        $tagid = $lexiangTagInfo['tagid'];
        // 删除乐享标签
        $params = array(
            "id" => $tagid
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/tag/delete", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok"
         *  }
         *  错误码说明：

         *  CODE	说明
         *  20010	标签id不存在
         *  20013	非乐享标签不支持删除
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("删除标签失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }

        if (!empty($retApi['code'])) {
            // 20010 不算错误 
            if (intval($retApi['code']) == 20010) {
            } else {
                throw new \Exception("删除标签失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }
    }

    // 更新乐享标签
    protected function doUpdateTag(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $qyweixinTagInfo,
        $lexiangTagInfo
    ) {
        $tagid = $lexiangTagInfo['tagid'];
        // 更新乐享标签
        $params = array(
            "id" => $tagid,
            "name" => $qyweixinTagInfo['tagname']
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/tag/update", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok"
         *  }
         *  错误码说明：

         *  CODE	说明
         *  20010	标签不存在
         *  20011	标签名对应的标签已经存在
         *  20012	非乐享标签不支持修改
         *  30012	标签名称只能是中文和英文字符，且长度不能超过32
         *  30013	用户列表只能为数组且长度不能超过1000
         *  30014	部门列表只能为数组且长度不能超过1000
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("更新标签失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!empty($retApi['code'])) {
            throw new \Exception("更新标签失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        $this->modelLexianglaTag->update(array('_id' => $lexiangTagInfo['_id']), array('$set' => array(
            "tagname" => $qyweixinTagInfo['tagname'],
            "memo" => \App\Common\Utils\Helper::myJsonEncode($retApi)
        )));
    }

    // 同步所有的成员
    protected function syncUserList(
        \App\Qyweixin\Services\QyService $weixinopenService,
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla
    ) {
        $error_msg = "";
        $is_success = true;
        if (!empty($this->processFlags['sync_user_inner']['qyweixin_department_user_sync'])) {
            // 记录一下
            $e = new \Exception("同步企业微信的所有的部门成员数据开始", 105001);
            $this->recordResult($e, time());

            // 同步企业微信的所有的成员数据
            $total_num = 0;
            try {
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // $res1 = $weixinopenService->getDepartmentUserDetaillist(1, 1, true);
                    $res1 = $weixinopenService->getDepartmentUserSimplelist(1, 1, true);
                    if (!empty($res1['userlist'])) {
                        $total_num += count($res1['userlist']);
                    }
                    unset($res1);
                    $this->modelLexianglaDepartmentSync->commit();
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    throw $e;
                }
            } catch (\Exception $e) {
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步企业微信的所有的部门成员数据异常结束，错误信息：{$error_msg}", 105002);
            } else {
                $e = new \Exception("同步企业微信的所有的部门成员数据正常结束，获取企业微信的部门成员个数：{$total_num}", 105002);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_user_inner']['qyweixin_user_sync'])) {
            // 记录一下
            $e = new \Exception("同步企业微信的所有的成员数据开始", 105003);
            $this->recordResult($e, time());

            // 同步企业微信的所有的成员数据
            $total_num = 0;
            try {
                // 对成员表进行处理，获取最新的成员信息
                $modelQyweixinUser = new \App\Qyweixin\Models\Contact\User();
                $query = array();
                $query['authorizer_appid'] = $weixinopenService->getAuthorizerAppid();
                $query['provider_appid'] = $weixinopenService->getProviderAppid();
                $query['userid'] = array('$ne' => '');
                $sort = array('_id' => 1);
                $userList = $modelQyweixinUser->findAll($query, $sort);

                if (!empty($userList)) {
                    foreach ($userList as $info) {
                        try {
                            // $this->modelLexianglaDepartmentSync->begin();
                            // 检查是否在部门用户表中存在
                            $isExist = $this->checkUserIsExist($info);
                            if ($isExist) {
                                $weixinopenService->getUserInfo($info);
                                $total_num++;
                            }
                            // $this->modelLexianglaDepartmentSync->commit();
                        } catch (\Exception $e) {
                            // $this->modelLexianglaDepartmentSync->rollback();
                            $param = array();
                            $param['error_msg']  = $e->getMessage();
                            $param['info'] = $info;
                            $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 105004);
                            $this->recordResult($e1, time());
                        }
                    }
                }
                unset($userList);
            } catch (\Exception $e) {
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步企业微信的所有的成员数据异常结束，错误信息：{$error_msg}", 105005);
            } else {
                $e = new \Exception("同步企业微信的所有的成员数据正常结束，获取企业微信的成员个数：{$total_num}", 105005);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_user_inner']['lexiangla_department_user_sync'])) {
            // 记录一下
            $e = new \Exception("同步乐享的所有的部门成员数据开始", 105006);
            $this->recordResult($e, time());
            // 同步乐享的所有的成员数据
            $total_num = 0;
            try {
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    $res2 = $serviceLexiangla->getUserList();
                    if (!empty($res2['user_list'])) {
                        $total_num += count($res2['user_list']);
                    }
                    unset($res2);
                    $this->modelLexianglaDepartmentSync->commit();
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    throw $e;
                }
            } catch (\Exception $e) {
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步乐享的所有的部门成员数据异常结束，错误信息：{$error_msg}", 105007);
            } else {
                $e = new \Exception("同步乐享的所有的部门成员数据正常结束，获取乐享的部门成员个数：{$total_num}", 105007);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_user_inner']['lexiangla_user_sync'])) {
            // 记录一下
            $e = new \Exception("同步乐享的所有的成员数据开始", 105008);
            $this->recordResult($e, time());
            // 同步乐享的所有的成员数据
            $total_num = 0;
            try {
                // 对乐享成员表进行处理，获取最新的成员信息
                $modelLexianglaUser = new \App\Lexiangla\Models\Contact\User();
                $query = array();
                $query['staff_id'] = array('$ne' => '');
                $query['is_exist'] = 1;
                $sort = array('_id' => 1);
                $lexianglaUserList = $modelLexianglaUser->findAll($query, $sort);
                if (!empty($lexianglaUserList)) {
                    foreach ($lexianglaUserList as $info) {
                        try {
                            // $this->modelLexianglaDepartmentSync->begin();
                            $serviceLexiangla->getUserInfo($info);
                            $total_num++;
                            // $this->modelLexianglaDepartmentSync->commit();
                        } catch (\Exception $e) {
                            // $this->modelLexianglaDepartmentSync->rollback();
                            $param = array();
                            $param['error_msg'] = $e->getMessage();
                            $param['info'] = $info;
                            $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 105009);
                            $this->recordResult($e1, time());
                        }
                    }
                }
                unset($lexianglaUserList);
            } catch (\Exception $e) {
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步乐享的所有的成员数据异常结束，错误信息：{$error_msg}", 105010);
            } else {
                $e = new \Exception("同步乐享的所有的成员数据正常结束，获取乐享的成员个数：{$total_num}", 105010);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_user_inner']['qyweixin_lexiangla_user_sync'])) {
            // 记录一下
            $e = new \Exception("企业微信和乐享平台之间的成员数据同步处理开始", 105011);
            $this->recordResult($e, time());

            // 从企业微信成员表中获取所有的数据
            $modelQyweixinUser = new \App\Qyweixin\Models\Contact\User();
            $query = array();
            $query['authorizer_appid'] = $weixinopenService->getAuthorizerAppid();
            $query['provider_appid'] = $weixinopenService->getProviderAppid();
            $query['userid'] = array('$ne' => '');
            $query['mobile'] = array('$ne' => '');
            $sort = array('_id' => 1);
            $qyweixinUserList = $modelQyweixinUser->findAll($query, $sort);

            // 循环处理
            $total_num = 0;
            $success_num = 0;
            $error_num = 0;
            $errorList = array();
            foreach ($qyweixinUserList as $qyweixinUserInfo) {
                $total_num++;
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个成员
                    $this->syncSingleUser($serviceLexiangla, $qyweixinUserInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num++;
                    // $param = array();
                    // $param['qyweixinUserInfo'] = $qyweixinUserInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 105012);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinUserInfo' => $qyweixinUserInfo, 'error_msg' => $e->getMessage());
                    $errorList[] = $param;
                    $error_num++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 105012);
                    $this->recordResult($e1, time());
                }
            }
            unset($qyweixinUserList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num'] = $total_num;
            $ret['success_num'] = $success_num;
            $ret['error_num'] = $error_num;
            if ($error_num < 10) {
                $ret['errorList'] = $errorList;
            }
            $e = new \Exception("企业微信和乐享平台之间的成员数据同步处理结束1，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 105013);
            $this->recordResult($e, time());

            // 如果有错误的话 那么再执行一次
            $total_num2 = 0;
            $success_num2 = 0;
            $error_num2 = 0;
            $errorList2 = array();
            foreach ($errorList as $errorInfo) {
                $total_num2++;
                $qyweixinUserInfo = $errorInfo['qyweixinUserInfo'];
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个成员
                    $this->syncSingleUser($serviceLexiangla, $qyweixinUserInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num2++;
                    // $param = array();
                    // $param['qyweixinUserInfo'] = $qyweixinUserInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 105014);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinUserInfo' => $qyweixinUserInfo, 'error_msg' => $e->getMessage());
                    $errorList2[] = $param;
                    $error_num2++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 105014);
                    $this->recordResult($e1, time());
                }
            }
            unset($errorList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num2'] = $total_num2;
            $ret['success_num2'] = $success_num2;
            $ret['error_num2'] = $error_num2;
            if ($error_num2 < 10) {
                $ret['errorList2'] = $errorList2;
            }
            $e = new \Exception("企业微信和乐享平台之间的成员数据同步处理结束2，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 105015);
            $this->recordResult($e, time());
        }

        return true;
    }

    // 同步单个成员
    protected function syncSingleUser($serviceLexiangla, $qyweixinUserInfo)
    {
        // 成员用于存在
        // 检查是否在部门用户表中存在
        $isExist = $this->checkUserIsExist($qyweixinUserInfo);
        $qyweixinUserInfo['is_exist'] = $isExist;

        // 查找乐享成员的同步表记录
        // $userSyncInfo = $this->modelLexianglaUserSync->getInfoByQyUserId($qyweixinUserInfo['userid']);
        $userSyncInfo = array();
        $userSyncInfo['staffid'] = $qyweixinUserInfo['userid'];

        // 获取乐享成员信息
        $lexiangUserInfo = $this->modelLexianglaUser->getInfoByStaffId($userSyncInfo['staffid']);

        // 如果记录是存在的 也就是企业微信上有这个记录所对应的成员
        if (!empty($qyweixinUserInfo['is_exist'])) {
            // 如果没有找到的话
            if (empty($userSyncInfo)) {
                // 创建乐享成员同步信息                            
                $userSyncInfo = $this->doCreateUser($serviceLexiangla, $qyweixinUserInfo);
            } else {
                // 如果未找到
                if (empty($lexiangUserInfo)) {
                    // // 删除乐享成员同步信息
                    // \App\Components\Lexiangla\Models\Contact\UserSync::where('id', $userSyncInfo['id'])->forceDelete();
                    // 创建乐享成员同步信息
                    $this->doCreateUser($serviceLexiangla, $qyweixinUserInfo);
                } else { // 如果找到的话
                    // 通过企业微信部门ID获取对应的乐享部门ID
                    $main_depart = $this->getLexianglaDepartmentId($qyweixinUserInfo['main_department']);
                    $department = $this->getLexianglaDepartment($qyweixinUserInfo['department']);
                    // 如果成员名称发生了改变的话
                    $isChanged = $this->modelLexianglaUser->isUserInfoChanged($qyweixinUserInfo, $lexiangUserInfo, $main_depart, $department);
                    if ($isChanged) {
                        // 调用乐享更新成员接口更新成员处理
                        $this->doUpdateUser(
                            $serviceLexiangla,
                            $qyweixinUserInfo,
                            $lexiangUserInfo,
                            $main_depart,
                            $department
                        );
                    }
                }
            }
        } else { // 如果记录不存在
            // 如果没有找到的话
            if (empty($userSyncInfo)) {
                //
            } else {
                // 如果未找到
                if (empty($lexiangUserInfo)) {
                    //
                } else {
                    // 该乐享成员不存在的话 就是在乐享平台上该成员的记录不存在的意思
                    if (empty($lexiangUserInfo['is_exist'])) {
                        //
                    } else { // 存在
                        // 调用乐享删除成员接口删除成员处理
                        $this->doDeleteUser($serviceLexiangla, $lexiangUserInfo);
                    }
                    // 删除乐享成员信息
                    $this->modelLexianglaUser->physicalRemove(array('_id' => $lexiangUserInfo['_id']));
                }
                // // 删除乐享成员同步信息
                // $this->modelLexianglaUserSync->physicalRemove(array('_id' => $userSyncInfo['_id']));
            }
            // 企业微信部门成员表的记录删除
            $modelQyweixinDepartmentUser = new \App\Qyweixin\Models\Contact\DepartmentUser();
            $modelQyweixinDepartmentUser->physicalRemove(array(
                'userid' => $qyweixinUserInfo['userid'],
                'authorizer_appid' => $qyweixinUserInfo['authorizer_appid'],
                'provider_appid' => $qyweixinUserInfo['provider_appid']
            ));
            // $modelQyweixinUser = new \App\Qyweixin\Models\Contact\User();
            // $modelQyweixinUser->physicalRemove(array('_id' => $qyweixinUserInfo['_id']));
        }
    }

    // 创建乐享成员
    protected function doCreateUser(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $qyweixinUserInfo
    ) {
        $qyweixin_userid = $qyweixinUserInfo['userid'];
        $qyweixin_name = $qyweixinUserInfo['name'];
        $qyweixin_english_name = $qyweixinUserInfo['english_name'];
        $qyweixin_mobile = $qyweixinUserInfo['mobile'];
        if (empty($qyweixin_mobile)) {
            return array();
        }
        $qyweixin_gender = intval($qyweixinUserInfo['gender']);
        // 1表示男性，2表示女性。默认0表示未定义
        if ($qyweixin_gender != 1 && $qyweixin_gender != 2) {
            $qyweixin_gender = 0;
        }
        $qyweixin_position = $qyweixinUserInfo['position'];
        $qyweixin_telephone = $qyweixinUserInfo['telephone'];
        $qyweixin_external_position = $qyweixinUserInfo['external_position'];

        // 通过企业微信部门ID获取对应的乐享部门ID
        $main_depart = $this->getLexianglaDepartmentId($qyweixinUserInfo['main_department']);
        $department = $this->getLexianglaDepartment($qyweixinUserInfo['department']);

        $country_code = "86";
        $birthday_calendar = "solar";
        $avatar = $this->getLexianglaAvatar($serviceLexiangla, $qyweixin_userid, $qyweixinUserInfo['avatar']);

        // 创建乐享成员
        $params = array(
            "staff_id" => $qyweixin_userid,
            "name" => $qyweixin_name,
            // "english_name" => $qyweixin_english_name,
            "country_code" => $country_code,
            "phone" => $qyweixin_mobile,
            "department" => $department,
            "main_depart" => $main_depart,
            // "position" => $qyweixin_position,
            // "work_position" => $qyweixin_external_position,
            "gender" => $qyweixin_gender,
            // "avatar" => $avatar,
            "birthday_calendar" => $birthday_calendar,
            // "solar_birthday_date" => "2000-01-01",
            // "solar_entryday_date" => "2000-01-01",
            // "tel_phone" => $qyweixin_telephone,
            // "id_card" => ""
        );
        if (!empty($qyweixin_english_name)) {
            $params['english_name'] = $qyweixin_english_name;
        }
        if (!empty($avatar)) {
            $params['avatar'] = $avatar;
        }
        if (!empty($qyweixin_position)) {
            $params['position'] = $qyweixin_position;
        }
        if (!empty($qyweixin_external_position)) {
            $params['work_position'] = $qyweixin_external_position;
        }
        if (!empty($qyweixin_telephone)) {
            $params['tel_phone'] = $qyweixin_telephone;
        }
        // print_r($params);
        // die('xxxxxxxxx');
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/user/create", $params);
        /**
         * {
         *      "msg": "success",
         *       "code": 0,
         *       "data": {
         *           "id": "zhangsan"
         *       }
         *   }
         *  错误码说明：
    
         *  CODE ID	说明
         *  1001	staff_id不能为空或staff_id不能包含特殊字符或长度大于64个字符
         *  1002	name不能为空
         *  1003	手机号不能为空
         *  1004	该staff_id已经存在
         *  1005	用户创建错误, 内部错误
         *  1102	birthday_calendar只能为lunar和solar
         *  1103	department必须为数组或部门列表不能超过20
         *  1104	entryday_date日期格式错误
         *  1105	成员名不能包含非法字符或者长度不能超过64
         *  1106	性别只能为0,1,2
         *  1107	职务信息不能为空或包含特殊字符或大于128个字符
         *  1207	职位work_position信息不能为空或包含特殊字符或大于128个字符
         *  1008	成员数已达到上限，如需扩容请拨打400-780-0088
         *  1109	该手机号已经被注册或邀请
         *  1111	solar_birthday_date日期格式错误
         *  1201	别名不能包含非法字符或者长度不能超过64
         *  1311	联系电话格式错误或者联系电话已经被他人占用
         *  1310	身份证号格式错误或者身份证号已经被他人占用
         *  800401	仅自建账号企业类型可调用
         */
        $params['retApi'] = $retApi;
        // print_r($params);
        // die('xxxxxxxx');

        if (empty($retApi)) {
            throw new \Exception("创建成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!empty($retApi['code'])) {
            // 1004 不算错误
            if (intval($retApi['code']) == 1004) {
                if (empty($retApi['data']) || empty($retApi['data']['id'])) {
                    $retApi['data']['id'] = $qyweixin_userid;
                }
            } // 1202 员工已经离职 需要调用
            elseif (intval($retApi['code']) == 1202) {
                // 离职成员重新入职
                $params2 = array(
                    "staff_id" => $qyweixin_userid,
                    "department" => $department
                );
                $retApi2 = $serviceLexiangla->getLxapiObject()->post("contact/user/entry", $params2);
                if (empty($retApi2)) {
                    throw new \Exception("离职成员重新入职失败:" . \App\Common\Utils\Helper::myJsonEncode($params2));
                }
                if (!empty($retApi2['code'])) {
                    throw new \Exception("离职成员重新入职失败:" . \App\Common\Utils\Helper::myJsonEncode($params2));
                }
                $retApi['data']['id'] = $qyweixin_userid;
            } else {
                throw new \Exception("创建成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }
        if (empty($retApi['data']) || empty($retApi['data']['id'])) {
            throw new \Exception("创建成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        $staffid = $retApi['data']['id'];

        // // 记录乐享成员同步信息
        // $userSyncInfo = new \App\Components\Lexiangla\Models\Contact\UserSyncModel();
        // $userSyncInfo->qyweixin_userid = $qyweixin_userid;
        // $userSyncInfo->staffid = $staffid;
        // $userSyncInfo->sync_time = date('Y-m-d H:i:s');
        // $userSyncInfo->memo = \App\Common\Utils\Helper::myJsonEncode($retApi);
        // $userSyncInfo->save();

        // 记录乐享成员信息
        $lexianglaUserInfo = $this->modelLexianglaUser->getInfoByStaffId($staffid);
        // 如果没有乐享成员信息 就创建一条
        if (empty($lexianglaUserInfo)) {
            $lexianglaUserInfo = array();
            $lexianglaUserInfo['staff_id'] = $staffid;
            $lexianglaUserInfo['name'] = $qyweixin_name;
            $lexianglaUserInfo['english_name'] = $qyweixin_english_name;
            $lexianglaUserInfo['country_code'] = $country_code;
            $lexianglaUserInfo['phone'] = $qyweixin_mobile;
            $lexianglaUserInfo['department'] = \App\Common\Utils\Helper::myJsonEncode($department);
            $lexianglaUserInfo['main_depart'] = $main_depart;
            $lexianglaUserInfo['position'] = $qyweixin_position;
            $lexianglaUserInfo['work_position'] = $qyweixin_external_position;
            $lexianglaUserInfo['gender'] = $qyweixin_gender;
            $lexianglaUserInfo['avatar'] = $avatar;
            $lexianglaUserInfo['birthday_calendar'] = $birthday_calendar;
            // $lexianglaUserInfo['solar_birthday_date'] = $solar_birthday_date;
            // $lexianglaUserInfo['solar_entryday_date'] = $solar_entryday_date;
            $lexianglaUserInfo['tel_phone'] = $qyweixin_telephone;
            // $lexianglaUserInfo['id_card'] = $id_card;
            $lexianglaUserInfo['is_exist'] = 1;
            $lexianglaUserInfo['sync_time'] = \App\Common\Utils\Helper::getCurrentTime();
            $lexianglaUserInfo['memo'] = \App\Common\Utils\Helper::myJsonEncode($retApi);
            $lexianglaUserInfo = $this->modelLexianglaUser->insert($lexianglaUserInfo);
        }
        return $lexianglaUserInfo;
    }

    // 删除乐享成员
    protected function doDeleteUser(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $lexiangUserInfo
    ) {
        $staff_id = $lexiangUserInfo['staff_id'];
        // 删除乐享成员
        $params = array(
            "staff_id" => $staff_id
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/user/resign", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok"
         *  }
         *  错误码说明：
    
         *  CODE ID	说明
         *  3001	staff_id对应用户不存在
         *  3003	用户已经删除，请勿重复调用
         *  800401	仅自建账号企业类型可调用
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("删除成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }

        if (!empty($retApi['code'])) {
            // 3001 不算错误 
            if (intval($retApi['code']) == 3001) {
            } else {
                throw new \Exception("删除成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }
    }

    // 更新乐享成员
    protected function doUpdateUser(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $qyweixinUserInfo,
        $lexiangUserInfo,
        $main_depart,
        $department
    ) {
        $staff_id = $lexiangUserInfo['staff_id'];
        $qyweixin_name = $qyweixinUserInfo['name'];
        $qyweixin_english_name = $qyweixinUserInfo['english_name'];
        $qyweixin_mobile = $qyweixinUserInfo['mobile'];
        $qyweixin_gender = intval($qyweixinUserInfo['gender']);
        // 1表示男性，2表示女性。默认0表示未定义
        if ($qyweixin_gender != 1 && $qyweixin_gender != 2) {
            $qyweixin_gender = 0;
        }
        $qyweixin_position = $qyweixinUserInfo['position'];
        $qyweixin_telephone = $qyweixinUserInfo['telephone'];
        $qyweixin_external_position = $qyweixinUserInfo['external_position'];

        $country_code = "86";
        $birthday_calendar = "solar";
        // $avatar = "";

        // 更新乐享成员
        $params = array(
            "staff_id" => $staff_id,
            "name" => $qyweixin_name,
            // "english_name" => $qyweixin_english_name,
            "country_code" => $country_code,
            "phone" => $qyweixin_mobile,
            "department" => $department,
            "main_depart" => $main_depart,
            // "position" => $qyweixin_position,
            // "work_position" => $qyweixin_external_position,
            "gender" => $qyweixin_gender,
            // "avatar" => $avatar,
            "birthday_calendar" => $birthday_calendar,
            // "solar_birthday_date" => "2000-01-01",
            // "solar_entryday_date" => "2000-01-01",
            // "tel_phone" => $qyweixin_telephone,
            // "id_card" => ""
        );
        if (!empty($qyweixin_english_name)) {
            $params['english_name'] = $qyweixin_english_name;
        }
        // if (!empty($avatar)) {
        //     $params['avatar'] = $avatar;
        // }
        if (!empty($qyweixin_position)) {
            $params['position'] = $qyweixin_position;
        }
        if (!empty($qyweixin_external_position)) {
            $params['work_position'] = $qyweixin_external_position;
        }
        if (!empty($qyweixin_telephone)) {
            $params['tel_phone'] = $qyweixin_telephone;
        }

        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/user/update", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok"
         *  }
         *  错误码说明：
    
         *  CODE ID	说明
         *  1001	staff_id对应用户不存在
         *  1002	用户编辑失败
         *  1102	birthday_calendar只能为lunar和solar
         *  1103	department必须为数组或部门列表不能超过20
         *  1104	entryday_date日期格式错误
         *  1105	成员名name不能包含非法字符或者长度不能超过64
         *  1106	性别gender只能为0,1,2
         *  1107	职务position信息不能为空或包含特殊字符或大于128个字符
         *  1207	职位work_position信息不能为空或包含特殊字符或大于128个字符
         *  1108	resign_at日期格式错误
         *  1109	该手机号已经被注册或邀请
         *  1111	solar_birthday_date日期格式错误
         *  1201	别名不能包含非法字符或者长度不能超过64
         *  1202	员工已经离职
         *  1211	该手机号码已激活，不可通过接口变更，需要成员自行更改
         *  1311	联系电话格式错误或者联系电话已经被他人占用
         *  1310	身份证号格式错误或者身份证号已经被他人占用
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("更新成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!empty($retApi['code'])) {
            // 1202 员工已经离职 需要调用
            if (intval($retApi['code']) == 1202) {
                // 离职成员重新入职
                $params2 = array(
                    "staff_id" => $staff_id,
                    "department" => $department
                );
                $retApi2 = $serviceLexiangla->getLxapiObject()->post("contact/user/entry", $params2);
                if (empty($retApi2)) {
                    throw new \Exception("离职成员重新入职失败:" . \App\Common\Utils\Helper::myJsonEncode($params2));
                }
                if (!empty($retApi2['code'])) {
                    throw new \Exception("离职成员重新入职失败:" . \App\Common\Utils\Helper::myJsonEncode($params2));
                }
            } else {
                throw new \Exception("更新成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }

        $updateData = $params;
        unset($updateData['staff_id']);
        unset($updateData['retApi']);
        $updateData['memo'] = \App\Common\Utils\Helper::myJsonEncode($retApi);
        $updateData['department'] = \App\Common\Utils\Helper::myJsonEncode($department);
        $this->modelLexianglaUser->update(array('_id' => $lexiangUserInfo['_id']), array('$set' => $updateData));
    }

    // 同步所有的标签成员
    protected function syncTagUserList(
        \App\Qyweixin\Services\QyService $weixinopenService,
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla
    ) {
        $error_msg = "";
        $is_success = true;

        if (!empty($this->processFlags['sync_taguser_inner']['qyweixin_taguser_sync'])) {
            // 记录一下
            $e = new \Exception("同步企业微信的所有的标签成员数据开始", 107001);
            $this->recordResult($e, time());

            // 同步企业微信的所有的标签成员数据
            $total_user_num = 0;
            $total_party_num = 0;
            try {
                $modelQyweixinTag = new \App\Qyweixin\Models\Contact\Tag();
                $query = array();
                $query['authorizer_appid'] = $weixinopenService->getAuthorizerAppid();
                $query['provider_appid'] = $weixinopenService->getProviderAppid();
                $query['tagid'] = array('$ne' => '');
                $sort = array('_id' => 1);
                $tagList = $modelQyweixinTag->findAll($query, $sort);

                if (!empty($tagList)) {
                    foreach ($tagList as $info) {
                        try {
                            $this->modelLexianglaDepartmentSync->begin();
                            $res1 = $weixinopenService->getTag($info['tagid']);
                            if (!empty($res1['userlist'])) {
                                $total_user_num += count($res1['userlist']);
                            }
                            if (!empty($res1['partylist'])) {
                                $total_party_num += count($res1['partylist']);
                            }
                            unset($res1);
                            $this->modelLexianglaDepartmentSync->commit();
                        } catch (\Exception $e) {
                            $this->modelLexianglaDepartmentSync->rollback();
                            $param = array();
                            $param['error_msg']  = $e->getMessage();
                            $param['info'] = $info;
                            $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107002);
                            $this->recordResult($e1, time());
                        }
                    }
                }
                unset($tagList);
            } catch (\Exception $e) {
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步企业微信的所有的标签成员数据异常结束，错误信息：{$error_msg}", 107003);
            } else {
                $e = new \Exception("同步企业微信的所有的标签成员数据正常结束，获取企业微信的标签成员个数：{$total_user_num}，标签部门个数：{$total_party_num}", 107003);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_taguser_inner']['lexiangla_taguser_sync'])) {
            // 记录一下
            $e = new \Exception("同步乐享的所有的标签成员数据开始", 107004);
            $this->recordResult($e, time());
            // 同步乐享的所有的标签成员数据
            $total_user_num = 0;
            $total_party_num = 0;
            try {
                $modelLexianglaTag = new \App\Lexiangla\Models\Contact\Tag();
                $query = array();
                $sort = array('_id' => 1);
                $lexianglaTagList = $modelLexianglaTag->findAll($query, $sort);
                if (!empty($lexianglaTagList)) {
                    foreach ($lexianglaTagList as $info) {
                        try {
                            $this->modelLexianglaDepartmentSync->begin();
                            $res2 = $serviceLexiangla->getTag($info['tagid']);
                            if (!empty($res2['data']['user_list'])) {
                                $total_user_num += count($res2['data']['user_list']);
                            }
                            if (!empty($res2['data']['department_list'])) {
                                $total_party_num += count($res2['data']['department_list']);
                            }
                            unset($res2);
                            $this->modelLexianglaDepartmentSync->commit();
                        } catch (\Exception $e) {
                            $this->modelLexianglaDepartmentSync->rollback();
                            $param = array();
                            $param['error_msg']  = $e->getMessage();
                            $param['info'] = $info;
                            $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107005);
                            $this->recordResult($e1, time());
                        }
                    }
                }
                unset($lexianglaTagList);
            } catch (\Exception $e) {
                // throw $e;
                $error_msg = $e->getMessage();
                $is_success = false;
            }

            // 记录一下
            if (!$is_success) {
                $e = new \Exception("同步乐享的所有的标签成员数据异常结束，错误信息：{$error_msg}", 107006);
            } else {
                $e = new \Exception("同步乐享的所有的标签成员数据正常结束，获取乐享的标签成员个数：{$total_user_num}，标签部门个数：{$total_party_num}", 107006);
            }
            $this->recordResult($e, time());

            // 如果失败就返回了
            if (!$is_success) {
                return false;
            }
        }

        if (!empty($this->processFlags['sync_taguser_inner']['qyweixin_lexiangla_taguser_sync'])) {

            // 记录一下
            $e = new \Exception("企业微信和乐享平台之间的标签成员数据同步处理开始", 107007);
            $this->recordResult($e, time());

            // 从企业微信标签成员表中获取所有的数据
            $modelQyweixinTagUser = new \App\Qyweixin\Models\Contact\TagUser();
            $query = array();
            $query['authorizer_appid'] = $weixinopenService->getAuthorizerAppid();
            $query['provider_appid'] = $weixinopenService->getProviderAppid();
            $query['tagid'] = array('$ne' => '');
            $query['userid'] = array('$ne' => '');
            $sort = array('_id' => 1);
            $qyweixinTagUserList = $modelQyweixinTagUser->findAll($query, $sort);

            // 循环处理
            $total_num = 0;
            $success_num = 0;
            $error_num = 0;
            $errorList = array();
            foreach ($qyweixinTagUserList as $qyweixinTagUserInfo) {
                $total_num++;
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个标签成员
                    $this->syncSingleTagUser($serviceLexiangla, $qyweixinTagUserInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num++;
                    // $param = array();
                    // $param['qyweixinTagUserInfo'] = $qyweixinTagUserInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107008);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinTagUserInfo' => $qyweixinTagUserInfo, 'error_msg' => $e->getMessage());
                    $errorList[] = $param;
                    $error_num++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107008);
                    $this->recordResult($e1, time());
                }
            }
            unset($qyweixinTagUserList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num'] = $total_num;
            $ret['success_num'] = $success_num;
            $ret['error_num'] = $error_num;
            if ($error_num < 10) {
                $ret['errorList'] = $errorList;
            }
            $e = new \Exception("企业微信和乐享平台之间的标签成员数据同步处理结束1，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 107009);
            $this->recordResult($e, time());

            // 如果有错误的话 那么再执行一次
            $total_num2 = 0;
            $success_num2 = 0;
            $error_num2 = 0;
            $errorList2 = array();
            foreach ($errorList as $errorInfo) {
                $total_num2++;
                $qyweixinTagUserInfo = $errorInfo['qyweixinTagUserInfo'];
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个标签成员
                    $this->syncSingleTagUser($serviceLexiangla, $qyweixinTagUserInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num2++;
                    // $param = array();
                    // $param['qyweixinTagUserInfo'] = $qyweixinTagUserInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107010);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinTagUserInfo' => $qyweixinTagUserInfo, 'error_msg' => $e->getMessage());
                    $errorList2[] = $param;
                    $error_num2++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107010);
                    $this->recordResult($e1, time());
                }
            }
            unset($errorList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num2'] = $total_num2;
            $ret['success_num2'] = $success_num2;
            $ret['error_num2'] = $error_num2;
            if ($error_num2 < 10) {
                $ret['errorList2'] = $errorList2;
            }
            $e = new \Exception("企业微信和乐享平台之间的标签成员数据同步处理结束2，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 107011);
            $this->recordResult($e, time());
        }

        if (!empty($this->processFlags['sync_taguser_inner']['qyweixin_lexiangla_tagparty_sync'])) {
            // 从企业微信标签部门表中获取所有的数据
            $modelQyweixinTagParty = new \App\Qyweixin\Models\Contact\TagParty();
            $query = array();
            $query['authorizer_appid'] = $weixinopenService->getAuthorizerAppid();
            $query['provider_appid'] = $weixinopenService->getProviderAppid();
            $query['tagid'] = array('$ne' => '');
            $query['deptid'] = array('$ne' => '');
            $sort = array('_id' => 1);
            $qyweixinTagPartyList = $modelQyweixinTagParty->findAll($query, $sort);

            // 循环处理
            $total_num = 0;
            $success_num = 0;
            $error_num = 0;
            $errorList = array();
            foreach ($qyweixinTagPartyList as $qyweixinTagPartyInfo) {
                $total_num++;
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个标签成员
                    $this->syncSingleTagParty($serviceLexiangla, $qyweixinTagPartyInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num++;
                    // $param = array();
                    // $param['qyweixinTagPartyInfo'] = $qyweixinTagPartyInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107012);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinTagPartyInfo' => $qyweixinTagPartyInfo, 'error_msg' => $e->getMessage());
                    $errorList[] = $param;
                    $error_num++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107012);
                    $this->recordResult($e1, time());
                }
            }
            unset($qyweixinTagPartyList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num'] = $total_num;
            $ret['success_num'] = $success_num;
            $ret['error_num'] = $error_num;
            if ($error_num < 10) {
                $ret['errorList'] = $errorList;
            }
            $e = new \Exception("企业微信和乐享平台之间的标签成员数据同步处理结束3，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 107013);
            $this->recordResult($e, time());

            // 如果有错误的话 那么再执行一次
            $total_num2 = 0;
            $success_num2 = 0;
            $error_num2 = 0;
            $errorList2 = array();
            foreach ($errorList as $errorInfo) {
                $total_num2++;
                $qyweixinTagPartyInfo = $errorInfo['qyweixinTagPartyInfo'];
                try {
                    $this->modelLexianglaDepartmentSync->begin();
                    // 处理同步单个标签成员
                    $this->syncSingleTagParty($serviceLexiangla, $qyweixinTagPartyInfo);
                    $this->modelLexianglaDepartmentSync->commit();
                    $success_num2++;
                    // $param = array();
                    // $param['qyweixinTagPartyInfo'] = $qyweixinTagPartyInfo;
                    // $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107014);
                    // $this->recordResult($e1, time());
                } catch (\Exception $e) {
                    $this->modelLexianglaDepartmentSync->rollback();
                    $param = array('qyweixinTagPartyInfo' => $qyweixinTagPartyInfo, 'error_msg' => $e->getMessage());
                    $errorList2[] = $param;
                    $error_num2++;
                    $e1 = new \Exception(\App\Common\Utils\Helper::myJsonEncode($param), 107014);
                    $this->recordResult($e1, time());
                }
            }
            unset($errorList);

            // 记录一下同步的结果的
            $ret = array();
            $ret['total_num2'] = $total_num2;
            $ret['success_num2'] = $success_num2;
            $ret['error_num2'] = $error_num2;
            if ($error_num2 < 10) {
                $ret['errorList2'] = $errorList2;
            }
            $e = new \Exception("企业微信和乐享平台之间的标签数据同步处理结束4，同步结果：" . \App\Common\Utils\Helper::myJsonEncode($ret), 107015);
            $this->recordResult($e, time());
        }

        return true;
    }

    // 同步单个标签成员
    protected function syncSingleTagUser($serviceLexiangla, $qyweixinTagUserInfo)
    {
        // 查找乐享标签的同步表记录
        $tagSyncInfo = $this->modelLexianglaTagSync->getInfoByQyTagId($qyweixinTagUserInfo['tagid']);
        // 如果没有找到的话
        if (empty($tagSyncInfo)) {
            // 该记录不做处理
            return;
        }
        // 查找乐享成员的同步表记录
        // $userSyncInfo = $this->modelLexianglaUserSync->getInfoByQyUserId($qyweixinTagUserInfo['userid']);
        $userSyncInfo = array();
        $userSyncInfo['staffid'] = $qyweixinTagUserInfo['userid'];
        if (empty($userSyncInfo)) {
            // 该记录不做处理
            return;
        }
        // 获取乐享标签成员信息
        $lexiangTagUserInfo = $this->modelLexianglaTagUser->getInfoByUseridAndTagid($userSyncInfo['staffid'], $tagSyncInfo['tagid']);

        // 如果记录是存在的 也就是企业微信上有这个记录所对应的标签成员
        if (!empty($qyweixinTagUserInfo['is_exist'])) {
            // 如果未找到
            if (empty($lexiangTagUserInfo)) {
                // 创建乐享标签成员同步信息
                $this->doCreateTagUser($serviceLexiangla, $tagSyncInfo['tagid'], $userSyncInfo['staffid']);
            } else { // 如果找到的话
                return;
            }
        } else { // 如果记录不存在
            // 如果未找到
            if (empty($lexiangTagUserInfo)) {
                //
            } else {
                // 该乐享标签成员不存在的话 就是在乐享平台上该标签成员的记录不存在的意思
                if (empty($lexiangTagUserInfo['is_exist'])) {
                    //
                } else { // 存在
                    // 调用乐享删除标签成员接口删除标签成员处理
                    $this->doDeleteTagUser($serviceLexiangla, $tagSyncInfo['tagid'], $userSyncInfo['staffid']);
                }
                // 删除乐享标签成员信息
                $this->modelLexianglaTagUser->physicalRemove(array('_id' => $lexiangTagUserInfo['_id']));
            }
            // 企业微信标签成员表的记录删除
            $modelQyweixinTagUser = new \App\Qyweixin\Models\Contact\TagUser();
            $modelQyweixinTagUser->physicalRemove(array('_id' => $qyweixinTagUserInfo['_id'], 'is_exist' => 0));
        }
    }

    // 创建乐享标签成员
    protected function doCreateTagUser(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $tagid,
        $staffid
    ) {
        // 创建乐享标签成员
        $params = array(
            "id" => $tagid,
            "user_list" => [$staffid],
        );

        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/tag/add-users", $params);
        /**
         * {
         *           "msg": "success",
         *           "code": 0,
         *           "data": null
         *       }
         *  错误码说明：
    
         *  CODE	说明
         *  20011	参数错误（id为空）
         *  20010	标签不存在
         *  20014	非乐享标签不支持增加标签成员
         *  30012	标签名称只能是中文和英文字符，且长度不能超过32
         *  30013	用户id列表只能为数组且长度不能超过1000
         *  30014	部门id列表只能为数组且长度不能超过1000
         */
        $params['retApi'] = $retApi;
        // print_r($params);
        // die('xxxxxxxx');

        if (empty($retApi)) {
            throw new \Exception("创建标签成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!empty($retApi['code'])) {
            throw new \Exception("创建标签成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }

        // 记录乐享标签成员信息
        $lexianglaTagUserInfo = $this->modelLexianglaTagUser->getInfoByUseridAndTagid($staffid, $tagid);
        // 如果没有乐享标签成员信息 就创建一条
        if (empty($lexianglaTagUserInfo)) {
            $lexianglaTagUserInfo = array();
            $lexianglaTagUserInfo['tagid'] = $tagid;
            $lexianglaTagUserInfo['tagname'] = "";
            $lexianglaTagUserInfo['userid'] = $staffid;
            $lexianglaTagUserInfo['username'] = "";
            $lexianglaTagUserInfo['is_exist'] = 1;
            $lexianglaTagUserInfo['get_time'] = \App\Common\Utils\Helper::getCurrentTime();
            $lexianglaTagUserInfo['memo'] = \App\Common\Utils\Helper::myJsonEncode($retApi);
            $lexianglaTagUserInfo = $this->modelLexianglaTagUser->insert($lexianglaTagUserInfo);
        }
        return $lexianglaTagUserInfo;
    }

    // 删除乐享标签成员
    protected function doDeleteTagUser(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $tagid,
        $staffid
    ) {
        // 删除乐享标签成员
        $params = array(
            "id" => $tagid,
            "user_list" => [$staffid],
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/tag/del-users", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok"
         *  }
         *  错误码说明：
    
         *  CODE	说明
         *  20010	标签不存在
         *  20015	非乐享标签不支持删除标签成员
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("删除标签成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }

        if (!empty($retApi['code'])) {
            // 20010 不算错误 
            if (intval($retApi['code']) == 20010) {
            } else {
                throw new \Exception("删除标签成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }
    }

    // 同步单个标签成员
    protected function syncSingleTagParty($serviceLexiangla, $qyweixinTagPartyInfo)
    {
        // 查找乐享标签的同步表记录
        $tagSyncInfo = $this->modelLexianglaTagSync->getInfoByQyTagId($qyweixinTagPartyInfo['tagid']);
        // 如果没有找到的话
        if (empty($tagSyncInfo)) {
            // 该记录不做处理
            return;
        }
        // 查找乐享部门的同步表记录
        $departmentSyncInfo = $this->modelLexianglaDepartmentSync->getInfoByQyDeptId($qyweixinTagPartyInfo['deptid']);
        if (empty($departmentSyncInfo)) {
            // 该记录不做处理
            return;
        }
        // 获取乐享标签部门信息
        $lexiangTagPartyInfo = $this->modelLexianglaTagParty->getInfoByDeptidAndTagid($departmentSyncInfo['deptid'], $tagSyncInfo['tagid']);

        // 如果记录是存在的 也就是企业微信上有这个记录所对应的标签部门
        if (!empty($qyweixinTagPartyInfo['is_exist'])) {
            // 如果未找到
            if (empty($lexiangTagPartyInfo)) {
                // 创建乐享标签部门同步信息
                $this->doCreateTagParty($serviceLexiangla, $tagSyncInfo['tagid'], $departmentSyncInfo['deptid']);
            } else { // 如果找到的话
                return;
            }
        } else { // 如果记录不存在
            // 如果未找到
            if (empty($lexiangTagPartyInfo)) {
                //
            } else {
                // 该乐享标签部门不存在的话 就是在乐享平台上该标签部门的记录不存在的意思
                if (empty($lexiangTagPartyInfo['is_exist'])) {
                    //
                } else { // 存在
                    // 调用乐享删除标签成员接口删除标签部门处理
                    $this->doDeleteTagParty($serviceLexiangla, $tagSyncInfo['tagid'], $departmentSyncInfo['deptid']);
                }
                // 删除乐享标签部门信息
                $this->modelLexianglaTagParty->physicalRemove(array('_id' => $lexiangTagPartyInfo['_id']));
            }
            // 企业微信标签部门表的记录删除
            $modelQyweixinTagParty = new \App\Qyweixin\Models\Contact\TagParty();
            $modelQyweixinTagParty->physicalRemove(array('_id' => $qyweixinTagPartyInfo['_id']));
        }
    }

    // 创建乐享标签部门
    protected function doCreateTagParty(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $tagid,
        $deptid
    ) {
        // 创建乐享标签成员
        $params = array(
            "id" => $tagid,
            "department_list" => [$deptid],
        );

        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/tag/add-users", $params);
        /**
         * {
         *          "msg": "success",
         *          "code": 0,
         *          "data": null
         *      }
         *  错误码说明：
    
         *  CODE	说明
         *  20011	参数错误（id为空）
         *  20010	标签不存在
         *  20014	非乐享标签不支持增加标签成员
         *  30012	标签名称只能是中文和英文字符，且长度不能超过32
         *  30013	用户id列表只能为数组且长度不能超过1000
         *  30014	部门id列表只能为数组且长度不能超过1000
         */
        $params['retApi'] = $retApi;
        // print_r($params);
        // die('xxxxxxxx');

        if (empty($retApi)) {
            throw new \Exception("创建标签成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }
        if (!empty($retApi['code'])) {
            throw new \Exception("创建标签成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }

        // 记录乐享标签成员信息
        $lexianglaTagPartyInfo = $this->modelLexianglaTagParty->getInfoByDeptidAndTagid($deptid, $tagid);
        // 如果没有乐享标签成员信息 就创建一条
        if (empty($lexianglaTagPartyInfo)) {
            $lexianglaTagPartyInfo = array();
            $lexianglaTagPartyInfo['tagid'] = $tagid;
            $lexianglaTagPartyInfo['tagname'] = "";
            $lexianglaTagPartyInfo['deptid'] = $deptid;
            $lexianglaTagPartyInfo['is_exist'] = 1;
            $lexianglaTagPartyInfo['get_time'] = \App\Common\Utils\Helper::getCurrentTime();
            $lexianglaTagPartyInfo['memo'] = \App\Common\Utils\Helper::myJsonEncode($retApi);
            $lexianglaTagPartyInfo = $this->modelLexianglaTagParty->insert($lexianglaTagPartyInfo);
        }
        return $lexianglaTagPartyInfo;
    }

    // 删除乐享标签部门
    protected function doDeleteTagParty(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $tagid,
        $deptid
    ) {
        // 删除乐享标签部门
        $params = array(
            "id" => $tagid,
            "department_list" => [$deptid],
        );
        $retApi = $serviceLexiangla->getLxapiObject()->post("contact/tag/del-users", $params);
        /**
         * {
         *      "code": 0,
         *      "msg": "ok"
         *  }
         *  错误码说明：
    
         *  CODE	说明
         *  20010	标签不存在
         *  20015	非乐享标签不支持删除标签成员
         */
        $params['retApi'] = $retApi;
        if (empty($retApi)) {
            throw new \Exception("删除标签成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
        }

        if (!empty($retApi['code'])) {
            // 20010 不算错误 
            if (intval($retApi['code']) == 20010) {
            } else {
                throw new \Exception("删除标签成员失败:" . \App\Common\Utils\Helper::myJsonEncode($params));
            }
        }
    }

    protected function getLexianglaDepartmentId($qyweixin_main_depart)
    {
        if (empty($qyweixin_main_depart)) {
            return "";
        }
        // 查找乐享部门的同步表记录
        $departmentSyncInfo = $this->modelLexianglaDepartmentSync->getInfoByQyDeptId($qyweixin_main_depart);
        if (empty($departmentSyncInfo)) {
            return "";
        } else {
            return $departmentSyncInfo['deptid'];
        }
    }

    protected function getLexianglaDepartment($qyweixin_department)
    {
        if (!is_array($qyweixin_department)) {
            $qyweixin_department = \json_decode($qyweixin_department, true);
        }
        if (empty($qyweixin_department)) {
            $qyweixin_department = array();
        }
        $department = array();
        foreach ($qyweixin_department as $qydeptid) {
            $lexianglaDeptid = $this->getLexianglaDepartmentId($qydeptid);
            if (!empty($lexianglaDeptid)) {
                $department[] = $lexianglaDeptid;
            }
        }
        return $department;
    }

    protected function getLexianglaAvatar(
        \App\Lexiangla\Services\LexianglaService $serviceLexiangla,
        $staff_id,
        $avatar
    ) {
        if (empty($avatar)) {
            return "";
        }
        try {
            $filename = tempnam(sys_get_temp_dir(), 'avatar_' . uniqid() . "_{$staff_id}") . ".jpg";
            file_put_contents($filename, file_get_contents($avatar));
            $applicationInfo = $serviceLexiangla->getApplicationInfo();
            $response = $serviceLexiangla->getLxapiObject()->postAsset($applicationInfo['staffID'], 'image', fopen($filename, 'r'));
            // {
            //     "asset_id": "68975160a41211ebbcc38ead0db1c463",
            //     "url": "https://lexiangla.com/assets/68975160a41211ebbcc38ead0db1c463",
            //     "public_url": "https://image-pub.lexiang-asset.com/company_8d67cf3afbd711ea875622f21195fed5/assets/2021/04/6831c0d4-a412-11eb-8226-8ead0db1c463.jpg"
            // }
            // print_r($response);
            // die('xxxxxxxxxxx');
            if (empty($response)) {
                return "";
            }
            if (empty($response['url'])) {
                return "";
            }
            return $response['url'];
        } catch (\Exception $th) {
            //throw $th;
            // die('error:' . $th->getMessage());
            return "";
        }
    }

    protected function checkUserIsExist($info)
    {
        $modelQyweixinDepartmentUser = new \App\Qyweixin\Models\Contact\DepartmentUser();
        $query = array();
        $query['userid'] = $info['userid'];
        $query['authorizer_appid'] = $info['authorizer_appid'];
        $query['is_exist'] = 1;
        $departmentUserInfo = $modelQyweixinDepartmentUser->findOne($query);
        if (empty($departmentUserInfo)) {
            return false;
        } else {
            return true;
        }
    }

    protected function getLexianglaDepartmentCount($departments)
    {
        /**
         * {
         *  "code": 0,
         *  "msg": "ok",
         *  "data": {
         *      "id": 1,
         *      "name": "根部门",
         *      "parent_id": 0,
         *      "path": "/1",
         *      "order": 12354,
         *      "children": [
         *          {
         *              "id": 2,
         *              "name": "根部门",
         *              "parent_id": 1,
         *              "path": "/1/2",
         *              "order": 12356,
         *              "children": []
         *          }
         *      ]
         *  }
         * }
         */
        $total_num = 0;
        if (!empty($departments)) {
            foreach ($departments as $departmentInfo) {
                $total_num++;
                // 如果有子部门的话那么就处理一下
                if (!empty($departmentInfo['children'])) {
                    $total_num += $this->getLexianglaDepartmentCount($departmentInfo['children']);
                }
            }
        }
        return $total_num;
    }

    // 记录结果
    protected function recordResult($e, $now)
    {
        $modelErrorLog = new \App\Activity\Models\ErrorLog();
        $modelErrorLog->log($this->activity_id, $e, $now);
    }
}
