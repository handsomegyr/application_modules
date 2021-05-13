<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="Tabbed Form示例"})
 *
 * @name Tabbed Form示例
 */
class TabbedformController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'Tabbed Form';
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
