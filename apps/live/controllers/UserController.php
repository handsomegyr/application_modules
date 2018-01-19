<?php
namespace App\Live\Controllers;

class UserController extends ControllerBase
{

    private $modelRoom = null;

    private $modelUser = null;

    private $modelAuchor = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        $this->modelRoom = new \App\Live\Models\Room();
        $this->modelUser = new \App\Live\Models\User();
    }

    /**
     * 房间登陆的接口
     */
    public function loginAction()
    {
        // http://www.applicationmodule.com/live/user/login?room_id=xxxxx&openid=xxx&nickname=xxx&headimgurl=xxx&authtype=anonymous,weixin,weibo&source=anonymous,weixin&channel=anonymous,weixin
        die('loginAction');
    }
}