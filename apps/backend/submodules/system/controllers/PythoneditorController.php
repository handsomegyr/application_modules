<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="Python Editor示例"})
 *
 * @name Python Editor示例
 */
class PythoneditorController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'Python Editor';
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
