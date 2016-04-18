<?php
namespace App\Points\Controllers;

/**
 * 积分服务
 *
 * @author Admin
 *        
 */
class IndexController extends ControllerBase
{

    private $modelUser = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelUser = new \App\Points\Models\User();
    }

    public function indexAction()
    {
        // http://webcms.didv.cn/points/index/index
        $member_id = '56468b9c887c22f35e8b4571';
        $member_name = '郭永荣';
        $headimgurl = '';
        $this->modelUser->create(1, $member_id, $member_name, $headimgurl);
        $this->modelUser->create(2, $member_id, $member_name, $headimgurl);
        $this->modelUser->create(3, $member_id, $member_name, $headimgurl);
        die('OK');
    }
}

