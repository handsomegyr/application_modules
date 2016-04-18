<?php
namespace App\Goods\Controllers;

class IndexController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }
}

