<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="Schedule管理"})
 *
 * @name Schedule管理
 */
class ScheduleController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'Task Schedule';
    }

    protected function getModel()
    {
        return array();
    }

    /**
     * @title({name="显示列表页面"})
     *
     * @name 显示列表页面
     */
    public function listAction()
    {
        try {
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="执行"})
     *
     * @name 执行
     */
    public function runAction()
    {
        // http://www.myapplicationmodule.com/admin/system/schedule/run?id=xxx
        try {
            $this->view->disable();
            $id = $this->request->get('id', array(
                'trim',
                'string'
            ), '');
            $ret = '{"status":true,"message":"success","data":"执行结果"}';
            $ret = \json_decode($ret, true);
            $this->makeJsonResult('', '执行成功', $ret);
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
