<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="Multiple step form示例"})
 *
 * @name Multiple step form示例
 */
class MultiplestepformController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'Multiple step form';
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
}
