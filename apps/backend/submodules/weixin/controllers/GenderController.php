<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\Gender;

/**
 * @title({name="微信性别管理"})
 *
 * @name 微信性别管理
 */
class GenderController extends \App\Backend\Controllers\FormController
{

    private $modelGender;

    public function initialize()
    {
        $this->modelGender = new Gender();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['key'] = array(
            'name' => '名称',
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
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        return '微信性别';
    }

    protected function getModel()
    {
        return $this->modelGender;
    }
}