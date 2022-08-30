<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\UserBehaviorDataByUserid;

/**
 * @title({name="联系客户按用户统计数据"})
 *
 * @name 联系客户按用户统计数据
 */
class ExternalcontactuserbehaviordatabyuseridController extends BaseController
{
    private $modelExternalcontactUserBehaviorDataByuserid;

    public function initialize()
    {
        $this->modelExternalcontactUserBehaviorDataByuserid = new UserBehaviorDataByUserid();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['getuserbehaviordata'] = array(
            'title' => '获取联系客户统计数据',
            'action' => 'getuserbehaviordata',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
    }

    /**
     * @title({name="获取联系客户统计数据"})
     *
     * @name 获取联系客户统计数据
     */
    public function getuserbehaviordataAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactuserbehaviordatabyuserid/getuserbehaviordata
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取联系客户统计数据";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('unassigned_provider_appid'));
                $authorizer_appid = trim($this->request->get('unassigned_authorizer_appid'));
                $agent_agentid = trim($this->request->get('unassigned_agent_agentid'));
                $userbehavior_userid = trim($this->request->get('userbehavior_userid'));
                $userbehavior_start_time = trim($this->request->get('userbehavior_start_time'));
                $userbehavior_end_time = trim($this->request->get('userbehavior_end_time'));
                if (empty($provider_appid)) {
                    // return $this->response()->error("第三方服务商应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                if (empty($userbehavior_userid)) {
                    return $this->makeJsonError("配置了客户联系功能的成员userid未设定");
                }
                if (empty($userbehavior_start_time)) {
                    return $this->makeJsonError("数据起始时间未设定");
                }
                if (empty($userbehavior_end_time)) {
                    return $this->makeJsonError("数据结束时间未设定");
                }
                $userbehavior_end_time = strtotime($userbehavior_end_time);
                $userbehavior_start_time = strtotime($userbehavior_start_time);

                if ($userbehavior_end_time - $userbehavior_start_time <= 0) {
                    return $this->makeJsonError("数据结束时间比数据起始时间早了");
                }
                if ($userbehavior_end_time - $userbehavior_start_time > 30 * 24 * 3600) {
                    return $this->makeJsonError("数据结束时间比数据起始时间超过了支持的最大查询跨度为30天");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agent_agentid);
                $res = $weixinopenService->getUserBehaviorDataByUserId($userbehavior_userid, $userbehavior_start_time, $userbehavior_end_time);
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $time = time();
        $today = date('Y-m-d', $time);
        $yesterday = date('Y-m-d', $time - 3600 * 24);

        $fields = array();
        $fields['userbehavior_provider_appid'] = array(
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
        $fields['userbehavior_authorizer_appid'] = array(
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
        $fields['userbehavior_agent_agentid'] = array(
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

        $fields['userbehavior_userid'] = array(
            'name' => '成员userid',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
            ),
        );

        $fields['userbehavior_start_time'] = array(
            'name' => '数据起始时间',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true,
            ),
        );
        $fields['userbehavior_end_time'] = array(
            'name' => '数据结束时间',
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
        $schemas['userid'] = array(
            'name' => '用户ID',
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
        $schemas['stat_time'] = array(
            'name' => '数据日期',
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
                'help' => '数据日期，为当日0点的时间戳',
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
        $schemas['new_apply_cnt'] = array(
            'name' => '发起申请数',
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
                'help' => '发起申请数，成员通过「搜索手机号」、「扫一扫」、「从微信好友中添加」、「从群聊中添加」、「添加共享、分配给我的客户」、「添加单向、双向删除好友关系的好友」、「从新的联系人推荐中添加」等渠道主动向客户发起的好友申请数量。',
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
        $schemas['new_contact_cnt'] = array(
            'name' => '新增客户数',
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
                'help' => '新增客户数，成员新添加的客户数量。',
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
        $schemas['chat_cnt'] = array(
            'name' => '聊天总数',
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
                'help' => '聊天总数， 成员有主动发送过消息的聊天数，包括单聊和群聊。',
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
        $schemas['message_cnt'] = array(
            'name' => '发送消息数',
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
                'help' => '发送消息数，成员在单聊和群聊中发送的消息总数。',
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
        $schemas['reply_percentage'] = array(
            'name' => '已回复聊天占比',
            'data' => array(
                'type' => 'string',
                'length' => 11,
                'defaultValue' => 0.00
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => '',
                'help' => '已回复聊天占比，客户主动发起聊天后，成员在一个自然日内有回复过消息的聊天数/客户主动发起的聊天数比例，不包括群聊，仅在确有回复时返回。',
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
        $schemas['avg_reply_time'] = array(
            'name' => '平均首次回复时长(分钟)',
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
                'help' => '平均首次回复时长，单位为分钟，即客户主动发起聊天后，成员在一个自然日内首次回复的时长间隔为首次回复时长，所有聊天的首次回复总时长/已回复的聊天总数即为平均首次回复时长，不包括群聊，仅在确有回复时返回。',

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
        $schemas['negative_feedback_cnt'] = array(
            'name' => '删除/拉黑成员的客户数',
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
                'help' => '删除/拉黑成员的客户数，即将成员删除或加入黑名单的客户数。',
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
        return '联系客户按用户统计数据';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactUserBehaviorDataByuserid;
    }
}
