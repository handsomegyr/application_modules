<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Language;

/**
 * @title({name="语言"})
 *
 * @name 语言
 */
class LanguageController extends \App\Backend\Controllers\FormController
{
    private $modelLanguage;

    public function initialize()
    {
        $this->modelLanguage = new Language();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas['name'] = array(
            'name' => '语言名',
            'data' => array(
                'type' => 'string',
                'length' => 30,
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
        $schemas['value'] = array(
            'name' => '语言值',
            'data' => array(
                'type' => 'string',
                'length' => 10,
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
        return '语言';
    }

    protected function getModel()
    {
        return $this->modelLanguage;
    }
}
