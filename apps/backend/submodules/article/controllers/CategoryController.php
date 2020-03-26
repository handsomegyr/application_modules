<?php
namespace App\Backend\Submodules\Article\Controllers;

use App\Backend\Submodules\Article\Models\Category;

/**
 * @title({name="文章分类管理"})
 *
 * @name 文章分类管理
 */
class CategoryController extends \App\Backend\Controllers\FormController
{

    private $modelCategory;

    public function initialize()
    {
        $this->modelCategory = new Category();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;
        
        $schemas['name'] = array(
            'name' => '分类名称',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'show_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['parent_id'] = array(
            'name' => '上级分类',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelCategory->getList4Tree('');
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['code'] = array(
            'name' => '标识码',
            'data' => array(
                'type' => 'string',
                'length' => 20
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
        
        $schemas['sort'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => 1
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
        return '文章分类';
    }

    protected function getModel()
    {
        return $this->modelCategory;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            $item['show_name'] = str_repeat('&nbsp;', $item['level'] * 4) . $item['name'];
            $item['show_name'] = $item['show_name'] . '&nbsp&nbsp<a href="javascript:;" class="btn yellow icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要将本地数据上传到elasticsearch吗？\', \'elastic\')" class="halflings-icon user white"><i></i> elasticsearch</a>';
        }
        return $list;
    }

    /**
     * @title({name="上传elasticsearch"})
     *
     * @name 上传elasticsearch
     */
    public function elasticAction()
    {
        // http://www.applicationmodule.com/admin/article/category/elastic?id=xxx
        try {
            $this->view->disable();
            
            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", - 1);
            }
            $categoryInfo = $this->modelCategory->getInfoById($id);
            if (empty($categoryInfo)) {
                throw new \Exception("id不正确", - 2);
            }
            
            $client = $this->getDI()->get('elasticsearch');
            
            // 删除
            $params = [
                'index' => 'application_modules',
                'type' => 'article_category',
                'id' => $id
            ];
            $response = $client->delete($params);
            
            // 新建
            $params = [
                'index' => 'application_modules',
                'type' => 'article_category',
                'id' => $id,
                'body' => [
                    'title' => $categoryInfo['name'],
                    'content' => $categoryInfo['name'],
                    'dataFrom' => 'article_category'
                ]
            ];
            $response = $client->index($params);
            
            // 获取
            $params = [
                'index' => 'application_modules',
                'type' => 'article_category',
                'id' => $id
            ];
            
            $response = $client->get($params);
            
            $this->makeJsonResult($response);
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}