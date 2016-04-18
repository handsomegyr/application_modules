<?php
namespace App\Yungou\Controllers;

/**
 * 云购
 *
 * @author Kan
 *        
 */
class HelpController extends ControllerBase
{

    private $modelInvitation = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->setLayout('index');
        $this->modelInvitation = new \App\Invitation\Models\Invitation();
        $this->modelInvitation->setIsExclusive(false);
    }

    /**
     * 福分经验值明细表
     */
    public function userexperienceAction()
    {}

    public function aboutAction()
    {}

    public function agreementAction()
    {}

    public function businessAction()
    {}

    public function contactusAction()
    {}

    public function deliveryfeesAction()
    {}

    public function genuinetwoAction()
    {}
	
	public function genuineAction()
    {}

    public function indexAction()
    {}

    public function jobsAction()
    {}

    public function linkAction()
    {}

    public function prodcheckAction()
    {}

    public function questiondetailAction()
    {}

    public function referauthAction()
    {
        if (! empty($_SESSION['member_id'])) {
            $myInvitationInfo = $this->modelInvitation->getInfoByUserId($_SESSION['member_id'], YUNGOU_ACTIVITY_ID);
            $this->assign('invitationInfo', $myInvitationInfo);
        }
    }

    public function securepaymentAction()
    {}

    public function shipAction()
    {}

    public function shiptwoAction()
    {}

    public function suggestionAction()
    {}

    public function newbieAction()
    {}
}

