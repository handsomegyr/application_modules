<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupMsg;

/**
 * @title({name="企业群发记录"})
 *
 * @name 企业群发记录
 */
class ExternalcontactgroupmsgController extends BaseController
{
    private $modelExternalcontactGroupMsg;

    public function initialize()
    {
        $this->modelExternalcontactGroupMsg = new GroupMsg();
        parent::initialize();
    }

    protected function getFormTools2($tools)
    {
        $tools['uploadmedia'] = array(
            'title' => '上传临时素材',
            'action' => 'uploadmedia',
            'is_show' => function ($row) {
                $weixinopenService = new \App\Qyweixin\Services\QyService($row['authorizer_appid'], $row['provider_appid'], $row['agentid']);
                if (!empty($row['agentid'])) {
                    if (
                        (!empty($row['image_media']) && $weixinopenService->isMediaTimeExpired($row['image_media_id'], $row['image_media_created_at'])) ||
                        (!empty($row['miniprogram_pic_media']) && $weixinopenService->isMediaTimeExpired($row['miniprogram_pic_media_id'], $row['miniprogram_pic_media_created_at']))
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['uploadmediaimg'] = array(
            'title' => '上传图片',
            'action' => 'uploadmediaimg',
            'is_show' => function ($row) {
                if (!empty($row['agentid'])) {
                    if (!empty($row['image_media']) && empty($row['image_pic_url'])) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['addGroupMsg'] = array(
            'title' => '发送消息',
            'action' => 'addGroupMsg',
            'is_show' => function ($row) {
                if (empty($row['msgid'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['getgroupmsgsendresult'] = array(
            'title' => '获取企业群发成员执行结果',
            'action' => 'getgroupmsgsendresult',
            'is_show' => function ($row) {
                if (!empty($row['agentid'])) {
                    if (!empty($row['msgid']) && !empty($row['creator'])) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="获取企业群发成员执行结果"})
     *
     * @name 获取企业群发成员执行结果
     */
    public function getgroupmsgsendresultAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactGroupMsg/getgroupmsgsendresult?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactGroupMsg->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "获取企业群发成员执行结果";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = $data['agentid'];
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $data['agentid']);
                $res = $weixinopenService->getGroupMsgSendResult($data['msgid'], $data['creator']);
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
        $fields['name'] = array(
            'name' => '名称',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );
        $fields['agentid'] = array(
            'name' => '微信企业应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->agentItems,
                'readonly' => true
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
        $schemas['msgid'] = array(
            'name' => '企业群发记录的id',
            'data' => array(
                'type' => 'string',
                'length' => 190,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '企业群发记录的id，可用于获取企业群发成员执行结果'
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
        $schemas['creator'] = array(
            'name' => '群发消息创建者userid',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '群发消息创建者userid，API接口创建的群发消息不吐出该字段'
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
        $create_type_options = array();
        $create_type_options['0'] = '企业';
        $create_type_options['1'] = '个人';
        $schemas['create_type'] = array(
            'name' => '群发消息创建来源',
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
                'items' => $create_type_options
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $create_type_options
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $create_type_options
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['create_time'] = array(
            'name' => '群发消息创建时间',
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
        $schemas['text_content'] = array(
            'name' => '消息文本内容',
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
                'help' => '消息文本内容,最长为4000字节',
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
        $schemas['attachments'] = array(
            'name' => '附件',
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
        return '企业群发记录';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactGroupMsg;
    }
}
