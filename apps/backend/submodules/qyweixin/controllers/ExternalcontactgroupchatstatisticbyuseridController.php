<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupChatStatisticByUserid;

/**
 * @title({name="客户群按群主统计数据"})
 *
 * @name 客户群按群主统计数据
 */
class ExternalcontactgroupchatstatisticbyuseridController extends BaseController
{
    private $modelExternalcontactGroupChatStatisticByuserid;

    public function initialize()
    {
        $this->modelExternalcontactGroupChatStatisticByuserid = new GroupChatStatisticByUserid();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['getgroupchatstatistic'] = array(
            'title' => '获取客户群统计数据',
            'action' => 'getgroupchatstatistic',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
    }

    /**
     * @title({name="获取客户群统计数据"})
     *
     * @name 获取客户群统计数据
     */
    public function getgroupchatstatisticAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactgroupchatstatisticbyuserid/getgroupchatstatistic
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取客户群统计数据";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('groupchatstatistic_provider_appid'));
                $authorizer_appid = trim($this->request->get('groupchatstatistic_authorizer_appid'));
                $agent_agentid = trim($this->request->get('groupchatstatistic_agent_agentid'));
                $groupchatstatistic_userid = trim($this->request->get('groupchatstatistic_userid'));
                $groupchatstatistic_day_begin_time = trim($this->request->get('groupchatstatistic_day_begin_time'));

                if (empty($provider_appid)) {
                    // return $this->response()->error("第三方服务商应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                if (empty($groupchatstatistic_userid)) {
                    return $this->makeJsonError("群主ID未设定");
                }
                if (empty($groupchatstatistic_day_begin_time)) {
                    return $this->makeJsonError("数据起始时间未设定");
                }
                $groupchatstatistic_day_begin_time = strtotime($groupchatstatistic_day_begin_time);

                if (time() - $groupchatstatistic_day_begin_time <= 0) {
                    return $this->makeJsonError("数据结束时间比数据起始时间早了");
                }
                //昨天至前180天
                if (time() - $groupchatstatistic_day_begin_time > 180 * 24 * 3600) {
                    return $this->makeJsonError("数据起始时间只能是昨天至前180天");
                }

                $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agent_agentid);
                $owner_filter = array();
                $owner_filter['userid_list'] = array($groupchatstatistic_userid);
                $order_by = 1;
                $order_asc = 0;
                $offset = 0;
                $limit = 1000;
                $res = $weixinopenService->getGroupChatStatistic($groupchatstatistic_day_begin_time, $owner_filter,  $order_by, $order_asc, $offset, $limit);
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $time = time();
        $yesterday = date('Y-m-d', $time - 3600 * 24);

        $fields = array();
        $fields['groupchatstatistic_provider_appid'] = array(
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
        $fields['groupchatstatistic_authorizer_appid'] = array(
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
        $fields['groupchatstatistic_agent_agentid'] = array(
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

        $fields['groupchatstatistic_userid'] = array(
            'name' => '群主ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
            ),
        );

        $fields['groupchatstatistic_day_begin_time'] = array(
            'name' => '数据起始时间',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['day_begin_time'] = array(
            'name' => '开始时间',
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
                'help' => '开始时间，填当天开始的0分0秒（否则系统自动处理为当天的0分0秒）。取值范围：昨天至前180天',
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
        // $schemas['userid'] = array(
        //     'name' => '群主ID',
        //     'data' => array(
        //         'type' => 'string',
        //         'length' => 255,
        //         'defaultValue' => ''
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'text',
        //         'is_show' => true,
        //         'items' => ''
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => '',
        //     ),
        //     'search' => array(
        //         'is_show' => true
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );

        // $orderbyOptions = array();
        // $orderbyOptions['1'] = '新增群的数量';
        // $orderbyOptions['2'] = '群总数';
        // $orderbyOptions['3'] = '新增群人数';
        // $orderbyOptions['4'] = '群总人数';
        // $schemas['order_by'] = array(
        //     'name' => '排序方式',
        //     'data' => array(
        //         'type' => 'integer',
        //         'length' => 1,
        //         'defaultValue' => 1
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'radio',
        //         'is_show' => true,
        //         'items' => $orderbyOptions,
        //         'help' => '排序方式。1 - 新增群的数量 2 - 群总数 3 - 新增群人数 4 - 群总人数 默认为1',
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => '',
        //         'items' => $orderbyOptions,
        //     ),
        //     'search' => array(
        //         'input_type' => 'select',
        //         'is_show' => true,
        //         'items' => $orderbyOptions,
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );
        // $schemas['order_asc'] = array(
        //     'name' => '是否升序',
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
        //         'help' => '是否升序。0-否；1-是。默认降序',
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
        // $schemas['offset'] = array(
        //     'name' => '分页偏移量',
        //     'data' => array(
        //         'type' => 'integer',
        //         'length' => 11,
        //         'defaultValue' => 0
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'number',
        //         'is_show' => true,
        //         'items' => '',
        //         'help' => '分页，偏移量, 默认为0',
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => '',
        //     ),
        //     'search' => array(
        //         'is_show' => true
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );
        // $schemas['limit'] = array(
        //     'name' => '分页预期请求的数据量',
        //     'data' => array(
        //         'type' => 'integer',
        //         'length' => 11,
        //         'defaultValue' => 1000
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'number',
        //         'is_show' => true,
        //         'items' => '',
        //         'help' => '分页，预期请求的数据量，默认为500，取值范围 1 ~ 1000',
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => '',
        //     ),
        //     'search' => array(
        //         'is_show' => true
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );
        $schemas['owner'] = array(
            'name' => '群主ID',
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
        $schemas['new_chat_cnt'] = array(
            'name' => '新增客户群数量',
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
        $schemas['chat_total'] = array(
            'name' => '截至当天客户群总数量',
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
        $schemas['chat_has_msg'] = array(
            'name' => '截至当天有发过消息的客户群数量',
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
        $schemas['new_member_cnt'] = array(
            'name' => '客户群新增群人数。',
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
        $schemas['member_total'] = array(
            'name' => '截至当天客户群总人数',
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
        $schemas['member_has_msg'] = array(
            'name' => '截至当天有发过消息的群成员数',
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
        $schemas['msg_total'] = array(
            'name' => '截至当天客户群消息总数',
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
        return '客户群按群主统计数据';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactGroupChatStatisticByuserid;
    }
}
