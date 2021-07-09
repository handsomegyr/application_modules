<?php
namespace App\Message\Controllers;

class ServiceController extends ControllerBase
{

    private $modelMsg = null;

    private $modelSysMsg = null;

    private $modelReplyMsg = null;

    private $modelMsgStatistics = null;

    private $modelMsgCount = null;

    private $modelMember = null;

    private $modelMemberFriend = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelMemberFriend = new \App\Member\Models\Friend();
        $this->modelMember = new \App\Member\Models\Member();
        $this->modelMsg = new \App\Message\Models\Msg();
        $this->modelSysMsg = new \App\Message\Models\SysMsg();
        $this->modelReplyMsg = new \App\Message\Models\ReplyMsg();
        $this->modelMsgStatistics = new \App\Message\Models\MsgStatistics();
        $this->modelMsgCount = new \App\Message\Models\MsgCount();
    }

    /**
     * 发私信的接口
     */
    public function insertuserprivatemsgAction()
    {
        // http://www.myapplicationmodule.com/message/service/insertuserprivatemsg?msgToUID=10605005&msgContent=hello
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $msgToUID = ($this->get('msgToUID', ''));
            if (empty($msgToUID)) {
                echo ($this->error(- 1, '用户ID为空'));
                return false;
            }
            $memberInfo = $this->modelMember->getInfoById($msgToUID);
            if (empty($memberInfo)) {
                echo ($this->error(- 2, '用户ID不正确'));
                return false;
            }
            $msgContent = urldecode($this->get('msgContent', ''));
            if (empty($msgContent)) {
                echo ($this->error(- 3, '消息内容为空'));
                return false;
            }
            
            // 私信 1:仅限好友 2 禁止
            $isCanSendPrivMsg = $this->modelMember->isCanSendPrivMsg($memberInfo, $_SESSION['member_id']);
            if (! $isCanSendPrivMsg) {
                echo ($this->error(- 4, '不能给该用户发私信'));
                return false;
            }
            
            $this->modelMsg->log($_SESSION['member_id'], $msgToUID, $msgContent);
            $this->modelMsgStatistics->log($_SESSION['member_id'], $msgToUID, $msgContent);
            $this->modelMsgCount->incPrivateMsgCount($msgToUID);
            
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取私信列表的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getuserprivmsglistAction()
    {
        // http://member.1yyg.com/JPData?action=getUserPrivMsgList&FIdx=1&EIdx=5&isCount=1&fun=jsonp1451059658328&_=1451059658486
        // http://www.myapplicationmodule.com/message/service/getuserprivmsglist?page=1&limit=5
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));
            $list = $this->modelMsgStatistics->getListByUserId($_SESSION['member_id'], $page, $limit);
            
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    if ($item['user1_id'] != $_SESSION['member_id']) {
                        $user_ids[] = $item['user1_id'];
                    } elseif ($item['user2_id'] != $_SESSION['member_id']) {
                        $user_ids[] = $item['user2_id'];
                    }
                }
                $memberList = $this->modelMember->getListByIds($user_ids);
                
                foreach ($list['datas'] as $item) {
                    // "userName": "18917****57",
                    // "userPhoto": "00000000000000000.jpg",
                    // "userWeb": "1011789946",
                    // "senderUserID": 10605005,
                    // "msgContents": "[my]hello",
                    // "msgNum": 4,
                    // "unReadNum": 0,
                    // "showTime": "今天 09:39"
                    $user_id = '';
                    if ($item['user1_id'] != $_SESSION['member_id']) {
                        $user_id = $item['user1_id'];
                    } elseif ($item['user2_id'] != $_SESSION['member_id']) {
                        $user_id = $item['user2_id'];
                    }
                    if (! isset($memberList[$user_id])) {
                        throw new \Exception("{$user_id}对应的会员信息不存在");
                    }
                    $memberInfo = $memberList[$user_id];
                    
                    $datas[] = array(
                        'is_me' => ($_SESSION['member_id'] == $item['msg_user_id']) ? 1 : 0,
                        'senderUserID' => ($item['user1_id'] == $_SESSION['member_id']) ? $item['user2_id'] : $item['user1_id'],
                        'userName' => $this->modelMember->getRegisterName($memberInfo, true),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $memberInfo['avatar']),
                        'userWeb' => $memberInfo['_id'],
                        'msgContents' => $item['content'],
                        'msgNum' => $item['msg_num'],
                        'unReadNum' => ($item['msg_user_id'] == $item['user1_id']) ? $item['user2_unread_num'] : $item['user1_unread_num'],
                        'showTime' => date('Y-m-d H:i:s', $item['msg_time']->sec)
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取某私信详细列表的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getuserprivmsgdetaillistAction()
    {
        // jsonp1451060106301({"code":0,"count":6,"data":[{"userName":"18917****57","userPhoto":"00000000000000000.jpg","userWeb":"1011789946","msgID":1212280,"senderUserID":10605005,"msgContents":"kjkjkjk","showTime":"昨天 23:50"},{"userName":"郭永荣","userPhoto":"20151106195125381.jpg","userWeb":"1010381532","msgID":1212279,"senderUserID":9563477,"msgContents":"nnnnnn","showTime":"昨天 23:47"},{"userName":"郭永荣","userPhoto":"20151106195125381.jpg","userWeb":"1010381532","msgID":1211267,"senderUserID":9563477,"msgContents":"hello","showTime":"昨天 09:39"},{"userName":"郭永荣","userPhoto":"20151106195125381.jpg","userWeb":"1010381532","msgID":1209762,"senderUserID":9563477,"msgContents":"vvvvvv","showTime":"12月23日 23:02"},{"userName":"郭永荣","userPhoto":"20151106195125381.jpg","userWeb":"1010381532","msgID":1209758,"senderUserID":9563477,"msgContents":"nvvbvb","showTime":"12月23日 22:59"}]})
        // http://member.1yyg.com/JPData?action=getUserPrivMsgDetailList&sendUserID=9533390&FIdx=1&EIdx=5&isCount=1&fun=jsonp1451049461581&_=1451049461724
        // http://www.myapplicationmodule.com/message/service/getuserprivmsgdetaillist?page=1&limit=5&sendUserID=9533390
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $sendUserID = ($this->get('sendUserID', ''));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));
            $list = $this->modelMsg->getListBy2UserId($_SESSION['member_id'], $sendUserID, $page, $limit);
            
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    $user_ids[] = $item['from_user_id'];
                }
                $memberList = $this->modelMember->getListByIds($user_ids);
                
                foreach ($list['datas'] as $item) {
                    // "userName": "18917****57",
                    // "userPhoto": "00000000000000000.jpg",
                    // "userWeb": "1011789946",
                    // "senderUserID": 10605005,
                    // "msgContents": "[my]hello",
                    // "showTime": "今天 09:39"
                    // "msgID":1212280,
                    $user_id = $item['from_user_id'];
                    if (! isset($memberList[$user_id])) {
                        throw new \Exception("{$user_id}对应的会员信息不存在");
                    }
                    $memberInfo = $memberList[$user_id];
                    
                    $datas[] = array(
                        'is_me' => ($_SESSION['member_id'] == $item['from_user_id']) ? 1 : 0,
                        'senderUserID' => $item['to_user_id'],
                        'userName' => $this->modelMember->getRegisterName($memberInfo, true),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $memberInfo['avatar']),
                        'userWeb' => $memberInfo['_id'],
                        'msgContents' => $item['content'],
                        'msgID' => $item['_id'],
                        'showTime' => date('Y-m-d H:i:s', $item['msg_time']->sec)
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 删除私信的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function deluserprivatemsgAction()
    {
        // http://member.1yyg.com/JPData?action=ignoreUserFriend&applyID=37301058&fun=jsonp1451046553987&_=1451046593893
        // http://member.1yyg.com/JPData?action=ignoreUserFriend&applyID=0&fun=jsonp1451046733341&_=1451046741989
        // http://www.myapplicationmodule.com/message/service/ignoreuserfriend?applyID=37233515
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $applyID = $this->get('applyID', '');
            $this->modelMemberFriend->ignore($_SESSION['member_id'], $applyID);
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取系统消息列表的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getusermessagelistAction()
    {
        // http://member.1yyg.com/JPData?action=getUserMessageList&FIdx=1&EIdx=10&isCount=1&fun=jsonp1451058226199&_=1451058226317
        // jsonp1451058226199({"code":0,"count":2,"countEx":0,"data":[{"msgID":4432753,"msgContent":"<a href=\"http://u.1yyg.com/1010381532\" class=\"blue\" target=\"_blank\">郭永荣</a> 已通过您的好友请求。","msgRead":0,"showTime":"1分钟前"},{"msgID":4386968,"msgContent":"<a href=\"http://u.1yyg.com/1010381532\" class=\"blue\" target=\"_blank\">郭永荣</a> 已通过您的好友请求。","msgRead":1,"showTime":"12月23日 20:39"}]})
        // http://www.myapplicationmodule.com/message/service/getusermessagelist?page=1&limit=10
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));
            $otherConditions = array();
            $otherConditions['is_read'] = false;
            $list = $this->modelSysMsg->getListByUserId($_SESSION['member_id'], $page, $limit);
            
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "msgID":4432753,
                    // "msgContent":"<a href=\"http://u.1yyg.com/1010381532\" class=\"blue\" target=\"_blank\">郭永荣</a> 已通过您的好友请求。",
                    // "msgRead":0,
                    // "showTime":"1分钟前"
                    $datas[] = array(
                        'msgID' => $item['_id'],
                        'msgContent' => $item['content'],
                        'msgRead' => 0,
                        'showTime' => date('Y-m-d H:i:s', $item['msg_time']->sec)
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 个人主页上发私信
     *
     * @return boolean
     */
    public function insertmsgAction()
    {
        // http://member.1yyg.com/JPData?action=insertMsg&msgTouserWeb=1010029819&msgContent=bbbbbb
        // ({'code':1})
        // http://www.myapplicationmodule.com/message/service/insertmsg?msgTouserWeb=1010029819&msgContent=bbbbbb
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $msgTouserWeb = $this->get('msgTouserWeb', '');
            if (empty($msgTouserWeb)) {
                echo $this->error(- 1, '接受者未指定');
                return false;
            }
            $memberInfo = $this->modelMember->getInfoById($msgTouserWeb);
            if (empty($memberInfo)) {
                echo ($this->error(- 2, '接受者ID不正确'));
                return false;
            }
            $msgContent = urldecode($this->get('msgContent', ''));
            if (empty($msgContent)) {
                echo $this->error(- 3, '消息内容为空');
                return false;
            }
            
            // 私信 1:仅限好友 2 禁止
            $isCanSendPrivMsg = $this->modelMember->isCanSendPrivMsg($memberInfo, $_SESSION['member_id']);
            if (! $isCanSendPrivMsg) {
                echo ($this->error(- 4, '不能给该用户发私信'));
                return false;
            }
            
            $this->modelMsg->log($_SESSION['member_id'], $msgTouserWeb, $msgContent);
            $this->modelMsgStatistics->log($_SESSION['member_id'], $msgTouserWeb, $msgContent);
            $this->modelMsgCount->incPrivateMsgCount($msgTouserWeb);
            
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取评论回复列表的接口
     * 会员-消息管理
     *
     * @throws \Exception
     * @return boolean
     */
    public function getreplymsgpagebyuseridAction()
    {
        // http://member.1yyg.com/JPData?action=getReplyMsgPageByUserID&FIdx=1&EIdx=10&isCount=1&fun=jsonp1450968515994&_=1450968516380
        // jsonp1452319344045({"code":0,"count":7,"data":[{"relateID":"125584","msgType":"1","youContent":"运气很好运气很好","heContent":"太牛逼","replyTime":"2015-12-24 22:19","replyUserID":"9563477","userName":"郭永荣","userWeb":"1010381532","msgID":229235},{"relateID":"125584","msgType":"1","youContent":"运气很好运气很好","heContent":"very&nbsp;goods","replyTime":"2015-12-24 21:57","replyUserID":"9563477","userName":"郭永荣","userWeb":"1010381532","msgID":229135},{"relateID":"125584","msgType":"1","youContent":"运气很好运气很好","heContent":"very&nbsp;lucky","replyTime":"2015-12-24 21:55","replyUserID":"9563477","userName":"郭永荣","userWeb":"1010381532","msgID":229127},{"relateID":"125584","msgType":"1","youContent":"运气很好运气很好","heContent":"真的很好","replyTime":"2015-12-24 21:32","replyUserID":"9563477","userName":"郭永荣","userWeb":"1010381532","msgID":229051},{"relateID":"125584","msgType":"1","youContent":"很是让人羡慕啊","heContent":"看来我也要去买一个","replyTime":"2015-12-24 09:47","replyUserID":"9563477","userName":"郭永荣","userWeb":"1010381532","msgID":228252},{"relateID":"125584","msgType":"1","youContent":"很是让人羡慕啊","heContent":"我也想中一个","replyTime":"2015-12-21 18:24","replyUserID":"9563477","userName":"郭永荣","userWeb":"1010381532","msgID":222547},{"relateID":"125584","msgType":"1","youContent":"很是让人羡慕啊","heContent":"让人羡慕啊","replyTime":"2015-12-21 18:21","replyUserID":"9563477","userName":"郭永荣","userWeb":"1010381532","msgID":222538}]})
        // http://www.myapplicationmodule.com/message/service/getreplymsgpagebyuserid?page=1&limit=10
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));
            
            $otherConditions = array();
            $sort = array();
            $list = $this->modelReplyMsg->getReplyMsgPageByUserID($_SESSION['member_id'], $page, $limit, $otherConditions, $sort);
            
            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (! empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    
                    // "relateID":"125584",
                    // "msgType":"1",
                    // "youContent":"运气很好运气很好",
                    // "heContent":"太牛逼",
                    // "replyTime":"2015-12-24 22:19",
                    // "replyUserID":"9563477",
                    // "userName":"郭永荣",
                    // "userWeb":"1010381532",
                    // "msgID":229235
                    
                    $datas[] = array(
                        'relateID' => $item['relate_id'],
                        'msgType' => empty($item['to_user_content']) ? 0 : 1,
                        'youContent' => $item['reply_content'],
                        'heContent' => $item['to_user_content'],
                        'replyTime' => date('Y-m-d H:i:s', $item['msg_time']->sec),
                        'replyUserID' => $item['reply_user_id'],
                        'userName' => getBuyerName($item['reply_user_name'], $item['reply_user_register_by']),
                        'userWeb' => $item['reply_user_id'],
                        'msgID' => $item['_id']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 删除某帖子某回复的接口
     * 会员 - 消息管理
     */
    public function deletereplymsgbyuseridAction()
    {
        // http://member.1yyg.com/JPData?action=deleteReplyMsgByUserID&msgID=229135&fun=jsonp1452414464329&_=1452414485008
        // http://www.myapplicationmodule.com/message/service/deletereplymsgbyuserid&msgID=229135
        try {
            $msgID = ($this->get('msgID', ''));
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            if ($msgID) {
                $info = $this->modelReplyMsg->getInfoById($msgID);
                if (empty($info)) {
                    echo ($this->error('-1', '消息ID不正确'));
                    return false;
                }
                $this->modelReplyMsg->remove(array(
                    '_id' => $msgID
                ));
            } else {
                $this->modelReplyMsg->removeByUserId($_SESSION['member_id']);
            }
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 用户消息数量的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getusermsgcountAction()
    {
        // http://www.myapplicationmodule.com/message/service/getusermsgcount
        try {
            $this->view->disable();
            
            // FriendCount: 0
            // privMsgCount: 0
            // replyMsgCount: 0
            // sysMsgCount: 0
            $ret = array();
            $ret['FriendCount'] = 0;
            $ret['privMsgCount'] = 0;
            $ret['replyMsgCount'] = 0;
            $ret['sysMsgCount'] = 0;
            
            if (! empty($_SESSION['member_id'])) {
                $msgCountInfo = $this->modelMsgCount->getInfoByUserId($_SESSION['member_id']);
                $ret['FriendCount'] = $msgCountInfo['friendMsgCount'];
                $ret['privMsgCount'] = $msgCountInfo['privMsgCount'];
                $ret['replyMsgCount'] = $msgCountInfo['replyMsgCount'];
                $ret['sysMsgCount'] = $msgCountInfo['sysMsgCount'];
            }
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 删除私信的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function deleteusermessageAction()
    {
        // http://member.1yyg.com/JPData?action=deleteUserMessage&msgID=4797501&fun=jsonp1453019249148&_=1453019255911
        // http://member.1yyg.com/JPData?action=deleteUserMessageAll&fun=jsonp1453019249151&_=1453019287487
        // http://www.myapplicationmodule.com/message/service/deleteusermessage?msgID=37233515
        try {
            $msgID = ($this->get('msgID', ''));
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (! $isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            if ($msgID) {
                $this->modelSysMsg->remove(array(
                    '_id' => $msgID
                ));
            } else {
                $this->modelSysMsg->removeByUserId($_SESSION['member_id']);
            }
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}