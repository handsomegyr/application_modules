<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\Project;
use App\Cronjob\Models\Task;
use App\Backend\Submodules\Database\Models\Project as DBProject;

/**
 * @title({name="项目管理"})
 *
 * @name 项目管理
 */
class ProjectController extends \App\Backend\Controllers\FormController
{    
    // 是否只读
    // protected $readonly = true;

    private $modelProject;
    private $modelTask;
    private $modelDbProject;

    protected $COMPANY_CUT_TASKTYPE = 1;
    protected $NGINX_SERVER_DOMAIN = ".myweb.com";

    public function initialize()
    {
        $this->modelProject = new Project();
        $this->modelTask = new Task();
        $this->modelDbProject = new DBProject();
        parent::initialize();
    }

    protected function getRowTools2($tools)
    {
        $tools['buildprojectsettings'] = array(
            'title' => '构建项目环境',
            'action' => 'buildprojectsettings',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code']) && !empty($row['db_pwd'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['rsyncdevtotest'] = array(
            'title' => '同步测试',
            'action' => 'rsyncdevtotest',
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

        $tools['publishtesttoprod'] = array(
            'title' => '发布正式',
            'action' => 'publishtesttoprod',
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

    protected function getFormTools2($tools)
    {
        $tools['buildprojectsettings'] = array(
            'title' => '构建项目环境',
            'action' => 'buildprojectsettings',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['project_code']) && !empty($row['db_pwd'])) {
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
        // http://www.myapplicationmodule.com.com/admin/weixin2/project/buildprojectsettings?id=xxx
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
            $taskContent = array();
            $taskContent['project_code'] = $data['project_code'];
            $taskContent['project_id'] = $data['_id'];
            $taskContent['db_pwd'] = $data['db_pwd'];
            $taskContent['process_list'] = 'create_project';
            $taskInfo = $this->modelTask->log($this->COMPANY_CUT_TASKTYPE, $taskContent);
            $res['taskInfo'] = $taskInfo;
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="同步测试"})
     *
     * @name 同步测试
     */
    public function rsyncdevtotestAction()
    {
        // http://www.myapplicationmodule.com.com/admin/weixin2/project/rsyncdevtotest?id=xxx
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
            $taskContent = array();
            $taskContent['project_code'] = $data['project_code'];
            $taskContent['project_id'] = $data['_id'];
            $taskContent['process_list'] = 'rsync_dev_to_test';
            $taskInfo = $this->modelTask->log($this->COMPANY_CUT_TASKTYPE, $taskContent);
            $res['taskInfo'] = $taskInfo;
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="发布正式"})
     *
     * @name 发布正式
     */
    public function publishtesttoprodAction()
    {
        // http://www.myapplicationmodule.com.com/admin/weixin2/project/publishtesttoprod?id=xxx
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
            $taskContent = array();
            $taskContent['project_code'] = $data['project_code'];
            $taskContent['project_id'] = $data['_id'];
            $taskContent['process_list'] = 'publish_test_to_prod';
            $taskInfo = $this->modelTask->log($this->COMPANY_CUT_TASKTYPE, $taskContent);
            $res['taskInfo'] = $taskInfo;
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;

        $schemas['project_code'] = array(
            'name' => '项目编号',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
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
        $schemas['isSystem'] = array(
            'name' => '是否公司内部项目',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                // 'input_type' => 'radio',
                'input_type' => 'switch',
                'readonly' => true,
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

        $schemas['db_pwd'] = array(
            'name' => '数据库密码',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
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
                'is_show' => false,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['ae'] = array(
            'name' => 'AE信息',
            'data' => array(
                'type' => 'array',
                'length' => 1024,
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                // 'input_type' => 'select', 
                'select' => array(
                    'multiple' => true
                ),
                'input_type' => 'checkbox',
                // 'readonly' => true,
                'checkbox' => array(
                    'isCheckAll' => true
                ),
                'is_show' => true,
                'items' => array('AE1' => 'AE1', 'AE2' => 'AE2', 'AE3' => 'AE3', 'AE4' => 'AE4'),
                'help' => '多个AE用逗号间隔'
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => array('AE1' => 'AE1', 'AE2' => 'AE2', 'AE3' => 'AE3', 'AE4' => 'AE4'),
            ),
            'search' => array(
                // 'input_type' => 'select',
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['executives'] = array(
            'name' => '执行人信息',
            'data' => array(
                'type' => 'array',
                'length' => 1024,
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => array('PE1' => 'PE1', 'PE2' => 'PE2', 'PE3' => 'PE3', 'PE4' => 'PE4'),
                'select' => array(
                    'multiple' => true
                ),
                'help' => '多个执行人用逗号间隔',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => array('PE1' => 'PE1', 'PE2' => 'PE2', 'PE3' => 'PE3', 'PE4' => 'PE4'),
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
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
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
            'name' => '开发地址',
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
                'help' => '多个地址用逗号间隔',
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
        $schemas['test_url'] = array(
            'name' => '测试地址',
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
                'help' => '多个地址用逗号间隔',
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
        $schemas['product_url'] = array(
            'name' => '正式地址',
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
                'help' => '多个地址用逗号间隔',
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

    protected function setDefaultQuery(\App\Backend\Models\Input $input)
    {
        if (isset($_SESSION['roleInfo'])) {
            $roleAlias = $_SESSION['roleInfo']['alias'];
        } else {
            $roleAlias = 'guest';
        }
        if ($roleAlias != 'superAdmin') {
            $queryCondtions = array(
                '$exp' => " ( exists (select * from icompany_project_user where `icompany_project_user`.`project_id` = `icompany_project`.`_id` and `icompany_project_user`.`user_id` = '{$_SESSION['admin_id']}' and `icompany_project_user`.`__REMOVED__` = 0) ) "
            );
            // $queryCondtions = array(
            //     'id' => array('$in' => array())
            // );
            $input->setDefaultQuery($queryCondtions);
        }
        return $input;
    }

    protected function insert(\App\Backend\Models\Input $input, $row)
    {
        try {
            $this->modelDbProject->begin();

            // 新建一条公司项目记录
            $newInfo = parent::insert($input, $row);

            // 创建一条公司项目记录所对应的idb管理用的数据库记录
            $data = array();
            $data['company_project_id'] = $newInfo['_id'];
            $data['name'] = $newInfo['project_name'];
            $data['dbname'] = $newInfo['project_code'];
            $data['sn'] = $newInfo['db_pwd'];
            $data['desc'] = $newInfo['description'];
            $data['isSystem'] = $newInfo['isSystem'];
            $this->modelDbProject->insert($data);

            $this->modelDbProject->commit();
            return $newInfo;
        } catch (\Exception $e) {
            $this->modelDbProject->rollback();
            throw $e;
        }
    }
}
