<?php

namespace App\Backend\Submodules\Member\Controllers;


/**
 * @title({name="会员标签"})
 *
 * @name 会员标签
 */
class TagController extends \App\Backend\Controllers\FormController
{
    // 是否只读
    protected $readonly = false;
    private $modelMemberTag;
    private $modelTag;

    private $tagList = array();

    public function initialize()
    {
        $this->modelTag = new \App\Backend\Submodules\Tag\Models\Tag();
        $this->modelMemberTag = new \App\Backend\Submodules\Member\Models\Tag();

        $this->tagList = $this->modelTag->getAll();

        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['member_id'] = array(
            'name' => '会员ID',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
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
        $schemas['mobile'] = array(
            'name' => '手机号',
            'data' => array(
                'type' => 'string',
                'length' => 20,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
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
        $schemas['openid'] = array(
            'name' => '微信用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 190,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
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
        $schemas['tag_id'] = array(
            'name' => '标签id',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->tagList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->tagList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->tagList
            ),
            'export' => array(
                'is_show' => true,
                'items' => $this->tagList
            )
        );
        $schemas['num'] = array(
            'name' => '浏览次数',
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
        return '会员标签';
    }

    protected function getModel()
    {
        return $this->modelMemberTag;
    }
}
