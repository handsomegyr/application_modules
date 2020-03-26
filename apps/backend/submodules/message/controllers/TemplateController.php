<?php
namespace App\Backend\Submodules\Message\Controllers;

use App\Backend\Submodules\Message\Models\Template;

/**
 * @title({name="消息模版管理"})
 *
 * @name 消息模版管理
 */
class TemplateController extends \App\Backend\Controllers\FormController
{

    private $modelTemplate;

    public function initialize()
    {
        $this->modelTemplate = new Template();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        
        $schemas['code'] = array(
            'name' => '模板代码',
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
        $schemas['name'] = array(
            'name' => '模板名称',
            'data' => array(
                'type' => 'string',
                'length' => 100
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
        $schemas['title'] = array(
            'name' => '模板标题',
            'data' => array(
                'type' => 'string',
                'length' => 100
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
        $schemas['content'] = array(
            'name' => '模板内容',
            'data' => array(
                'type' => 'html',
                'length' => 1000
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'ueditor',
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
        return '消息模版';
    }

    protected function getModel()
    {
        return $this->modelTemplate;
    }
}