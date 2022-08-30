<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\ResignedTransferResult;

/**
 * @title({name="离职成员的客户分配情况"})
 *
 * @name 离职成员的客户分配情况
 */
class ExternalcontactresignedtransferresultController extends BaseController
{
    private $modelExternalcontactResignedTransferResult;

    public function initialize()
    {
        $this->modelExternalcontactResignedTransferResult = new ResignedTransferResult();
        parent::initialize();
    }

    protected function getFormTools2($tools)
    {
        $tools['ResignedTransferResult'] = array(
            'title' => '再分配',
            'action' => 'ResignedTransferResult',
            'is_show' => function ($row) {
                if (!empty($row['external_userid']) && !empty($row['handover_userid']) && !empty($row['takeover_userid']) && empty($row['is_ResignedTransferResulted']) && !empty($row['authorizer_appid'])) {
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
     * @title({name="再分配"})
     *
     * @name 再分配
     */
    public function ResignedTransferResultAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactResignedTransferResult/ResignedTransferResult?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactResignedTransferResult->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "再分配";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = trim($this->request->get('ResignedTransferResult_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->ResignedTransferResult($data);

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4FormTool()
    {
        $fields = array();
        $fields['_id'] = array(
            'name' => 'ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );
        $fields['external_userid'] = array(
            'name' => '外部联系人的userid',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );
        $fields['handover_userid'] = array(
            'name' => '离职成员的userid',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );
        $fields['takeover_userid'] = array(
            'name' => '接替成员的userid',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );
        $fields['ResignedTransferResult_agentid'] = array(
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
        $schemas['agentid'] = array(
            'name' => '应用ID',
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
                'items' => $this->agentItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->agentItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->agentItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['handover_userid'] = array(
            'name' => '原添加成员的userid',
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
        $schemas['takeover_userid'] = array(
            'name' => '接替成员的userid',
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
        $schemas['external_userid'] = array(
            'name' => '外部联系人的userid',
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
                'help' => '外部联系人的userid，注意不是企业成员的帐号',
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
        //接替状态， 1-接替完毕 2-等待接替 3-客户拒绝 4-接替成员客户达到上限
        $statusOptions = array();
        $statusOptions['1'] = '接替完毕';
        $statusOptions['2'] = '等待接替';
        $statusOptions['3'] = '客户拒绝';
        $statusOptions['4'] = '接替成员客户达到上限';
        $schemas['status'] = array(
            'name' => '接替状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $statusOptions,
                'help' => '接替状态， 1-接替完毕 2-等待接替 3-客户拒绝 4-接替成员客户达到上限',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $statusOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $statusOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['takeover_time'] = array(
            'name' => '接替客户的时间',
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
                'help' => '接替客户的时间，如果是等待接替状态，则为未来的自动接替时间',
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
        return '离职成员的客户分配情况';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactResignedTransferResult;
    }
}
