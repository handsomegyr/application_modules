<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="PHP Editor示例"})
 *
 * @name PHP Editor示例
 */
class PhpeditorController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'PHP Editor';
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
