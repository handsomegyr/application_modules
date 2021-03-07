<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\Contact\Department;

/**
 * @title({name="部门"})
 *
 * @name 部门
 */
class DepartmentController extends BaseController
{
    private $modelDepartment;

    public function initialize()
    {
        $this->modelDepartment = new Department();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['getdepartmentlist'] = array(
            'title' => '获取部门列表',
            'action' => 'getdepartmentlist',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
    }

    /**
     * @title({name="获取部门列表"})
     *
     * @name 获取部门列表
     */
    public function getdepartmentlistAction()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/department/getdepartmentlist
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取部门列表";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('department_provider_appid'));
                $authorizer_appid = trim($this->request->get('department_authorizer_appid'));
                $agent_agentid = trim($this->request->get('department_agent_agentid'));
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
                $res = $weixinopenService->getDepartmentList($department_id);
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['department_provider_appid'] = array(
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
        $fields['department_authorizer_appid'] = array(
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
        $fields['department_agent_agentid'] = array(
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
        $schemas['deptid'] = array(
            'name' => '部门id',
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
                'help' => '部门id，32位整型，指定时必须大于1。若不填该参数，将自动生成id',
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
            'name' => '部门名称',
            'data' => array(
                'type' => 'string',
                'length' => 32,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '部门名称。同一个层级的部门名称不能重复。长度限制为1~32个字符，字符不能包括:?”<>｜',
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
        $schemas['name_en'] = array(
            'name' => '英文名称',
            'data' => array(
                'type' => 'string',
                'length' => 32,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '英文名称，需要在管理后台开启多语言支持才能生效。长度限制为1~32个字符，字符不能包括:?”<>｜',
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
        $schemas['parentid'] = array(
            'name' => '父部门id',
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
                'help' => '父部门id，32位整型',
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
        $schemas['order'] = array(
            'name' => '在父部门中的次序值',
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
                'help' => '在父部门中的次序值。order值大的排序靠前。有效的值范围是[0, 2^32)',
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
        $schemas['sync_time'] = array(
            'name' => '同步时间',
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
        return '部门';
    }

    protected function getModel()
    {
        return $this->modelDepartment;
    }
}
