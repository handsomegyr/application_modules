<?php
namespace App\Backend\Submodules\System\Controllers;

use App\Backend\Submodules\System\Models\Source;

/**
 * @title({name="访问来源管理"})
 *
 * @name 访问来源管理
 */
class SourceController extends \App\Backend\Controllers\FormController
{

    private $modelSource;

    public function initialize()
    {
        $this->modelSource = new Source();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['key'] = array(
            'name' => '来源',
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
        
        $schemas['value'] = array(
            'name' => '值',
            'data' => array(
                'type' => 'string',
                'length' => '20'
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
        return '访问来源';
    }

    protected function getModel()
    {
        return $this->modelSource;
    }
}