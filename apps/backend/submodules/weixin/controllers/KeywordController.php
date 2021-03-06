<?php

namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\ReplyType;
use App\Backend\Submodules\Weixin\Models\Reply;
use App\Backend\Submodules\Weixin\Models\Keyword;

/**
 * @title({name="微信关键词管理"})
 *
 * @name 微信关键词管理
 */
class KeywordController extends \App\Backend\Controllers\FormController
{

    private $modelReplyType;

    private $modelReply;

    private $modelKeyword;

    public function initialize()
    {
        $this->modelReplyType = new ReplyType();
        $this->modelReply = new Reply();
        $this->modelKeyword = new Keyword();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['keyword'] = array(
            'name' => '关键词',
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

        $schemas['fuzzy'] = array(
            'name' => '模糊匹配?',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->trueOrFalseDatas,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['reply_type'] = array(
            'name' => '回复类型',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelReplyType->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                // 'list_data_name' => 'reply_type_name',
                'items' => function () {
                    return $this->modelReplyType->getAll();
                }
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['reply_ids'] = array(
            'name' => '回复内容',
            'data' => array(
                'type' => 'array',
                'length' => '1024',
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelReply->getAll();
                },
                'select' => array(
                    'multiple' => true
                )
            ),
            'list' => array(
                'is_show' => true,
                // 'list_data_name' => 'reply_ids_show',
                'items' => function () {
                    return $this->modelReply->getAll();
                },
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['priority'] = array(
            'name' => '优先级',
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

        $schemas['times'] = array(
            'name' => '命中次数',
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
        return '微信关键词';
    }

    protected function getModel()
    {
        return $this->modelKeyword;
    }
}
