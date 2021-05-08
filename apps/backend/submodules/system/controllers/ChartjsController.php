<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="Chartjs管理"})
 *
 * @name Chartjs管理
 */
class ChartjsController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'Chartjs';
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
