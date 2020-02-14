<?php

namespace App\Backend\Submodules\Cronjob\Controllers;

use App\Backend\Submodules\Cronjob\Models\Inspire;

/**
 * @title({name="运行状况"})
 *
 * @name 运行状况
 */
class InspireController extends \App\Backend\Controllers\FormController
{
    private $modelInspire;

    public function initialize()
    {
        $this->modelInspire = new Inspire();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['content'] = array(
            'name' => '文本',
            'data' => array(
                'type' => 'varchar',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '运行状况';
    }

    protected function getModel()
    {
        return $this->modelInspire;
    }
}
