<?php
namespace Webcms\Member\Controllers;

use Phalcon\Mvc\View;

/**
 * 我的1元云购
 *
 * @author Kan
 *        
 */
class IndexController extends ControllerBase
{

    private $modelMemberFriend = null;

    private $modelMemberNews = null;

    private $modelPost = null;

    private $modelGoods = null;

    private $modelOrderLog = null;

    private $modelOrderGoods = null;

    private $modelMsgStatistics = null;

    private $modelMsgCount = null;

    private $modelPayLog = null;

    private $modelInvitation = null;

    public function initialize()
    {
        parent::initialize();
        $this->modelMemberFriend = new \Webcms\Member\Models\Friend();
        $this->modelMemberNews = new \Webcms\Member\Models\News();
        $this->modelPost = new \Webcms\Post\Models\Post();
        $this->modelGoods = new \Webcms\Goods\Models\Goods();
        $this->modelOrderLog = new \Webcms\Order\Models\Log();
        
        $this->modelOrderGoods = new \Webcms\Order\Models\Goods();
        $this->modelMsgStatistics = new \Webcms\Message\Models\MsgStatistics();
        $this->modelMsgCount = new \Webcms\Message\Models\MsgCount();
        $this->modelPayLog = new \Webcms\Payment\Models\Log();
        $this->modelInvitation = new \Webcms\Invitation\Models\Invitation();
        $this->modelInvitation->setIsExclusive(false);
    }

    /**
     * 我的1元云购首页
     */
    public function indexAction()
    {
        // http://webcms.didv.cn/member/index/index
        // 消息数
        $msgCountInfo = $this->modelMsgCount->getInfoByUserId($_SESSION['member_id']);
        $this->assign('msgCount', $msgCountInfo['sysMsgCount'] + $msgCountInfo['privMsgCount'] + $msgCountInfo['friendMsgCount'] + $msgCountInfo['replyMsgCount']);
        
        $myInvitationInfo = $this->modelInvitation->getInfoByUserId($_SESSION['member_id'], YUNGOU_ACTIVITY_ID);
        $this->assign('invitationInfo', $myInvitationInfo);
        // 待确认
        // 1待确认地址 2待发货 3待收货 4待晒单
        $otherConditions = array();
        $otherConditions['order_state'] = \Webcms\Order\Models\Goods::ORDER_STATE1;
        $confirmNum4Wait = $this->modelOrderGoods->getOrderCountByBuyerId($_SESSION['member_id'], $otherConditions);
        $this->assign('confirmNum4Wait', $confirmNum4Wait);
        
        // 待发货
        $otherConditions = array();
        $otherConditions['order_state'] = \Webcms\Order\Models\Goods::ORDER_STATE2;
        $deliveryNum4Wait = $this->modelOrderGoods->getOrderCountByBuyerId($_SESSION['member_id'], $otherConditions);
        $this->assign('deliveryNum4Wait', $deliveryNum4Wait);
        
        // 待收货
        $otherConditions = array();
        $otherConditions['order_state'] = \Webcms\Order\Models\Goods::ORDER_STATE3;
        $receiveNum4Wait = $this->modelOrderGoods->getOrderCountByBuyerId($_SESSION['member_id'], $otherConditions);
        $this->assign('receiveNum4Wait', $receiveNum4Wait);
        
        // 好友动态
        $friend_ids = $this->modelMemberFriend->getMyFriendIds($_SESSION['member_id'], 1, 1000);
        if (! empty($friend_ids)) {
            $otherConditions = array();
            $otherConditions['user_id'] = array(
                '$in' => $friend_ids
            );
            $newsList = $this->modelMemberNews->getNewsList(1, 10, $otherConditions);
            $this->assign('newsList', $newsList['datas']);
        }
        
        // 获得的商品
        $list = $this->modelOrderGoods->getUserWinList($_SESSION['member_id'], 1, 1);
        if (! empty($list['datas'])) {
            $this->assign('orderInfo', $list['datas'][0]);
        }
    }

    /**
     * 网银充值页面
     */
    public function userrechargeAction()
    {
        // http://webcms.didv.cn/member/index/userrecharge
    }

    /**
     * 卡充值页面
     */
    public function cardrechargeAction()
    {
        // http://webcms.didv.cn/member/index/cardrecharge
    }

    /**
     * 云购记录页面
     */
    public function userbuylistAction()
    {
        // http://webcms.didv.cn/member/index/userbuylist
    }

    /**
     * 云购记录详细页面
     */
    public function userbuydetailAction()
    {
        // http://webcms.didv.cn/member/index/userbuydetail?goods_id=xxx
        $this->view->disableLevel(View::LEVEL_LAYOUT);
        $goods_id = $this->get('goods_id', '');
        if (empty($goods_id)) {
            die('error');
        }
        $goodsInfo = $this->modelGoods->getInfoById($goods_id);
        $this->assign('goodsInfo', $goodsInfo);
    }

    /**
     * 获得的商品页面
     */
    public function orderlistAction()
    {
        // http://webcms.didv.cn/member/index/orderlist
    }

    /**
     * 获得的商品详情页面
     */
    public function orderdetailAction()
    {
        // http://webcms.didv.cn/member/index/orderdetail?orderno=xxxx
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        $orderno = $this->get('orderno', '');
        if (empty($orderno)) {
            die('error');
        }
        $orderInfo = $this->modelOrderGoods->getInfoByOrderNo($orderno);
        $this->assign('orderInfo', $orderInfo);
        if (! empty($orderInfo['post_id'])) {
            $postInfo = $this->modelPost->getInfoById($orderInfo['post_id']);
            $this->assign('postInfo', $postInfo);
        }
        // 获取订单日志
        $logList = $this->modelOrderLog->getListByOrderId($orderno);
        $this->assign('logList', $logList);
    }

    /**
     * 晒单管理页面
     */
    public function postsinglelistAction()
    {
        // http://webcms.didv.cn/member/index/postsinglelist
    }

    /**
     * 晒单添加页面
     */
    public function postsingleaddAction()
    {
        // http://webcms.didv.cn/member/index/postsingleadd?goods_id=xxx
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        $goods_id = $this->get('goods_id', '');
        if (empty($goods_id)) {
            die('error');
        }
        $goodsInfo = $this->modelGoods->getInfoById($goods_id);
        if (empty($goodsInfo)) {
            die('error');
        }
        $this->assign('goodsInfo', $goodsInfo);
        // 获取晒单记录
        $postInfo = $this->modelPost->getInfoByBuyerIdAndGoodsId($_SESSION['member_id'], $goods_id);
        if (empty($postInfo)) {
            die('error');
        }
        $this->assign('postInfo', $postInfo);
        if ($postInfo['state'] == \Webcms\Post\Models\Post::STATE2) {
            die('error');
        }
    }

    /**
     * 晒单添加页面
     */
    public function postsingleeditAction()
    {
        // http://webcms.didv.cn/member/index/postsingleedit?goods_id=xxx
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        $goods_id = $this->get('goods_id', '');
        if (empty($goods_id)) {
            die('error');
        }
        $goodsInfo = $this->modelGoods->getInfoById($goods_id);
        if (empty($goodsInfo)) {
            die('error');
        }
        $this->assign('goodsInfo', $goodsInfo);
        // 获取晒单记录
        $postInfo = $this->modelPost->getInfoByBuyerIdAndGoodsId($_SESSION['member_id'], $goods_id);
        if (empty($postInfo)) {
            die('error');
        }
        $this->assign('postInfo', $postInfo);
        if ($postInfo['state'] == \Webcms\Post\Models\Post::STATE2) {
            die('error');
        }
    }

    /**
     * 我的关注页面
     */
    public function collectlistAction()
    {
        // http://webcms.didv.cn/member/index/collectlist
    }

    /**
     * 我的账户页面
     */
    public function userbalanceAction()
    {
        // http://webcms.didv.cn/member/index/userbalance
        // 充值总额：￥1.00 消费总额：￥1.00 转入总额：￥0.00 转出总额：￥0.00
        $summaryMoney4Type1 = $this->modelPayLog->getSummaryMoney($_SESSION['member_id'], \Webcms\Payment\Models\Log::TYPE1);
        $this->assign('summaryMoney4Type1', $summaryMoney4Type1);
        
        $summaryMoney4Type2 = $this->modelPayLog->getSummaryMoney($_SESSION['member_id'], \Webcms\Payment\Models\Log::TYPE2);
        $this->assign('summaryMoney4Type2', $summaryMoney4Type2);
        
        $summaryMoney4Type3 = $this->modelPayLog->getSummaryMoney($_SESSION['member_id'], \Webcms\Payment\Models\Log::TYPE3);
        $this->assign('summaryMoney4Type3', $summaryMoney4Type3);
        
        // $summaryMoney4Type4 = $this->modelPayLog->getSummaryMoney($_SESSION['member_id'], \Webcms\Payment\Models\Log::TYPE4);
        // $this->assign('summaryMoney4Type4', $summaryMoney4Type4);
    }

    /**
     * 我的好友页面
     */
    public function myfriendsAction()
    {
        // http://webcms.didv.cn/member/index/myfriends
    }

    /**
     * 查找好友页面
     */
    public function searchfriendsAction()
    {
        // http://webcms.didv.cn/member/index/searchfriends
    }

    /**
     * 云购圈-加入圈子页面
     */
    public function joingroupAction()
    {
        // http://webcms.didv.cn/member/index/joingroup
    }

    /**
     * 云购圈-发表的话题页面
     */
    public function joingroup01Action()
    {
        // http://webcms.didv.cn/member/index/joingroup01
    }

    /**
     * 云购圈-回复的话题页面
     */
    public function joingroup02Action()
    {
        // http://webcms.didv.cn/member/index/joingroup02
    }

    /**
     * 邀请管理页面
     */
    public function invitedlistAction()
    {
        // http://webcms.didv.cn/member/index/invitedlist
        $myInvitationInfo = $this->modelInvitation->getInfoByUserId($_SESSION['member_id'], YUNGOU_ACTIVITY_ID);
        $this->assign('invitationInfo', $myInvitationInfo);
        // 成功邀请 0 位会员注册，已有 0 位会员参与云购，您通过邀请获得奖励：0 福分
    }

    /**
     * 佣金明细页面
     */
    public function commissionqueryAction()
    {
        // http://webcms.didv.cn/member/index/commissionquery
    }

    /**
     * 我的福分页面
     */
    public function memberpointsAction()
    {
        // http://webcms.didv.cn/member/index/memberpoints
    }

    /**
     * 消息管理页面
     */
    public function usermessageAction()
    {
        // http://webcms.didv.cn/member/index/usermessage
        // 更新已读
        $this->modelMsgCount->clearSysMsgCount($_SESSION['member_id']);
    }

    /**
     * 好友请求页面
     */
    public function friendsapplyAction()
    {
        // http://webcms.didv.cn/member/index/friendsapply
        // 更新已读
        $this->modelMsgCount->clearFriendMsgCount($_SESSION['member_id']);
    }

    /**
     * 评论回复消息页面
     */
    public function replycommentsmsgAction()
    {
        // http://webcms.didv.cn/member/index/replycommentsmsg
        // 更新已读
        $this->modelMsgCount->clearReplyMsgCount($_SESSION['member_id']);
    }

    /**
     * 私信页面
     */
    public function userprivmsgAction()
    {
        // http://webcms.didv.cn/member/index/userprivmsg
        // 更新已读
        $this->modelMsgCount->clearPrivateMsgCount($_SESSION['member_id']);
    }

    /**
     * 私信2页面
     */
    public function userprivmsgdetailAction()
    {
        // http://webcms.didv.cn/member/index/userprivmsgdetail?senderUserID=xxx
        $user_id = $this->get('senderUserID', '');
        $this->assign('user_id', $user_id);
        
        if (! empty($user_id)) {
            // 获取用户信息
            $toMsgUserInfo = $this->modelMember->getInfoById($user_id);
            $this->assign('userName', $this->modelMember->getRegisterName($toMsgUserInfo, true));
            
            $msgStatisticsInfo = $this->modelMsgStatistics->getInfoBy2UserId($_SESSION['member_id'], $user_id);
            $this->assign('msg_num', $msgStatisticsInfo['msg_num']);
            // 更新已读
            $this->modelMsgStatistics->setToRead($msgStatisticsInfo['_id']);
        }
    }
}

