<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\NotKeyword;

/**
 * @title({name="微信非关键词管理"})
 *
 * @name 微信非关键词管理
 */
class NotKeywordController extends \App\Backend\Controllers\FormController
{

    private $modelNotKeyword;

    public function initialize()
    {
        $this->modelNotKeyword = new NotKeyword();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['msg'] = array(
            'name' => '信息',
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
        
        $schemas['times'] = array(
            'name' => '次数',
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
        return '微信非关键词';
    }

    protected function getModel()
    {
        return $this->modelNotKeyword;
    }
}