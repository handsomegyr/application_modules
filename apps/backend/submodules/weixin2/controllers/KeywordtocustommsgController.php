<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToCustomMsg;
use App\Backend\Submodules\Weixin2\Models\Keyword\Keyword;
use App\Backend\Submodules\Weixin2\Models\CustomMsg\CustomMsg;

/**
 * @title({name="关键词和客服消息对应设定"})
 *
 * @name 关键词和客服消息对应设定
 */
class KeywordtocustommsgController extends \App\Backend\Controllers\FormController
{
    private $modelKeywordToCustomMsg;
    private $modelKeyword;
    private $modelCustomMsg;
    public function initialize()
    {
        $this->modelKeywordToCustomMsg = new KeywordToCustomMsg();
        $this->modelKeyword = new Keyword();
        $this->modelCustomMsg = new CustomMsg();

        $this->keywordItems = $this->modelKeyword->getAll();
        $this->customMsgItems = $this->modelCustomMsg->getAllByType("", "_id");
        parent::initialize();
    }
    private $keywordItems = null;
    private $customMsgItems = null;

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['keyword_id'] = array(
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
        $schemas['custom_msg_id'] = array(
            'name' => '客服消息ID',
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
                'items' => $this->customMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->customMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->customMsgItems
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
