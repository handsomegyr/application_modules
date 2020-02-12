<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToTemplateMsg;

/**
 * @title({name="关键词和模板消息对应设定"})
 *
 * @name 关键词和模板消息对应设定
 */
class KeywordtotemplatemsgController extends \App\Backend\Controllers\FormController
{
    private $modelKeywordToTemplateMsg;

    public function initialize()
    {
        $this->modelKeywordToTemplateMsg = new KeywordToTemplateMsg();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['keyword_id'] = array(
            'name' => '关键词ID',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
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
        $schemas['template_msg_id'] = array(
            'name' => '模板消息ID',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
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
        return '关键词和模板消息对应设定';
    }

    protected function getModel()
    {
        return $this->modelKeywordToTemplateMsg;
    }
}
