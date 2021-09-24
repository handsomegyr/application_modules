<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\Service;
use App\Cronjob\Models\Task;

/**
 * @title({name="服务管理"})
 *
 * @name 服务管理
 */
class ServiceController extends \App\Backend\Controllers\FormController
{
    private $modelService;
    private $modelTask;

    protected $COMPANY_CUT_TASKTYPE = 1;
    protected $NGINX_SERVER_DOMAIN = ".myweb.com";

    public function initialize()
    {
        $this->modelService = new Service();
        $this->modelTask = new Task();
        parent::initialize();
    }

    protected function getRowTools2($tools)
    {
        $tools['process'] = array(
            'title' => '执行',
            'action' => 'process',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['process_list'])) {
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
        $tools['process'] = array(
            'title' => '执行',
            'action' => 'process',
            'process_without_modal' => true,
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['process_list'])) {
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
     * @title({name="执行"})
     *
     * @name 执行
     */
    public function processAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/service/process?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelService->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 登录一个任务
            $taskContent = array();
            $taskContent['process_list'] = $data['process_list'];
            if (!empty($data['params'])) {
                $taskContent['params'] = $data['params'];
            }
            $taskInfo = $this->modelTask->log($this->COMPANY_CUT_TASKTYPE, $taskContent);
            $res['taskInfo'] = $taskInfo;
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \\App\Common\Utils\Helper::myJsonEncode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;

        $schemas['name'] = array(
            'name' => '服务名称',
            'data' => array(
                'type' => 'string',
                'length' => 50,
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
        $schemas['process_list'] = array(
            'name' => '处理列表',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '多个处理用逗号间隔',
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
        $schemas['params'] = array(
            'name' => '服务参数',
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
        return '服务管理';
    }

    protected function getModel()
    {
        return $this->modelService;
    }

    protected function setDefaultQuery(\App\Backend\Models\Input $input)
    {
        if (isset($_SESSION['roleInfo'])) {
            $roleAlias = $_SESSION['roleInfo']['alias'];
        } else {
            $roleAlias = 'guest';
        }
        // 只有在超级管理员的时候才显示出来
        if ($roleAlias != 'superAdmin') {
            $queryCondtions = array(
                '$exp' => " ( 1=0 ) "
            );
            $input->setDefaultQuery($queryCondtions);
        }
        return $input;
    }
}
