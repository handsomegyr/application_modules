<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\Contact\DepartmentUser;

/**
 * @title({name="部门成员"})
 *
 * @name 部门成员
 */
class DepartmentuserController extends BaseController
{
    private $modelDepartmentUser;

    public function initialize()
    {
        $this->modelDepartmentUser = new DepartmentUser();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['getdepartmentusersimplelist'] = array(
            'title' => '获取部门成员',
            'action' => 'getdepartmentusersimplelist',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        $tools['getdepartmentuserdetaillist'] = array(
            'title' => '获取部门成员详情',
            'action' => 'getdepartmentuserdetaillist',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="获取部门成员"})
     *
     * @name 获取部门成员
     */
    public function getdepartmentusersimplelistAction()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/departmentuser/getdepartmentusersimplelist
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取部门成员";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('departmentuser_provider_appid'));
                $authorizer_appid = trim($this->request->get('departmentuser_authorizer_appid'));
                $agent_agentid = trim($this->request->get('departmentuser_agent_agentid'));
                $department_id = trim($this->request->get('department_id'));
                if (empty($provider_appid)) {
                    // return $this->response()->error("第三方服务商应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                if (empty($departmentuser_agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                if (empty($department_id)) {
                }

                $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agent_agentid);
                $res = $weixinopenService->getDepartmentUserSimplelist($department_id);
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取部门成员详情"})
     *
     * @name 获取部门成员详情
     */
    public function getdepartmentuserdetaillistAction()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/departmentuser/getdepartmentuserdetaillist
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取部门成员详情";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('departmentuser_provider_appid'));
                $authorizer_appid = trim($this->request->get('departmentuser_authorizer_appid'));
                $agent_agentid = trim($this->request->get('departmentuser_agent_agentid'));
                $department_id = trim($this->request->get('department_id'));
                if (empty($provider_appid)) {
                    // return $this->response()->error("第三方服务商应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                if (empty($department_id)) {
                }

                $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agent_agentid);
                $res = $weixinopenService->getDepartmentUserDetaillist($department_id);
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['departmentuser_provider_appid'] = array(
            'name' => '第三方服务商应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems,
            ),
        );
        $fields['departmentuser_authorizer_appid'] = array(
            'name' => '授权方应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems,
            ),
        );
        $fields['departmentuser_agent_agentid'] = array(
            'name' => '微信企业应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->agentItems,
            ),
        );

        $fields['department_id'] = array(
            'name' => '部门id',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
            ),
        );

        return $fields;
    }

    protected function getSchemas2($schemas)
    {
        $schemas['provider_appid'] = array(
            'name' => '第三方服务商应用ID',
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
                'items' => $this->providerItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->providerItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->providerItems
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
        $schemas['department_id'] = array(
            'name' => '获取的部门id',
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
        // $schemas['fetch_child'] = array(
        //     'name' => '是否递归获取子部门下面的成员',
        //     'data' => array(
        //         'type' => 'boolean',
        //         'length' => 1,
        //         'defaultValue' => false
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'radio',
        //         'is_show' => true,
        //         'items' => $this->trueOrFalseDatas,
        //         'help' => '是否递归获取子部门下面的成员：1-递归获取，0-只获取本部门',
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '1',
        //         'render' => '',
        //     ),
        //     'search' => array(
        //         'is_show' => true
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );
        $schemas['userid'] = array(
            'name' => '成员UserID',
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
                'help' => '成员UserID。对应管理端的帐号',
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
        $schemas['name'] = array(
            'name' => '成员名称',
            'data' => array(
                'type' => 'string',
                'length' => 100,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '成员名称，此字段从2019年12月30日起，对新创建第三方应用不再返回，2020年6月30日起，对所有历史第三方应用不再返回，后续第三方仅通讯录应用可获取，第三方页面需要通过通讯录展示组件来展示名字',
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
        $schemas['department'] = array(
            'name' => '成员所属部门列表',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '成员所属部门列表。列表项为部门ID，32位整型',
            ),
            'list' => array(
                'is_show' => false,
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
        $schemas['open_userid'] = array(
            'name' => '全局唯一',
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
                'help' => '全局唯一。对于同一个服务商，不同应用获取到企业内同一个成员的open_userid是相同的，最多64个字节。仅第三方应用可获取',
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
        $schemas['get_time'] = array(
            'name' => '获取时间',
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
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
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
        return '部门成员';
    }

    protected function getModel()
    {
        return $this->modelDepartmentUser;
    }
}
