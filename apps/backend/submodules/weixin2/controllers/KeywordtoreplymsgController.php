<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToReplyMsg;
use App\Backend\Submodules\Weixin2\Models\Keyword\Keyword;
use App\Backend\Submodules\Weixin2\Models\ReplyMsg\ReplyMsg;

/**
 * @title({name="关键词和回复消息对应设定"})
 *
 * @name 关键词和回复消息对应设定
 */
class KeywordtoreplymsgController extends \App\Backend\Controllers\FormController
{
    private $modelKeywordToReplyMsg;

    private $modelKeyword;
    private $modelReplyMsg;

    public function initialize()
    {
        $this->modelKeywordToReplyMsg = new KeywordToReplyMsg();
        $this->modelKeyword = new Keyword();
        $this->modelReplyMsg = new ReplyMsg();

        $this->keywordItems = $this->modelKeyword->getAll();
        $this->replyMsgItems = $this->modelReplyMsg->getAllByType("", "_id");
        parent::initialize();
    }
    private $keywordItems = null;
    private $replyMsgItems = null;

    protected function getSchemas2($schemas)
    {        $schemas['keyword_id'] = array(
            'name' => '关键词ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->keywordItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->keywordItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->keywordItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['reply_msg_id'] = array(
            'name' => '回复消息ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->replyMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->replyMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->replyMsgItems
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
