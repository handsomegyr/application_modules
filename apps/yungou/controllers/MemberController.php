<?php
namespace Webcms\Yungou\Controllers;

/**
 * 云购
 *
 * @author Kan
 *        
 */
class MemberController extends ControllerBase
{

    private $modelMemberVisitor = null;

    private $modelPointsUser = null;

    public function initialize()
    {
        parent::initialize();
        $this->modelMemberVisitor = new \Webcms\Member\Models\Visitor();
        $this->modelPointsUser = new \Webcms\Points\Models\User();
    }

    /**
     * 用户个人主页
     */
    public function indexAction()
    {
        $id = $this->get('id', '');
        if (empty($id)) {
            $this->goToError();
            return;
        }
        $memberInfo = $this->modelMember->getInfoById($id);
        $this->assign('memberInfo', $memberInfo);
        
        $hometown = array();
        if (! empty($memberInfo['hometown'])) {
            $hometown = explode('|', $memberInfo['hometown']);
        }
        $this->assign('hometown', $hometown);
        
        // {"msgSet":1,"areaSet":0,"searchSet":0,"buySet":0,"rafSet":0,"postSet":0,"buyShowNum":10,"rafShowNum":0,"postShowNum":10}
        
        $_SESSION['browser_time'] = getCurrentTime();
        // 记录访客
        if (! empty($_SESSION['member_id'])) {
            if ($_SESSION['member_id'] != $id) {
                $this->modelMemberVisitor->visit($id, $_SESSION['member_id']);
            }
        }
        
        // 私信 1:仅限好友 2 禁止
        $isCanSendPrivMsg = $this->modelMember->isCanSendPrivMsg($memberInfo, $_SESSION['member_id']);
        $this->assign('isCanSendPrivMsg', $isCanSendPrivMsg);
        
        // 地理位置 0:允许 1:禁止
        $isCanSeeAreaInfo = $this->modelMember->isCanSeeAreaInfo($memberInfo);
        $this->assign('isCanSeeAreaInfo', $isCanSeeAreaInfo);
    }
}

