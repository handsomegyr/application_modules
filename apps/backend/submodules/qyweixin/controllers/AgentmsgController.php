<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\AgentMsg\AgentMsg;
use App\Backend\Submodules\Qyweixin\Models\AgentMsg\Type;
use App\Backend\Submodules\Qyweixin\Models\Media\Media;

/**
 * @title({name="应用消息"})
 *
 * @name 应用消息
 */
class AgentmsgController extends BaseController
{
    private $modelAgentMsg;

    private $modelType;
    private $modelMedia;

    public function initialize()
    {
        $this->modelAgentMsg = new AgentMsg();
        $this->modelType = new Type();
        $this->modelMedia = new Media();

        $this->typeItems = $this->modelType->getAll();
        $this->mediaItems = $this->modelMedia->getAllByType("", "_id");

        parent::initialize();
    }

    protected $typeItems = null;
    protected $mediaItems = null;

    protected function getFormTools2($tools)
    {
        $tools['sendmsg'] = array(
            'title' => '发送消息',
            'action' => 'sendmsg',
            // 'is_show' =>true,
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['agentid']) && !empty($row['provider_appid'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
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
                'required' => true
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
        $schemas['name'] = array(
            'name' => '应用消息名称',
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
        $schemas['msg_type'] = array(
            'name' => '应用消息类型',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->typeItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->typeItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->typeItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['media'] = array(
            'name' => '临时素材',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->mediaItems,
                'help' => '发送的图片/语音/视频/图文消息（点击跳转到图文消息页）的媒体ID，临时素材记录ID,(图片,语音,视频消息用)',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->mediaItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->mediaItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        // $schemas['media_id'] = array(
        //     'name' => '永久素材',
        //     'data' => array(
        //         'type' => 'string',
        //         'length' => 255,
        //         'defaultValue' => ''
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'select',
        //         'is_show' => true,
        //         'items' => $this->materialItems,
        //         'help' => '发送的图片/语音/视频/图文消息（点击跳转到图文消息页）的媒体ID，永久素材媒体ID,(图片,语音,视频,mpnews图文消息用)',
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => '',
        //         'items' => $this->materialItems,
        //     ),
        //     'search' => array(
        //         'input_type' => 'select',
        //         'items' => $this->materialItems,
        //         'is_show' => true
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );

        $schemas['title'] = array(
            'name' => '标题',
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
                'items' => '',
                'help' => '标题,(音乐,视频,图文消息用)',
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
            'name' => '描述',
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
                'help' => '描述,(文本,音乐,视频,图文消息用)',
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
        $schemas['url'] = array(
            'name' => '点击后跳转的链接',
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
                'help' => '点击后跳转的链接。最长2048字节，请确保包含了协议头(http/https)',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => ''
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['btntxt'] = array(
            'name' => '按钮文字',
            'data' => array(
                'type' => 'string',
                'length' => 10,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '按钮文字。 默认为“详情”， 不超过4个文字，超过自动截断。',
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
        $schemas['appid'] = array(
            'name' => '小程序的appid',
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
                'help' => '小程序的appid，要求小程序的appid需要与公众号有关联关系',
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
        $schemas['pagepath'] = array(
            'name' => '小程序的页面路径',
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
                'help' => '小程序的页面路径，跟app.json对齐，支持参数，比如pages/index/index?foo=bar',
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

        $schemas['emphasis_first_item'] = array(
            'name' => '是否放大第一个content_item',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
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
        $schemas['content_item'] = array(
            'name' => '消息内容键值对',
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
                'help' => '消息内容键值对，最多允许10个item',
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => ''
            ),
            'search' => array(
                'input_type' => 'text',
                'is_show' => false,
            ),
            'export' => array(
                'is_show' => false
            )
        );
        $schemas['task_id'] = array(
            'name' => '任务id',
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
                'help' => '任务id，同一个应用发送的任务卡片消息的任务id不能重复，只能由数字、字母和“_-@”组成，最长支持128字节',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => ''
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['btn'] = array(
            'name' => '按钮列表',
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
                'items' => '',
                'help' => '按钮个数为1~2个。',
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

        $schemas['safe'] = array(
            'name' => '是否是保密消息',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
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
        $schemas['enable_id_trans'] = array(
            'name' => '是否开启id转译',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
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

        $schemas['enable_duplicate_check'] = array(
            'name' => '是否开启重复消息检查',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => true
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

        $schemas['duplicate_check_interval'] = array(
            'name' => '重复消息检查的时间间隔',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 1800
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => '',
                'help' => '表示是否重复消息检查的时间间隔，默认1800s，最大不超过4小时',
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
        $schemas['priority'] = array(
            'name' => '优先级',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
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

        return $schemas;
    }

    protected function getName()
    {
        return '应用消息';
    }

    protected function getModel()
    {
        return $this->modelAgentMsg;
    }

    /**
     * @title({name="发送消息"})
     * 发送消息
     *
     * @name 发送消息
     */
    public function sendmsgAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/agentmsg/sendmsg?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelBlackUser->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = array();
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );
                $fields['agentmsg_name'] = array(
                    'name' => '应用消息名称',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );

                $fields['agentmsg_ToUserName'] = array(
                    'name' => '消息接收者',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                    ),
                );
                // $fields['activity_id'] = array(
                //     'name' => '所属活动',
                //     'validation' => array(
                //         'required' => true
                //     ),
                //     'form' => array(
                //         'input_type' => 'select',
                //         'is_show' => true,
                //         'items' => $this->modelActivity->getAll(),
                //         'readonly' => true,
                //     ),
                // );
                // $fields['exchange_activity_id'] = array(
                //     'name' => '变更所属活动',
                //     'validation' => array(
                //         'required' => true
                //     ),
                //     'form' => array(
                //         'input_type' => 'select',
                //         'is_show' => true,
                //         'items' => $this->modelActivity->getAll()
                //     ),
                // );

                $title = "发送消息";
                return $this->showModal($title, $fields, $row);
            } else {
                // 如果是POST请求的话就是进行具体的处理  
                $ToUserName = trim($this->request->get('ToUserName'));
                if (empty($ToUserName)) {
                    return $this->makeJsonError("ToUserName未指定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($row['authorizer_appid'], $row['provider_appid'], $row['agentid']);
                $FromUserName = "";
                $agentMsgInfo = $row;
                $match = array();
                $match['id'] = 0;
                $match['keyword'] = '';
                $match['agent_msg_type'] = $$row['msg_type'];
                $sendRet = $weixinopenService->sendAgentMsg($FromUserName, $ToUserName, $agentMsgInfo, $match);
                if ($sendRet['is_ok']) {
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
                } else {
                    return $this->makeJsonError($sendRet['api_ret']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
