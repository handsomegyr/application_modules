<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="Json Editor示例"})
 *
 * @name Json Editor示例
 */
class JsoneditorController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'Json Editor';
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
