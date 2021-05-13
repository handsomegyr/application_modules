<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="Clike Editor示例"})
 *
 * @name Clike Editor示例
 */
class ClikeeditorController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'Clike Editor';
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
