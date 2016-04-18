<?php
namespace App\Yungou\Controllers;
use Phalcon\Mvc\View;

class ControllerBase extends \App\Common\Controllers\ControllerBase
{

    protected $modelMember = null;

    protected function initialize()
    {
        parent::initialize();
        $this->view->setVar("resourceUrl", "/yungou/");
        $this->modelMember = new \App\Member\Models\Member();
        if (! empty($_SESSION['member_id'])) {
            $memberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            $this->assign('memberInfo', $memberInfo);
        }
    }
    
    public function goToError()
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->pick("error/error");
    }
}
