<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Event\Event;
use App\Backend\Submodules\Weixin2\Models\Event\Category;

/**
 * @title({name="事件"})
 *
 * @name 事件
 */
class EventController extends \App\Backend\Controllers\FormController
{
    private $modelEvent;
    private $modelEventCategory;

    public function initialize()
    {
        $this->modelEvent = new Event();
        $this->modelEventCategory = new Category();
        $this->eventCategoryItems = $this->modelEventCategory->getAll();
        parent::initialize();
    }

    private $eventCategoryItems = null;

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '事件名',
            'data' => array(
                'type' => 'string',
                'length' => 100,
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
        $schemas['value'] = array(
            'name' => '事件值',
            'data' => array(
                'type' => 'string',
                'length' => 50,
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

        $schemas['category'] = array(
            'name' => '事件分类',
            'data' => array(
                'type' => 'string',
                'length' => 20,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->eventCategoryItems

            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->eventCategoryItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->eventCategoryItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '事件';
    }

    protected function getModel()
    {
        return $this->modelEvent;
    }
}
