<?php
namespace App\Backend\Submodules\Weixinredpack\Controllers;

use App\Backend\Submodules\Weixinredpack\Models\Redpack;

/**
 * @title({name="红包管理"})
 *
 * @name 红包管理
 */
class RedpackController extends \App\Backend\Controllers\FormController
{

    private $modelRedpack;

    public function initialize()
    {
        $this->modelRedpack = new Redpack();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['code'] = array(
            'name' => '红包代码',
            'data' => array(
                'type' => 'string',
                'length' => 25
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
            'name' => '红包名',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
        $schemas['desc'] = array(
            'name' => '说明',
            'data' => array(
                'type' => 'html',
                'length' => 1000
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'ueditor',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '红包';
    }

    protected function getModel()
    {
        return $this->modelRedpack;
    }
}