<?php
namespace Webcms\Goods\Controllers;

class IndexController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }
}

