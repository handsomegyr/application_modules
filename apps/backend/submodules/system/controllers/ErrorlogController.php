<?php
namespace App\Backend\Submodules\System\Controllers;

use App\Backend\Submodules\System\Models\ErrorLog;

/**
 * @title({name="错误信息管理"})
 *
 * @name 错误信息管理
 */
class ErrorlogController extends \App\Backend\Controllers\FormController
{

    private $modelErrorLog;

    public function initialize()
    {
        $this->modelErrorLog = new ErrorLog();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['error_code'] = array(
            'name' => '错误代号',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => true
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
        
        $schemas['error_message'] = array(
            'name' => '错误内容',
            'data' => array(
                'type' => 'string',
                'length' => '200'
            ),
            'validation' => array(
                'required' => true
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
        return '错误信息';
    }

    protected function getModel()
    {
        return $this->modelErrorLog;
    }
}