<?php

namespace App\Backend\Submodules\System\Controllers;

/**
 * @title({name="Api测试管理"})
 *
 * @name Api测试管理
 */
class ApitestController extends \App\Backend\Controllers\FormController
{

    public function initialize()
    {
        parent::initialize();
    }

    protected function getName()
    {
        return 'Api测试';
    }

    protected function getModel()
    {
        return array();
    }

    /**
     * @title({name="显示列表页面"})
     *
     * @name 显示列表页面
     */
    public function listAction()
    {
        try {
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @title({name="发送请求"})
     *
     * @name 发送请求
     */
    public function sendAction()
    {
        // http://www.myapplicationmodule.com/admin/system/apitest/send?&uri=api/users&method=GET&user=
        try {
            $this->view->disable();
            $uri = $this->request->get('uri', array(
                'trim',
                'string'
            ), '');
            $ret = '{"status":true,"message":"success","data":{"headers":"{\n    \"cache-control\": [\n        \"no-cache, private\"\n    ],\n    \"date\": [\n        \"Sat, 08 May 2021 08:51:51 GMT\"\n    ],\n    \"content-type\": [\n        \"application\\\/json\"\n    ],\n    \"set-cookie\": [\n        \"XSRF-TOKEN=eyJpdiI6Im8wWEtyUU4zWE11ejEzYjJSQlk1VVE9PSIsInZhbHVlIjoiUWtyRnlkeVhlN1hQKzFMaVJ1VUU3RUtKeVwvdEU3bEFLNGVFYXg0dE1YYVQrYURyTkdLd3FaNFlDMXB4ekNwZEUiLCJtYWMiOiI1OTM4ZDAzYWI0NWY2MjY3M2UzZmIyMTcwNjMzZjIxNWNmNGU3MDBiNjhjM2VlMmRkNGQ4ZjY3MzBhMDIzN2I2In0%3D; expires=Sat, 08-May-2021 10:51:51 GMT; Max-Age=7200; path=\\\/\",\n        \"laravel_session=eyJpdiI6Im9UMEZpb3hUcG9mdE5oc2RPVFh3cUE9PSIsInZhbHVlIjoibDczMlJ1SnRIZklcL3JEOFJDY3JTb0JRejVcL295K2dmb1hsMlZFUVZtbUxlUmg2ZXRUM1FYblRyeHJzYytnNUdkIiwibWFjIjoiMDVkYjgwZDMyMjJiZjUzMGZiMTQ1M2M4NDJhZjllMjZhZjYyZWQ5NjgxNzljOWUwMDRhY2I2ZDhjODI2NjdhZSJ9; expires=Sat, 08-May-2021 10:51:51 GMT; Max-Age=7200; path=\\\/; httponly\"\n    ]\n}","cookies":"[\n    {},\n    {}\n]","content":"[]","language":"json","status":{"code":200,"text":"OK"}}}';
            $ret = \json_decode($ret, true);
            $this->makeJsonResult('', '发送请求成功', $ret);
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
