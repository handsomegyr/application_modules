<?php

namespace App\Service\Controllers;

class TimeController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 提供生成时间的服务
     */
    public function servertimeAction()
    {
        // http://www.jizigou.com/service/time/servertime?time=2015-12-13 18:29:40
        // $time = $this->get('time', '');
        try {
            $currentTime = date('Y-m-d H:i:s');
            echo $this->result('OK', $currentTime);
            return true;
        } catch (\Exception $e) {
            // 记录错误
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }
}
