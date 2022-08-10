<?php
class QyweixinTask extends \Phalcon\CLI\Task
{
    // 监控任务
    private $activity_id = 6;

    /**
     * 获取企业微信的accesstoken
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getaccesstoken
     * @param array $params
     */
    public function getaccesstokenAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $now = \App\Common\Utils\Helper::getCurrentTime($now);
            $query = array(
                'access_token_expire' => array(
                    '$lte' => $now
                )
            );
            $sort = array('access_token_expire' => 1, '_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {
                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];
                    try {
                        // 更新
                        $modelAgent->getTokenByAppid($provider_appid, $authorizer_appid, $agentid);
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
     * 获取获取会话内容
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getchatdata
     * @param array $params
     */
    public function getchatdataAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();

        $now = time();
        $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
        $modelChatdata = new \App\Qyweixin\Models\MsgAudit\Chatdata();
        $modelSn = new \App\Qyweixin\Models\MsgAudit\Sn();
        $modelMaxseq = new \App\Qyweixin\Models\MsgAudit\Maxseq();

        // \DB::enableQueryLog();
        // 每次获取一百条记录
        $query = array();
        $sort = array('authorizer_appid' => 1);
        $maxseqList = $modelMaxseq->findAll($query, $sort);

        // print_r(\DB::getQueryLog());
        $params  = array(
            'name' => 'qyweixin:get_chatdata',
            'maxseqList' => $maxseqList,
            'now' => date('Y-m-d H:i:s', $now)
        );
        print_r($params);
        if (empty($maxseqList)) {
            return;
        }

        date_default_timezone_set('Asia/Chongqing');
        foreach ($maxseqList as $maxseqItem) {
            $corpid = $maxseqItem['authorizer_appid'];
            try {
                // 获取agentInfo信息
                $agentInfo = $modelAgent->getInfoByAppid($maxseqItem['provider_appid'], $maxseqItem['authorizer_appid'], 9999997, false);
                if (empty($agentInfo)) {
                    continue;
                }
                $snList = $modelSn->getListByCorpid($corpid);
                if (empty($snList)) {
                    continue;
                }
                $old_max_seq = intval($maxseqItem['max_seq']);

                $chatdataListRet = $this->getChatdata($maxseqItem, $agentInfo, $snList);
                $chatdataList = $chatdataListRet['chatdataList'];
                $max_seq = intval($chatdataListRet['max_seq']);

                // print_r($chatdataListRet);
                // die('max_seq:' . $max_seq);
                try {
                    $modelMaxseq->begin();
                    if (!empty($chatdataList)) {
                        foreach ($chatdataList as $chatdataInfo) {
                            // 如果是混合消息的话
                            if (!empty($chatdataInfo['msgtype']) && $chatdataInfo['msgtype'] == 'mixed') {
                                foreach ($chatdataInfo[$chatdataInfo['msgtype']]['item'] as $idx => $itemInfo) {
                                    $chatdataItem = $chatdataInfo;
                                    $chatdataItem['decrypt_chat_msg'] = $chatdataItem['decrypt_msg'];
                                    $chatdataItem = array_merge($chatdataItem, $chatdataItem['decrypt_chat_msg']);
                                    $chatdataItem['mixed_item_idx'] = ($idx + 1);
                                    //"type": "text", 
                                    // "content": "{\"content\":\"你好[微笑]\\n\"}"
                                    $chatdataItem['mixed_item_msgtype'] = $itemInfo['type'];
                                    $chatdataItem = array_merge($chatdataItem, $itemInfo['content']);
                                    $modelChatdata->saveChatdataInfo($chatdataItem, $maxseqItem['provider_appid'], $maxseqItem['authorizer_appid']);
                                }
                            } else {
                                $chatdataItem = $chatdataInfo;
                                $chatdataItem['decrypt_chat_msg'] = $chatdataItem['decrypt_msg'];
                                $chatdataItem = array_merge($chatdataItem, $chatdataItem['decrypt_chat_msg']);
                                $chatdataItem['mixed_item_idx'] = 0;
                                if (!empty($chatdataItem['msgtype'])) {
                                    $msgtype = $chatdataItem['msgtype'];
                                    /**
                                     * [msgtype] => docmsg
                                     * [doc] => Array
                                     * (
                                     *       [title] => 欢迎使用微文档
                                     *       [doc_creator] => GuoYongRong
                                     *       [link_url] => https://doc.weixin.qq.com/txdoc/word?docid=w2_AKYAtQatAEkQraLOh0yQNuJc7NQi0&scode=AK8AuAfrAAY5O6mwTuAKYAtQatAEk&type=0
                                     *  )
                                     */
                                    if ($msgtype == 'docmsg') {
                                        $msgtype = 'doc';
                                    }
                                    $chatdataItem = array_merge($chatdataItem, $chatdataItem[$msgtype]);
                                }
                                // print_r($chatdataItem);
                                // die('xxxxx');
                                $modelChatdata->saveChatdataInfo($chatdataItem, $maxseqItem['provider_appid'], $maxseqItem['authorizer_appid']);
                            }
                        }
                    }
                    if ($max_seq > $old_max_seq) {
                        $modelMaxseq->update(array('_id', $maxseqItem['_id']), array('$set' => array('max_seq' => $max_seq)));
                    }
                    $modelMaxseq->commit();
                } catch (\Exception $e) {
                    $modelMaxseq->rollback();
                    $modelActivityErrorLog->log($this->activity_id, $e, $now);
                }
            } catch (\Exception $e2) {
                $modelActivityErrorLog->log($this->activity_id, $e, $now);
            }
        }
    }

    /**
     * 获取部门列表
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getdepartmentlist
     * @param array $params
     */
    public function getdepartmentlistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array(
                'agentid' => '9999998'
            );
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {
                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];

                    $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'provider_appid:' . $provider_appid . ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid));
                    $lock->setExpire(3600 * 8);
                    if ($lock->lock()) {
                        continue;
                    }

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_department_list:' . $authorizer_appid . ':' . $agentid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $agentid, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
                        $weixinopenService->getDepartmentList(0);
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
     * 获取部门成员
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getdepartmentusersimplelist
     * @param array $params
     */
    public function getdepartmentusersimplelistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_department_user_simple_list_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        try {
            $modelDepartment = new \App\Qyweixin\Models\Contact\Department();
            $query = array();
            $sort = array('_id' => 1);
            $departmentList = $modelDepartment->findAll($query, $sort);
            if (!empty($departmentList)) {
                foreach ($departmentList as $info) {

                    $deptid = $info['deptid'];
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = '9999998';

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_department_user_simple_list:' . $authorizer_appid . ":" . $deptid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }
                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $deptid, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                        $weixinopenService->getDepartmentUserSimpleList($deptid);
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
     * 从部门人员获取用户列表
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getuserlistfromdepartmentuser
     * @param array $params
     */
    public function getuserlistfromdepartmentuserAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_user_list_from_department_user_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        try {
            $modelDepartmentUser = new \App\Qyweixin\Models\Contact\DepartmentUser();
            $query = array();
            $sort = array('_id' => 1);
            $departmentUserList = $modelDepartmentUser->findAll($query, $sort);
            if (!empty($departmentUserList)) {
                $modelUser = new \App\Qyweixin\Models\Contact\User();
                foreach ($departmentUserList as $info) {

                    $userid = $info['userid'];
                    $name = $info['name'];
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = '9999998';

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_user_list_from_department_user:' . $authorizer_appid . ":" . $userid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }
                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $userid, $expire_time);

                    try {
                        $userInfo = $modelUser->getInfoByUserId($userid, $authorizer_appid, $provider_appid);
                        if (empty($userInfo)) {
                            $data = array();
                            $data['userid'] = $userid;
                            $data['provider_appid'] = $provider_appid;
                            $data['authorizer_appid'] = $authorizer_appid;
                            $data['name'] = $name;
                            // 增加一个user
                            $userInfo = $modelUser->insert($data);
                        }
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
     * 获取用户详情
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getuserinfo
     * @param array $params
     */
    public function getuserinfoAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_user_info_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        try {
            $modelUser = new \App\Qyweixin\Models\Contact\User();
            $query = array();
            $sort = array('_id' => 1);
            $userList = $modelUser->findAll($query, $sort);
            if (!empty($userList)) {
                foreach ($userList as $info) {

                    $userid = $info['userid'];
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = '9999998';

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_user_info:' . $authorizer_appid . ":" . $userid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $userid, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                        $weixinopenService->getUserInfo($info);
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
     * 获取标签库
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin gettaglist
     * @param array $params
     */
    public function gettaglistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array(
                'agentid' => '9999998'
            );
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {
                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];

                    $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'provider_appid:' . $provider_appid . ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid));
                    $lock->setExpire(3600 * 8);
                    if ($lock->lock()) {
                        continue;
                    }

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_tag_list:' . $authorizer_appid . ':' . $agentid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $agentid, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
                        $weixinopenService->getTagList();
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
     * 获取标签成员
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin gettagpartyuser
     * @param array $params
     */
    public function gettagpartyuserAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_tag_party_user_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        try {
            $modelTag = new \App\Qyweixin\Models\Contact\Tag();
            $query = array();
            $sort = array('_id' => 1);
            $tagList = $modelTag->findAll($query, $sort);
            if (!empty($tagList)) {
                foreach ($tagList as $info) {

                    $tagid = $info['tagid'];
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = '9999998';

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_tag_party_user:' . $authorizer_appid . ":" . $tagid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $tagid, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                        $weixinopenService->getTag($tagid);
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
     * 获取配置了客户联系功能的成员列表
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getfollowuserlist
     * @param array $params
     */
    public function getfollowuserlistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array(
                'agentid' => '9999999'
            );
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {
                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];

                    $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'provider_appid:' . $provider_appid . ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid));
                    $lock->setExpire(3600 * 8);
                    if ($lock->lock()) {
                        continue;
                    }

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_follow_user_list:' . $authorizer_appid . ':' . $agentid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $agentid, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
                        $weixinopenService->getFollowUserList();
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
     * 获取客户列表
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getexternaluserlist
     * @param array $params
     */
    public function getexternaluserlistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_external_user_list_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        try {
            $modelFollowUser = new \App\Qyweixin\Models\ExternalContact\FollowUser();
            $query = array();
            $sort = array('_id' => 1);
            $ecFolowUserList = $modelFollowUser->findAll($query, $sort);
            if (!empty($ecFolowUserList)) {
                foreach ($ecFolowUserList as $info) {

                    $externaluser_follow_user = $info['follow_user'];
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = '9999999';

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_external_user_list:' . $authorizer_appid . ":" . $externaluser_follow_user;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }
                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $externaluser_follow_user, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                        $weixinopenService->getExternalUserList($externaluser_follow_user);
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
     * 获取客户详情
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getexternaluserinfo
     * @param array $params
     */
    public function getexternaluserinfoAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_external_user_info_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        try {
            $modelExternalUser = new \App\Qyweixin\Models\ExternalContact\ExternalUser();
            $query = array();
            $sort = array('_id' => 1);
            $ecUserList = $modelExternalUser->findAll($query, $sort);
            if (!empty($ecUserList)) {
                foreach ($ecUserList as $info) {

                    $external_userid = $info['external_userid'];
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = '9999999';

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_external_user_info:' . $authorizer_appid . ":" . $external_userid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $external_userid, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                        $weixinopenService->getExternalUserInfo($info);
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
     * 获取客户列表
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getgroupchatlist
     * @param array $params
     */
    public function getgroupchatlistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_group_chat_list_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        try {
            $modelFollowUser = new \App\Qyweixin\Models\ExternalContact\FollowUser();
            $query = array();
            $sort = array('_id' => 1);
            $ecFolowUserList = $modelFollowUser->findAll($query, $sort);
            if (!empty($ecFolowUserList)) {
                foreach ($ecFolowUserList as $info) {

                    $externaluser_follow_user = $info['follow_user'];
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = '9999999';

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_group_chat_list:' . $authorizer_appid . ":" . $externaluser_follow_user;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }
                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $externaluser_follow_user, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                        $status_filter = 0;
                        $cursor = "";
                        $limit = 1000;
                        $owner_filter = array();
                        $owner_filter['userid_list'] = array($externaluser_follow_user);
                        $weixinopenService->getGroupChatList($status_filter, $owner_filter, $cursor, $limit);
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
     * 获取客户详情
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getgroupchatinfo
     * @param array $params
     */
    public function getgroupchatinfoAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_group_chat_info_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        try {
            $modelGroupChat = new \App\Qyweixin\Models\ExternalContact\GroupChat();
            $query = array();
            $sort = array('_id' => 1);
            $groupChatList = $modelGroupChat->findAll($query, $sort);
            if (!empty($groupChatList)) {
                foreach ($groupChatList as $info) {

                    $chat_id = $info['chat_id'];
                    $provider_appid = $info['provider_appid'];
                    $authorizer_appid = $info['authorizer_appid'];
                    $externaluser_agent_agentid = '9999999';

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_group_chat_info:' . $authorizer_appid . ":" . $chat_id;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $chat_id, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                        $weixinopenService->getGroupChatInfo($info);
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
     * 获取企业客户朋友圈列表
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getmomentlist
     * @param array $params
     */
    public function getmomentlistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array(
                'agentid' => '9999999'
            );
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {

                $yesterday = time() - 24 * 3600;
                $end_time = mktime(0, 0, 0, date('m', $yesterday), date('d', $yesterday), date('Y', $yesterday));
                $start_time = strtotime("-1 month", time());
                $creator = "";
                $filter_type = 2;
                $limit = 100;

                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];

                    $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'provider_appid:' . $provider_appid . ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid));
                    $lock->setExpire(3600 * 8);
                    if ($lock->lock()) {
                        continue;
                    }

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_moment_list:' . $authorizer_appid . ':' . $agentid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $agentid, $expire_time);

                    try {
                        $cursor = "";
                        do {
                            $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
                            $res = $weixinopenService->getMomentList($start_time, $end_time, $creator, $filter_type, $limit, $cursor);

                            // 分页游标，下次请求时填写以获取之后分页的记录，如果已经没有更多的数据则返回空
                            if (empty($res['next_cursor'])) {
                                $cursor = "";
                            } else {
                                $cursor = $res['next_cursor'];
                            }
                        } while (!empty($cursor));
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
     * 获取企业的全部群发记录
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getgroupmsglist
     * @param array $params
     */
    public function getgroupmsglistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array(
                'agentid' => '9999999'
            );
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {

                $yesterday = time() - 24 * 3600;
                $end_time = mktime(0, 0, 0, date('m', $yesterday), date('d', $yesterday), date('Y', $yesterday));
                $start_time = strtotime("-1 month", time());
                $creator = "";
                $filter_type = 2;
                $limit = 100;

                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];

                    $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'provider_appid:' . $provider_appid . ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid));
                    $lock->setExpire(3600 * 8);
                    if ($lock->lock()) {
                        continue;
                    }

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_groupmsg_list:' . $authorizer_appid . ':' . $agentid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $agentid, $expire_time);

                    try {
                        $cursor = "";
                        // chat_type	是	群发任务的类型，默认为single，表示发送给客户，group表示发送给客户群
                        $chat_type = 'single';
                        do {
                            $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
                            $res = $weixinopenService->getGroupmsgList($chat_type, $start_time, $end_time, $creator, $filter_type, $limit, $cursor);

                            // 分页游标，下次请求时填写以获取之后分页的记录，如果已经没有更多的数据则返回空
                            if (empty($res['next_cursor'])) {
                                $cursor = "";
                            } else {
                                $cursor = $res['next_cursor'];
                            }
                        } while (!empty($cursor));

                        $cursor = "";
                        // chat_type	是	群发任务的类型，默认为single，表示发送给客户，group表示发送给客户群
                        $chat_type = 'group';
                        do {
                            $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
                            $res = $weixinopenService->getGroupmsgList($chat_type, $start_time, $end_time, $creator, $filter_type, $limit, $cursor);

                            // 分页游标，下次请求时填写以获取之后分页的记录，如果已经没有更多的数据则返回空
                            if (empty($res['next_cursor'])) {
                                $cursor = "";
                            } else {
                                $cursor = $res['next_cursor'];
                            }
                        } while (!empty($cursor));
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
     * 获取群发成员发送任务列表
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getgroupmsgtasklist
     * @param array $params
     */
    public function getgroupmsgtasklistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        $cacheKey1 = 'get_groupmsg_task_list_command';
        $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, $cacheKey1));
        $lock->setExpire(3600 * 8);
        if ($lock->lock()) {
            return;
        }

        $limit = 100;
        $yesterday = time() - 24 * 3600;
        $end_time = mktime(0, 0, 0, date('m', $yesterday), date('d', $yesterday), date('Y', $yesterday));
        $start_time = strtotime("-1 month", time());

        $query = array(
            'create_time' => array(
                '$lte' => \App\Common\Utils\Helper::getCurrentTime($end_time),
                '$gte' => \App\Common\Utils\Helper::getCurrentTime($start_time)
            ),
            'msgid' => array(
                '$ne' => ''
            )
        );
        $sort = array('create_time' => 1, '_id' => 1);
        $modelMsgTemplate = new \App\Qyweixin\Models\ExternalContact\MsgTemplate();
        $msgidList = $modelMsgTemplate->findAll($query, $sort);

        if (empty($msgidList)) {
            return;
        }

        try {
            foreach ($msgidList as $info) {

                // 进行锁定处理
                $provider_appid = $info['provider_appid'];
                $authorizer_appid = $info['authorizer_appid'];
                $agentid = $info['agentid'];
                $msgid = $info['msgid'];

                $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid . ' msgid:' . $msgid));
                $lock->setExpire(3600 * 8);
                if ($lock->lock()) {
                    continue;
                }

                // 如果缓存中已经存在那么就不做处理
                $cacheKey = 'get_groupmsg_task_list:' . $authorizer_appid . ':' . $agentid . ':' . $msgid;
                $userFromCache = $cache->get($cacheKey);
                if (!empty($userFromCache)) {
                    continue;
                }

                // 加缓存处理
                $expire_time = 8 * 60 * 60;
                $cache->save($cacheKey, $msgid, $expire_time);

                try {
                    $cursor = "";
                    do {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
                        $res = $weixinopenService->getGroupmsgTask($msgid, $limit, $cursor);

                        // 分页游标，下次请求时填写以获取之后分页的记录，如果已经没有更多的数据则返回空
                        if (empty($res['next_cursor'])) {
                            $cursor = "";
                        } else {
                            $cursor = $res['next_cursor'];
                        }
                    } while (!empty($cursor));
                } catch (\Exception $e) {
                    $modelActivityErrorLog->log($this->activity_id, $e, $now);
                }
            }
        } catch (\Exception $e) {
            $modelActivityErrorLog->log($this->activity_id, $e, $now);
        }
    }

    private function getChatdata($maxseqInfo, $agentInfo, $snList)
    {
        $chatdataList = array();
        $startmemory = memory_get_usage(true);
        $startTime = time();
        // echo "start memory=" . $startmemory  . "\n";
        // echo "start time=" . date('Y-m-d H:i:s', $startTime) . "\n";

        $resSdk = \weworkfinance_create($agentInfo['authorizer_appid'], $agentInfo['secret']);
        $max_seq = $maxseqInfo['max_seq'];
        // if (false) {
        // 获取聊天内容列表        
        for ($i = 0; $i < 10; $i++) {
            $oldmaxseq = $max_seq;
            $result = \weworkfinance_getchatdata($resSdk, $max_seq, 1000, 1200, "", "");
            $result = \json_decode($result, true);

            // echo "getchatdata result:\n";
            if (!empty($result['chatdata'])) {
                foreach ($result['chatdata'] as $item) {
                    // {
                    //     "publickey_ver": 1,
                    //     "encrypt_chat_msg": "JQ8ETrWPTBIdHNOsNjKkrO2AoWOvA/yJ2TSseouZlzfMdQuysnfouyCI/ezQWyWLXz9zCBPZtrW8OMz41HkgBqX8n7+PoxXqQI8i35AjXtZdwxqs3886jb6U1NLYqLtLOUPsjUye9a4KJTvPDKz/E29RxMGYH4kEapr3VPHx0tKw38oCgUSdFmWCJZN5mcF/jOZ832zrw1vsadEnc8JIqvftn9nRtk0hYCEcsVlM46ZRgu8W0+2HWGGlvHo5VhyqTeb3HFN/METX9Pw7anmV64PP57SsaPVugs9JQ1kNOcdrhj9TGaeIVCHlnvjpdBIrZCYj9+Z8EAnkt5my3kLYEetgovv7AdnneiSgLY=hui74kf0",
                    //     "msgid": "11203349837257011161_1594634329",
                    //     "encrypt_random_key": "R7eLotKjBMWMbGoMsjRM54eofjXlcmgqS1xU1CxEONST2Ei9Xu6NPZeasAJU9sXVQbD8KduLmGCEGYDIVz52pZ/+MPzw1dXsXNmtKG7Lgjj5H9hbyRuHZD1vRUdvTf0FwkhAWKaM4XEOz9X1Na7VKk78cPVKuHswE8BqlsdugM8=",
                    //     "seq": 1
                    // },
                    if (empty($snList['publickey_ver:' . $item['publickey_ver']])) {
                        $item['decrypt_key'] = "publickey_ver:{$item['publickey_ver']}未找到对应的私钥信息";
                        $item['decrypt_msg'] = '';
                    } else {
                        // 解密
                        $item['decrypt_key'] = $this->privateDecrypt($item['encrypt_random_key'], $snList['publickey_ver:' . $item['publickey_ver']]['private_key']);
                        $item['decrypt_msg'] =  \weworkfinance_decryptdata($item['decrypt_key'], $item['encrypt_chat_msg']);
                    }

                    if (!empty($item['decrypt_msg'])) {
                        $item['decrypt_msg'] = \json_decode($item['decrypt_msg'], true);
                    } else {
                        $item['decrypt_msg'] = array();
                    }
                    if (!empty($item['decrypt_msg'])) {
                        // 如果是图片消息
                        if (
                            isset($item['decrypt_msg']['msgtype']) &&
                            in_array($item['decrypt_msg']['msgtype'], array(
                                'image', 'voice', 'video', 'emotion',
                                'file', 'mixed', 'meeting_voice_call', 'voip_doc_share'
                            ))
                        ) {
                            $msgtype = $item['decrypt_msg']['msgtype'];
                            // 如果不是混合消息的话
                            if ($msgtype != 'mixed') {
                                // sdkfileid 有值的话
                                if (!empty($item['decrypt_msg'][$msgtype]['sdkfileid'])) {
                                    // 获取媒体内容
                                    $myFile = $this->getMediaFileContent($resSdk, $msgtype, $item['decrypt_msg'][$msgtype]['sdkfileid'], $item['decrypt_msg'][$msgtype]);
                                    if (!empty($myFile)) {
                                        $item['decrypt_msg'][$msgtype]['media_file'] = $myFile;
                                    }
                                }
                            } else {
                                // 混合消息的时候特殊处理
                                foreach ($item['decrypt_msg'][$msgtype]['item'] as $key => $itemInfo) {
                                    $type = $itemInfo['type'];
                                    $itemInfo['content'] = \json_decode($itemInfo['content'], true);
                                    if (in_array($type, array(
                                        'image', 'voice', 'video', 'emotion',
                                        'file', 'meeting_voice_call', 'voip_doc_share'
                                    ))) {
                                        // sdkfileid 有值的话
                                        if (!empty($itemInfo['content']['sdkfileid'])) {
                                            // 获取媒体内容
                                            $myFile = $this->getMediaFileContent($resSdk, $type, $itemInfo['content']['sdkfileid'], $itemInfo['content']);
                                            if (!empty($myFile)) {
                                                $itemInfo['content']['media_file'] = $myFile;
                                            }
                                        }
                                    }
                                    // 重新保存
                                    $item['decrypt_msg'][$msgtype]['item'][$key] = $itemInfo;
                                }
                            }
                        }
                    }

                    if ($max_seq < $item['seq']) {
                        $max_seq = $item['seq'];
                    }

                    // print_r($item);
                    // echo "\n";
                    $chatdataList[$item['msgid']] = $item;
                }
            }

            // 如果发生了未改变的话
            if ($oldmaxseq == $max_seq) {
                break;
            }
        }
        // }
        \weworkfinance_free($resSdk);

        $endmemory = memory_get_usage(true);
        $endTime = time();
        // echo "end memory=" . $endmemory . "\n";
        // echo "end time=" . date('Y-m-d H:i:s', $endTime) . "\n";
        if ($endmemory - $startmemory > 0) {
            echo "different memory=" . ($endmemory - $startmemory) . "\n";
            echo "used time=" . ($endTime - $startTime) . "\n";
        }

        return array('max_seq' => $max_seq, 'chatdataList' => $chatdataList);
    }

    /**
     * 获取企业标签库
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getcorptaglist
     * @param array $params
     */
    public function getcorptaglistAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $cache = $this->getDI()->get("cache");

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array(
                'agentid' => '9999999'
            );
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {
                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];

                    $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'provider_appid:' . $provider_appid . ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid));
                    $lock->setExpire(3600 * 8);
                    if ($lock->lock()) {
                        continue;
                    }

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'get_corp_tag_list:' . $authorizer_appid . ':' . $agentid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $agentid, $expire_time);

                    try {
                        $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agentid);
                        $weixinopenService->getCorpTagList();
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
     * 获取「联系客户统计」数据
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getuserbehaviordata
     * @param array $params
     */
    public function getuserbehaviordataAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $yesterday = date("Y-m-d", $now - 24 * 3600);
        $start_time = strtotime($yesterday . " 00:00:00");
        $end_time = strtotime($yesterday . " 23:59:59");
        $cache = $this->getDI()->get("cache");

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array(
                'agentid' => '9999999'
            );
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {
                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];

                    $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'provider_appid:' . $provider_appid . ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid));
                    $lock->setExpire(3600 * 8);
                    if ($lock->lock()) {
                        continue;
                    }

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'getuserbehaviordata:' . $authorizer_appid . ':' . $agentid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $agentid, $expire_time);

                    try {
                        $modelFollowUser = new \App\Qyweixin\Models\ExternalContact\FollowUser();
                        $query = array();
                        $sort = array('_id' => 1);
                        $ecFolowUserList = $modelFollowUser->findAll($query, $sort);
                        if (!empty($ecFolowUserList)) {
                            foreach ($ecFolowUserList as $info) {

                                $externaluser_follow_user = $info['follow_user'];
                                $provider_appid = $info['provider_appid'];
                                $authorizer_appid = $info['authorizer_appid'];
                                $externaluser_agent_agentid = '9999999';

                                // 如果缓存中已经存在那么就不做处理
                                $cacheKey = 'getuserbehaviordata:' . $authorizer_appid . ":" . $externaluser_follow_user;
                                $userFromCache = $cache->get($cacheKey);
                                if (!empty($userFromCache)) {
                                    continue;
                                }
                                // 加缓存处理
                                $expire_time = 8 * 60 * 60;
                                $cache->save($cacheKey, $externaluser_follow_user, $expire_time);

                                try {
                                    $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                                    $weixinopenService->getUserBehaviorDataByUserId($externaluser_follow_user, $start_time, $end_time);
                                } catch (\Exception $e) {
                                    $modelActivityErrorLog->log($this->activity_id, $e, $now);
                                }
                            }
                        }
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
     * 获取「群聊数据统计」数据
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php qyweixin getgroupchatstatistic
     * @param array $params
     */
    public function getgroupchatstatisticAction(array $params)
    {
        $modelActivityErrorLog = new \App\Activity\Models\ErrorLog();
        $now = time();
        $yesterday = date("Y-m-d", $now - 24 * 3600);
        $start_time = strtotime($yesterday . " 00:00:00");
        $end_time = strtotime($yesterday . " 23:59:59");
        $cache = $this->getDI()->get("cache");

        try {
            $modelAgent = new \App\Qyweixin\Models\Agent\Agent();
            $query = array(
                'agentid' => '9999999'
            );
            $sort = array('_id' => 1);
            $agentList = $modelAgent->findAll($query, $sort);
            if (!empty($agentList)) {
                foreach ($agentList as $agentItem) {

                    // 进行锁定处理
                    $provider_appid = $agentItem['provider_appid'];
                    $authorizer_appid = $agentItem['authorizer_appid'];
                    $agentid = $agentItem['agentid'];

                    $lock = new \iLock(\App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, 'provider_appid:' . $provider_appid . ' authorizer_appid:' . $authorizer_appid . ' agentid:' . $agentid));
                    $lock->setExpire(3600 * 8);
                    if ($lock->lock()) {
                        continue;
                    }

                    // 如果缓存中已经存在那么就不做处理
                    $cacheKey = 'getgroupchatstatistic:' . $authorizer_appid . ':' . $agentid;
                    $userFromCache = $cache->get($cacheKey);
                    if (!empty($userFromCache)) {
                        continue;
                    }

                    // 加缓存处理
                    $expire_time = 8 * 60 * 60;
                    $cache->save($cacheKey, $agentid, $expire_time);

                    try {
                        $modelFollowUser = new \App\Qyweixin\Models\ExternalContact\FollowUser();
                        $query = array();
                        $sort = array('_id' => 1);
                        $ecFolowUserList = $modelFollowUser->findAll($query, $sort);
                        if (!empty($ecFolowUserList)) {
                            foreach ($ecFolowUserList as $info) {

                                $externaluser_follow_user = $info['follow_user'];
                                $provider_appid = $info['provider_appid'];
                                $authorizer_appid = $info['authorizer_appid'];
                                $externaluser_agent_agentid = '9999999';

                                // 如果缓存中已经存在那么就不做处理
                                $cacheKey = 'getgroupchatstatistic:' . $authorizer_appid . ":" . $externaluser_follow_user;
                                $userFromCache = $cache->get($cacheKey);
                                if (!empty($userFromCache)) {
                                    continue;
                                }
                                // 加缓存处理
                                $expire_time = 8 * 60 * 60;
                                $cache->save($cacheKey, $externaluser_follow_user, $expire_time);

                                try {
                                    $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $externaluser_agent_agentid);
                                    $weixinopenService->getGroupChatStatisticGroupByDay($externaluser_follow_user, $start_time, $end_time);
                                } catch (\Exception $e) {
                                    $modelActivityErrorLog->log($this->activity_id, $e, $now);
                                }
                            }
                        }
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
     * @uses 私钥解密
     * @param string $encrypted
     * @return null
     */
    private function privateDecrypt($encrypted, $privateKey)
    {
        if (!is_string($encrypted)) {
            return null;
        }

        $decrypted = "";
        $part_len = 2048 / 8;
        $base64_decoded = base64_decode($encrypted);
        $parts = str_split($base64_decoded, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_private_decrypt($part, $decrypted_temp, $privateKey);
            $decrypted .= $decrypted_temp;
        }
        // wt7QSvEU5gplyq+7ilqR7f18R9A3tz/2CNqhO4OWAUETHlRoWxvPnM00hyFAtgIa1gi3VN9jx/uV9MITJrtUww==
        // wt7QSvEU5gplyq+7ilqR7f18R9A3tz/2CNqhO4OWAUETHlRoWxvPnM00hyFAtgIa1gi3VN9jx/uV9MITJrtUww==
        // echo "decrypted:" . $decrypted;
        return $decrypted;
        //return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, $encrypted)) ? $decrypted : null;
    }

    private function getMediaFileContent($resSdk, $msgtype, $sdkfileid, $msgContent)
    {
        $path = APP_PATH . '/public/chatdata/';
        $ext = '';
        $myFile = "";

        // 获取媒体内容
        $indexbuf = "";
        $mediaContent = "";
        // 循环获取
        while (true) {
            $ret4Media = \weworkfinance_getmediadata(
                $resSdk,
                $indexbuf,
                $sdkfileid,
                600, //十分钟超时
                "",
                ""
            );
            if (empty($ret4Media)) {
                $ret4Media = array();
            } else {
                $ret4Media = \json_decode($ret4Media, true);
            }
            if (empty($ret4Media)) {
                break;
            }
            if (!empty($ret4Media['content'])) {
                $mediaContent .= base64_decode($ret4Media['content']);
            }
            if (!empty($ret4Media['isMediaDataFinish'])) {
                break;
            }
            if (!empty($ret4Media['newIndexBuf'])) {
                $indexbuf  = $ret4Media['newIndexBuf'];
            }
        }
        if (!empty($mediaContent)) {
            if ($msgtype == 'image') {
                $ext = '.jpg';
            } elseif ($msgtype == 'voice') {
                $ext = '.mp3';
            } elseif ($msgtype == 'video') {
                $ext = '.mp4';
            } elseif ($msgtype == 'emotion') {
                $emotion_type = $msgContent['type'];
                //type	表情类型，png或者gif.1表示gif 2表示png。Uint32类型
                if ($emotion_type == 1) {
                    $ext = '.gif';
                } elseif ($emotion_type == 2) {
                    $ext = '.png';
                }
            } elseif ($msgtype == 'file') {
                $fileext = $msgContent['fileext'];
                $ext = '.' . $fileext;
            } elseif ($msgtype == 'meeting_voice_call') {
                if (!empty($msgContent['demofiledata'])) {
                    $myFile = $msgContent['demofiledata']['filename'];
                }
            } elseif ($msgtype == 'voip_doc_share') {
                //"filename": "欢迎使用微盘.pdf.pdf",
                $myFile = $msgContent['filename'];
            }

            if (empty($myFile)) {
                $myFile = 'getmediadata_' . \uniqid() . $ext;
            }
            // 先本地保存
            file_put_contents($path . $myFile, $mediaContent);
            // 可以改成OSS保存
        }

        return $myFile;
    }
}
