<?php

namespace App\Backend\Submodules\Database\Controllers;

use App\Backend\Submodules\Database\Models\Plugin\Collection\Index;
use App\Backend\Submodules\Database\Models\Plugin;
use App\Backend\Submodules\Database\Models\Plugin\Collection;

/**
 * @title({name="插件表索引管理"})
 *
 * @name 插件表索引管理
 */
class PlugincollectionindexController extends \App\Backend\Controllers\FormController
{
    private $modelIndex;
    private $modelPlugin;
    private $modelCollection;

    public function initialize()
    {
        $this->modelIndex = new Index();
        $this->modelPlugin = new Plugin();
        $this->modelCollection = new Collection();
        $this->pluginList = $this->modelPlugin->getAll();
        $this->collectionList4Plugin = $this->modelCollection->getAll();
        parent::initialize();
    }
    private $pluginList = null;
    private $collectionList4Plugin = null;

    protected function getSchemas2($schemas)
    {
        $schemas['plugin_id'] = array(
            'name' => '所属插件',            
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
                'items' => $this->pluginList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->pluginList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->pluginList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['plugin_collection_id'] = array(
            'name' => '所属插件表',
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
                'items' => $this->collectionList4Plugin
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->collectionList4Plugin
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->collectionList4Plugin
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['indexes'] = array(
            'name' => '索引',
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
        $schemas['options'] = array(
            'name' => '索引配置信息',
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '插件表索引管理';
    }

    protected function getModel()
    {
        return $this->modelIndex;
    }
}
