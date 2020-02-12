<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToCustomMsg;

/**
 * @title({name="关键词和客服消息对应设定"})
 *
 * @name 关键词和客服消息对应设定
 */
class KeywordtocustommsgController extends \App\Backend\Controllers\FormController
{
    private $modelKeywordToCustomMsg;

    public function initialize()
    {
        $this->modelKeywordToCustomMsg = new KeywordToCustomMsg();
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
        $schemas['custom_msg_id'] = array(
            'name' => '客服消息ID',
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
        return '关键词和客服消息对应设定';
    }

    protected function getModel()
    {
        return $this->modelKeywordToCustomMsg;
    }
}
