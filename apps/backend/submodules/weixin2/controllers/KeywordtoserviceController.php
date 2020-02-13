<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToService;
use App\Backend\Submodules\Weixin2\Models\Keyword\Keyword;
use App\Backend\Submodules\Weixin2\Models\Service;

/**
 * @title({name="关键词和服务对应设定"})
 *
 * @name 关键词和服务对应设定
 */
class KeywordtoserviceController extends \App\Backend\Controllers\FormController
{
    private $modelKeywordToService;
    private $modelKeyword;
    private $modelService;
    public function initialize()
    {
        $this->modelKeywordToService = new KeywordToService();
        $this->modelKeyword = new Keyword();
        $this->modelService = new Service();

        $this->keywordItems = $this->modelKeyword->getAll();
        $this->serviceItems = $this->modelService->getAll;
        parent::initialize();
    }
    private $keywordItems = null;
    private $serviceItems = null;

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
        $schemas['service_id'] = array(
            'name' => '服务ID',
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
                'items' => $this->serviceItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->serviceItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->serviceItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '关键词和服务对应设定';
    }

    protected function getModel()
    {
        return $this->modelKeywordToService;
    }
}
