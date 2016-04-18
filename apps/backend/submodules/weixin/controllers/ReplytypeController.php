<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\ReplyType;

/**
 * @title({name="微信回复类型管理"})
 *
 * @name 微信回复类型管理
 */
class ReplyTypeController extends \App\Backend\Controllers\FormController
{

    private $modelReplyType;

    public function initialize()
    {
        $this->modelReplyType = new ReplyType();
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
        return '微信回复类型';
    }

    protected function getModel()
    {
        return $this->modelReplyType;
    }
}