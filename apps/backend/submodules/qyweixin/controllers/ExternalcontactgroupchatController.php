<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupChat;

/**
 * @title({name="客户群"})
 *
 * @name 客户群
 */
class ExternalcontactgroupchatController extends BaseController
{
    private $modelExternalcontactGroupChat;

    public function initialize()
    {
        $this->modelExternalcontactGroupChat = new GroupChat();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['getgroupchatlist'] = array(
            'title' => '获取客户群列表',
            'action' => 'getgroupchatlist',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
    }
    protected function getFormTools2($tools)
    {
        $tools['getgroupchatinfo'] = array(
            'title' => '获取客户群详情',
            'action' => 'getgroupchatinfo',
            'is_show' => function ($row) {
                if (!empty($row['chat_id']) && !empty($row['authorizer_appid'])) {
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
     * @title({name="获取客户群列表"})
     *
     * @name 获取客户群列表
     */
    public function getgroupchatlistAction()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/externalcontactgroupchat/getgroupchatlist
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取客户群列表";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $provider_appid = trim($this->request->get('groupchat_provider_appid'));
                $authorizer_appid = trim($this->request->get('groupchat_authorizer_appid'));
                $agent_agentid = trim($this->request->get('groupchat_agent_agentid'));
                $groupchat_userid = trim($this->request->get('groupchat_userid'));
                if (empty($provider_appid)) {
                    // return $this->response()->error("第三方服务商应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                if (empty($groupchat_userid)) {
                    return $this->makeJsonError("配置了客户联系功能的成员userid未设定");
                }

                $weixinopenService = new \App\Qyweixin\Services\QyService($authorizer_appid, $provider_appid, $agent_agentid);
                $status_filter = 0;
                $offset = 0;
                $limit = 1000;
                $owner_filter = array();
                $owner_filter['userid_list'] = array($groupchat_userid);
                $res = $weixinopenService->getGroupChatList($status_filter, $owner_filter, $offset, $limit);
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['groupchat_provider_appid'] = array(
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
        $fields['groupchat_authorizer_appid'] = array(
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
        $fields['groupchat_agent_agentid'] = array(
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

        $fields['groupchat_userid'] = array(
            'name' => '配置了客户联系功能的成员userid',
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

    /**
     * @title({name="获取客户群详情"})
     *
     * @name 获取客户群详情
     */
    public function getgroupchatinfoAction()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/externalcontactgroupchat/getgroupchatinfo?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactGroupChat->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "获取客户群详情";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = trim($this->request->get('groupchat_agentid'));                
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->getGroupChatInfo($data['chat_id']);

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4FormTool()
    {
        $fields = array();
        $fields['groupchat_rec_id'] = array(
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
        $fields['groupchatid'] = array(
            'name' => '客户群ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );
        $fields['groupchat_agentid'] = array(
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
        $schemas['chat_id'] = array(
            'name' => '客户群ID',
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
        $schemas['name'] = array(
            'name' => '群名',
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
        $schemas['owner'] = array(
            'name' => '群主ID',
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
        $schemas['notice'] = array(
            'name' => '群公告',
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
        $schemas['create_time'] = array(
            'name' => '群的创建时间',
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
        $schemas['member_list'] = array(
            'name' => '群成员列表',
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
        $statusOptions = array();
        $statusOptions['0'] = '正常';
        $statusOptions['1'] = '跟进人离职';
        $statusOptions['2'] = '离职继承中';
        $statusOptions['3'] = '离职继承完成';

        $schemas['status'] = array(
            'name' => '客户群状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $statusOptions,
                'help' => '客户群状态。0 - 正常1 - 跟进人离职2 - 离职继承中3 - 离职继承完成',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $statusOptions,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $statusOptions,
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
        return '客户群';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactGroupChat;
    }
}
