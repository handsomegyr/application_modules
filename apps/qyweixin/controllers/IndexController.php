<?php

namespace App\Qyweixin\Controllers;

class IndexController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    public function indexAction()
    {
        // http://www.applicationmodule.com/weixin/index/index
        die('index');
    }
}
