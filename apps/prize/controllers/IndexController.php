<?php
namespace App\Prize\Controllers;

class IndexController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    public function indexAction()
    {
        die('Prize');
    }
}

