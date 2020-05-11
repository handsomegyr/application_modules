<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\Project;

/**
 * @title({name="项目管理"})
 *
 * @name 项目管理
 */
class ProjectController extends \App\Backend\Controllers\FormController
{
    private $modelProject;
    protected $NGINX_SERVER_DOMAIN = ".myweb.com ";
    public function initialize()
    {
        $this->modelProject = new Project();
        parent::initialize();
    }

    protected function getRowTools2($tools)
    {
        $tools['buildprojectsettings'] = array(
            'title' => '构建项目环境',
            'action' => 'buildprojectsettings',
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['buildprojectsettings'] = array(
            'title' => '构建项目环境',
            'action' => 'buildprojectsettings',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code'])) {
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
     * @title({name="构建项目环境"})
     *
     * @name 构建项目环境
     */
    public function buildprojectsettingsAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/authorizer/buildprojectsettings?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelProject->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 登录一个任务
            $modelTask = new \App\Cronjob\Models\Task();
            $taskContent = array();
            $taskContent['project_code'] = $data['project_code'];
            $taskContent['project_id'] = $data['_id'];
            $taskContent['process_list'] = 'create_project';
            $taskInfo = $modelTask->log(1, $taskContent);

            //更新
            //svn://192.168.81.129/p001
            //$modelProject->update(array('_id' => $project_id), array('$set' => array('svn_url' => $svn_url)));
            $res['taskInfo'] = $taskInfo;
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['project_code'] = array(
            'name' => '项目编号',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候显示
                        $settings['is_show'] = true;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
        $schemas['operation_code'] = array(
            'name' => '运维编号',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候显示
                        $settings['is_show'] = true;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
        $schemas['project_name'] = array(
            'name' => '项目名称',
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
        $schemas['language'] = array(
            'name' => '开发语言',
            'data' => array(
                'type' => 'string',
                'length' => 50,
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
        $schemas['ae'] = array(
            'name' => 'AE信息,多个AE用逗号间隔',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
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
        $schemas['executives'] = array(
            'name' => '执行人信息,多个执行人用逗号间隔',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
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
        $schemas['svn_url'] = array(
            'name' => '项目SVN地址',
            'data' => array(
                'type' => 'string',
                'length' => 256,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    } else {
                        // 修改的时候不能修改
                        $settings['readonly'] = true;
                    }
                    return $settings;
                }
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
        $schemas['dev_url'] = array(
            'name' => '开发地址,多个地址用逗号间隔',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    }
                    return $settings;
                }
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
        $schemas['test_url'] = array(
            'name' => '测试地址,多个地址用逗号间隔',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    }
                    return $settings;
                }
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
        $schemas['product_url'] = array(
            'name' => '正式地址,多个地址用逗号间隔',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'extensionSettings' => function ($column, $Grid) {
                    $settings = array();
                    $row = $column->getRow();
                    if (empty($row->_id)) {
                        // 新增的时候不显示
                        $settings['is_show'] = false;
                    }
                    return $settings;
                }
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
        $schemas['server_id'] = array(
            'name' => '项目所在服务器',
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
        $schemas['dm_names'] = array(
            'name' => '项目域名',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
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
        $schemas['enabled'] = array(
            'name' => '允许发布',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
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
                'list_type' => '1',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        //上线状态:0开发中,1部署中,2已上线,3已下线
        $onlineOptions = array();
        $onlineOptions['0'] = '开发中';
        $onlineOptions['1'] = '部署中';
        $onlineOptions['2'] = '已上线';
        $onlineOptions['3'] = '已下线';
        $schemas['online'] = array(
            'name' => '上线状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $onlineOptions
            ),
            'list' => array(
                'is_show' => true,
                'render' => '',
                'items' => $onlineOptions
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'select',
                'items' => $onlineOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['last_upload_time'] = array(
            'name' => '最后发布时间',
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
        $schemas['description'] = array(
            'name' => '项目备注',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
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
        $schemas['svn_log'] = array(
            'name' => 'SVN日志',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => ''
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
        return '项目管理';
    }

    protected function getModel()
    {
        return $this->modelProject;
    }

    protected function validate4Insert(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        $this->getModel()->checkProjectCode($input->id, $input->project_code);
        $this->getModel()->checkOperationCode($input->id, $input->operation_code);
        //svn://192.168.81.129/p001
        $input->svn_url = "svn://192.168.81.129/" . $input->project_code;

        //$project_code . "_dev" . $this->NGINX_SERVER_DOMAIN
        $input->dev_url = $input->project_code . "_dev" . $this->NGINX_SERVER_DOMAIN;
        $input->test_url = $input->project_code . "_test" . $this->NGINX_SERVER_DOMAIN;
        $input->product_url = $input->project_code . $this->NGINX_SERVER_DOMAIN;
    }
}
