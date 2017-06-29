<?php
namespace App\Install\Controllers;

class ControllerBase extends \App\Common\Controllers\ControllerBase
{

    protected function initialize()
    {
        parent::initialize();
        // $this->view->setVar("resourceUrl", "/install/");
        $this->view->setVar("resourceUrl", "/backend/metronic.bootstrap/");
    }
}
