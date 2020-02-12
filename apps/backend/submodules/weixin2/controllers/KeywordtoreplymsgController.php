<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToReplyMsg;

/**
 * @title({name="关键词和回复消息对应设定"})
 *
 * @name 关键词和回复消息对应设定
 */
class KeywordtoreplymsgController extends \App\Backend\Controllers\FormController
{
    private $modelKeywordToReplyMsg;

    public function initialize()
    {
        $this->modelKeywordToReplyMsg = new KeywordToReplyMsg();
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
        $schemas['reply_msg_id'] = array(
            'name' => '回复消息ID',
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
        return '关键词和回复消息对应设定';
    }

    protected function getModel()
    {
        return $this->modelKeywordToReplyMsg;
    }
}
