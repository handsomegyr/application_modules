<?php

namespace App\Campaign\Controllers;

/**
 * 检索事例
 *
 * @author Administrator
 *        
 */
class SearchController extends ControllerBase
{

    protected function doCampaignInitialize()
    {
        $this->view->disable();
    }

    /**
     * 测试的接口
     */
    public function testAction()
    {
        // http://www.myapplicationmodule.com/campaign/search/elastic?keyword=xxxx
        try {
            $keyword = $this->get('keyword', '');

            $client = $this->getDI()->get('elasticsearch');

            // $deleteParams = [
            // 'index' => 'application_modules'
            // ];
            // $response = $client->indices()->delete($deleteParams);

            // $params = [
            // 'index' => 'application_modules',
            // 'body' => [
            // 'settings' => [
            // 'number_of_shards' => 2,
            // 'number_of_replicas' => 0
            // ]
            // ]
            // ];
            // $response = $client->indices()->create($params);

            // 检索
            $params = [
                'index' => 'application_modules',
                'body' => [
                    'query' => [
                        'match' => [
                            'title' => $keyword
                        ]
                    ]
                ]
            ];
            $response = $client->search($params);

            echo $this->result("OK", $response);
            return true;
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 输入关键词进行检索的接口
     */
    public function elasticAction()
    {
        // http://www.myapplicationmodule.com/campaign/search/elastic?keyword=xxxx
        try {
            $keyword = $this->get('keyword', '');

            $client = $this->getDI()->get('elasticsearch');

            // 检索
            $params = [
                'index' => 'application_modules',
                'body' => [
                    'query' => [
                        'match' => [
                            'title' => $keyword
                        ]
                    ]
                ]
            ];
            $response = $client->search($params);

            echo $this->result("OK", $response);
            return true;
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }
}
