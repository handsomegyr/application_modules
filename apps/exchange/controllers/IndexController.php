<?php
namespace Webcms\Exchange\Controllers;

class IndexController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    public function indexAction()
    {
        die('Exchange');
    }
}

