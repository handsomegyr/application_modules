<?php

namespace App\Service\Controllers;

class IpController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    public function convertAction()
    {
        try {
            // http://www.applicationmodule.com/service/ip/convert?ip=124.113.229.91
            $ip = $this->get('ip', '');
            $address = convertIp($ip);
            echo ($this->result("OK", $address));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}
