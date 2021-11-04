<?php

namespace App\Lexiangla\Controllers;

class IndexController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    public function indexAction()
    {
        // http://www.myapplicationmodule.com/lexiangla/index/index
        die('Lexiangla');
    }
}
