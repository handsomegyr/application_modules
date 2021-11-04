<?php

namespace App\Backend\Submodules\Tag\Controllers;

use App\Backend\Submodules\Tag\Models\TagToEntity;
use App\Backend\Submodules\Tag\Models\Tag;

/**
 * @title({name="给实体打标签"})
 *
 * @name 给实体打标签
 */
class TagtoentityController extends \App\Backend\Controllers\FormController
{
    private $modelTagToEntity;
    private $modelTag;

    public function initialize()
    {
        $this->modelTagToEntity = new TagToEntity();
        $this->modelTag = new Tag();

        $this->tagList = $this->modelTag->getAll();
        parent::initialize();
    }
    private $tagList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['entity_type'] = array(
            'name' => '实体类型',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
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
        $schemas['entity_id'] = array(
            'name' => '实体ID',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
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
        $schemas['entity_no'] = array(
            'name' => '实体编号',
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
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
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
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '给实体打标签';
    }

    protected function getModel()
    {
        return $this->modelTagToEntity;
    }
}
