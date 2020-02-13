<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToTemplateMsg;
use App\Backend\Submodules\Weixin2\Models\Keyword\Keyword;
use App\Backend\Submodules\Weixin2\Models\TemplateMsg\TemplateMsg;

/**
 * @title({name="关键词和模板消息对应设定"})
 *
 * @name 关键词和模板消息对应设定
 */
class KeywordtotemplatemsgController extends \App\Backend\Controllers\FormController
{
    private $modelKeywordToTemplateMsg;
    private $modelKeyword;
    private $modelTemplateMsg;
    public function initialize()
    {
        $this->modelKeywordToTemplateMsg = new KeywordToTemplateMsg();
        $this->modelKeyword = new Keyword();
        $this->modelTemplateMsg = new TemplateMsg();

        $this->keywordItems = $this->modelKeyword->getAll();
        $this->templateMsgItems = $this->modelTemplateMsg->getAll();
        parent::initialize();
    }
    private $keywordItems = null;
    private $templateMsgItems = null;

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
        $schemas['template_msg_id'] = array(
            'name' => '模板消息ID',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->templateMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->templateMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->templateMsgItems
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
