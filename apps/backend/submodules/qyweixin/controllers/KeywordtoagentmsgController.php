<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\Keyword\KeywordToAgentMsg;
use App\Backend\Submodules\Qyweixin\Models\Keyword\Keyword;
use App\Backend\Submodules\Qyweixin\Models\AgentMsg\AgentMsg;

/**
 * @title({name="关键词和应用消息对应设定"})
 *
 * @name 关键词和应用消息对应设定
 */
class KeywordtoagentmsgController extends \App\Backend\Controllers\FormController
{
    private $modelKeywordToAgentMsg;
    private $modelKeyword;
    private $modelAgentMsg;
    public function initialize()
    {
        $this->modelKeywordToAgentMsg = new KeywordToAgentMsg();
        $this->modelKeyword = new Keyword();
        $this->modelAgentMsg = new AgentMsg();

        $this->keywordItems = $this->modelKeyword->getAll('agent_msg_type');
        $this->agentMsgItems = $this->modelAgentMsg->getAllByType("", "_id");
        parent::initialize();
    }
    private $keywordItems = null;
    private $agentMsgItems = null;

    protected function getSchemas2($schemas)
    {
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
        $schemas['agent_msg_id'] = array(
            'name' => '应用消息ID',
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
                'items' => $this->agentMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->agentMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->agentMsgItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '关键词和应用消息对应设定';
    }

    protected function getModel()
    {
        return $this->modelKeywordToAgentMsg;
    }
}
