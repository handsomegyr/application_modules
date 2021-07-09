<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Miniprogram\Urlscheme;

/**
 * @title({name="小程序scheme码"})
 *
 * @name 小程序scheme码
 */
class MiniprogramurlschemeController extends BaseController
{
    private $modelUrlscheme;
    public function initialize()
    {
        $this->modelUrlscheme = new Urlscheme();
        parent::initialize();
    }

    protected function isCanCreateUrlscheme($model)
    {
        return !empty($model['authorizer_appid']);
    }

    protected function getFormTools2($tools)
    {
        $tools['createurlscheme'] = array(
            'title' => '生成小程序scheme码',
            'action' => 'createurlscheme',
            'is_show' => function ($row) {
                if ($this->isCanCreateUrlscheme($row)) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="生成小程序scheme码"})
     *
     * @name 生成小程序scheme码
     */
    public function createurlschemeAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/miniprogramurlscheme/createurlscheme?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelUrlscheme->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->createMiniappUrlScheme($id);

            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['component_appid'] = array(
            'name' => '第三方平台应用ID',
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
                'items' => $this->componentItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->componentItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['authorizer_appid'] = array(
            'name' => '授权方应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->authorizerItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['name'] = array(
            'name' => '名称',
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
        $schemas['path'] = array(
            'name' => '小程序页面路径',
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
                'items' => '',
                'help' => '通过 scheme 码进入的小程序页面路径，必须是已经发布的小程序存在的页面，不可携带 query。path 为空时会跳转小程序主页。例如:/pages/mall/home',
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
        $schemas['query_content'] = array(
            'name' => '进入小程序时的query',
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
                'items' => '',
                'help' => "通过 scheme 码进入小程序时的 query，最大1024个字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~,例如:scene=xxx",
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
        $isExpireOptions = array(
            0 => '永久有效',
            1 => '到期失效',
        );
        $expireTypeOptions = array(
            '0' => '失效时间',
            '1' => '失效间隔天数',
        );
        $schemas['is_expire'] = array(
            'name' => '小程序 scheme 码类型',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $isExpireOptions
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $isExpireOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $isExpireOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['expire_type'] = array(
            'name' => '小程序 scheme 码失效类型',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $expireTypeOptions
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $expireTypeOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $expireTypeOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['expire_time'] = array(
            'name' => 'scheme 码的失效时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true,
                'items' => '',
                'help' => '到期失效的 scheme 码的失效时间，为 Unix 时间戳。生成的到期失效 scheme 码 在该时间前有效。最长有效期为1年。expire_type 为 失效时间 必填',
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
        $schemas['expire_interval'] = array(
            'name' => 'scheme 码的失效间隔天数',
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
                'items' => '',
                'help' => '到期失效的 scheme 码的失效间隔天数。生成的到期失效scheme 码在该间隔时间到达前有效。最长间隔天数为365天。expire_type 为 失效间隔天数 必填',
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
        $schemas['openlink'] = array(
            'name' => '小程序scheme 码',
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
                'items' => '',
                'readonly' => true
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
        return '小程序scheme码';
    }

    protected function getModel()
    {
        return $this->modelUrlscheme;
    }
}
