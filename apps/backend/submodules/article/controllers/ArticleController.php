<?php
namespace App\Backend\Submodules\Article\Controllers;

use App\Backend\Submodules\Article\Models\Article;
use App\Backend\Submodules\Article\Models\Category;

/**
 * @title({name="文章管理"})
 *
 * @name 文章管理
 */
class ArticleController extends \App\Backend\Controllers\FormController
{

    private $modelCategory;

    private $modelArticle;

    public function initialize()
    {
        $this->modelArticle = new Article();
        
        $this->modelCategory = new Category();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        
        $schemas['category_id'] = array(
            'name' => '所属分类',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelCategory->getList4Tree();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'category_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['title'] = array(
            'name' => '主题',
            'data' => array(
                'type' => 'string',
                'length' => 100
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
        
        $schemas['content'] = array(
            'name' => '内容',
            'data' => array(
                'type' => 'html',
                'length' => 1000
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'ueditor',
                //'input_type' => 'ckeditor',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['url'] = array(
            'name' => '链接',
            'data' => array(
                'type' => 'string',
                'length' => 100
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['article_time'] = array(
            'name' => '发布时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'datetimepicker',
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['is_show'] = array(
            'name' => '是否显示',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => 1
            ),
            // 'ajax' => 'toggleisshow'
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['show_order'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
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
        return '文章';
    }

    protected function getModel()
    {
        return $this->modelArticle;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $categoryList = array(); // $this->modelCategory->getAll();
        foreach ($list['data'] as &$item) {
            $item['category_name'] = isset($categoryList[$item['category_id']]) ? $categoryList[$item['category_id']] : '';
            $item['article_time'] = date("Y-m-d H:i:s", $item['article_time']->sec);
            $item['title'] = $item['title'] . '&nbsp&nbsp<a href="javascript:;" class="btn yellow icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要将本地文章上传到elasticsearch吗？\', \'elastic\')" class="halflings-icon user white"><i></i> elasticsearch</a>';
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
        // http://www.applicationmodule.com/admin/article/article/elastic?id=xxx
        try {
            $this->view->disable();
            
            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", - 1);
            }
            $articleInfo = $this->modelArticle->getInfoById($id);
            if (empty($articleInfo)) {
                throw new \Exception("id不正确", - 2);
            }
            
            $client = $this->getDI()->get('elasticsearch');
            
            // 删除
            $params = [
                'index' => 'application_modules',
                'type' => 'article',
                'id' => $id
            ];
            $response = $client->delete($params);
            
            // 新建
            $params = [
                'index' => 'application_modules',
                'type' => 'article',
                'id' => $id,
                'body' => [
                    'title' => $categoryInfo['name'],
                    'content' => $categoryInfo['name'],
                    'dataFrom' => 'article'
                ]
            ];
            $response = $client->index($params);
            
            // 获取
            $params = [
                'index' => 'application_modules',
                'type' => 'article',
                'id' => $id
            ];
            
            $response = $client->get($params);
            
            $this->makeJsonResult($response);
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}