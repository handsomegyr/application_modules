<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CodeType;

/**
 * @title({name="code码展示类型管理"})
 *
 * @name code码展示类型管理
 */
class CodetypeController extends \App\Backend\Controllers\FormController
{

    private $modelCodeType;

    public function initialize()
    {
        $this->modelCodeType = new CodeType();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['code_type'] = array(
            'name' => '展示类型',
            'data' => array(
                'type' => 'string',
                'length' => '16'
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
        
        $schemas['name'] = array(
            'name' => '展示名称',
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
        
        return $schemas;
    }

    protected function getName()
    {
        return 'code码展示类型';
    }

    protected function getModel()
    {
        return $this->modelCodeType;
    }
}