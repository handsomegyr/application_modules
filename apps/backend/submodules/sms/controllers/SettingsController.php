<?php
namespace App\Backend\Submodules\Sms\Controllers;

use App\Backend\Models\Sms\Settings;

/**
 * @title({name="短信设置管理"})
 *
 * @name 短信设置管理
 */
class SettingsController extends \App\Backend\Controllers\FormController
{

    private $modelSettings;

    public function initialize()
    {
        $this->modelSettings = new Settings();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['apiname'] = array(
            'name' => '短信接口',
            'data' => array(
                'type' => 'string',
                'length' => 30
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['apikey'] = array(
            'name' => '用户唯一标识',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '短信设置';
    }

    protected function getModel()
    {
        return $this->modelSettings;
    }
}