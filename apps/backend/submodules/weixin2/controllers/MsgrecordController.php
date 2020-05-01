<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Kf\MsgRecord;
use App\Backend\Submodules\Weixin2\Models\Kf\Account;

/**
 * @title({name="聊天记录"})
 *
 * @name 聊天记录
 */
class MsgrecordController extends BaseController
{
    private $modelMsgRecord;
    private $modelAccount;
    public function initialize()
    {
        $this->modelMsgRecord = new MsgRecord();
        $this->modelAccount = new Account();
        $this->accountItems = $this->modelAccount->getAll();
        parent::initialize();
    }
    protected $accountItems = null;

    protected function getHeaderTools2($tools)
    {
        $tools['syncmsgrecordlist'] = array(
            'title' => '获取聊天记录',
            'action' => 'syncmsgrecordlist',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="获取聊天记录"})
     *
     * @name 获取聊天记录
     */
    public function syncmsgrecordlistAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/msgrecord/syncmsgrecordlist?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                // 日期选择
                $fields['msgrecord_msgrecord_start_time'] = array(
                    'name' => '聊天开始日期',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );

                // 日期选择
                $fields['msgrecord_msgrecord_end_time'] = array(
                    'name' => '聊天结束日期',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $title = "获取聊天记录";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('msgrecord_component_appid'));
                $authorizer_appid = trim($this->request->get('msgrecord_authorizer_appid'));
                $msgrecord_start_time = trim($this->request->get('msgrecord_msgrecord_start_time'));
                $msgrecord_end_time = trim($this->request->get('msgrecord_msgrecord_end_time'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                if (empty($msgrecord_start_time)) {
                    return $this->makeJsonError("聊天开始日期未设定");
                }
                if (empty($msgrecord_end_time)) {
                    return $this->makeJsonError("聊天结束日期未设定");
                }
                $msgrecord_start_time = strtotime($msgrecord_start_time);
                $msgrecord_end_time = strtotime($msgrecord_end_time);
                if ($msgrecord_end_time < $msgrecord_start_time) {
                    return $this->makeJsonError("聊天结束日期小于聊天开始日期");
                }

                $weixinopenService = new \App\Weixin2\Services\WeixinService($authorizer_appid, $component_appid);
                $res = $weixinopenService->syncMsgRecordList($msgrecord_start_time, $msgrecord_end_time);
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['msgrecord_component_appid'] = array(
            'name' => '第三方平台应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems,
            ),
        );
        $fields['msgrecord_authorizer_appid'] = array(
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
        return $fields;
    }

    protected function getSchemas2($schemas)
    {
        $schemas['component_appid'] = array(
            'name' => '第三方平台应用ID',
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
                'items' => $this->componentItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->componentItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems
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
        $schemas['worker'] = array(
            'name' => '完整客服帐号',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->accountItems,
                'help' => '完整客服帐号，格式为：帐号前缀@公众号微信号',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->accountItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->accountItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['openid'] = array(
            'name' => '用户标识',
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
        // 操作码，2002（客服发送信息），2003（客服接收消息）
        $opercodeOptions = array();
        $opercodeOptions['2002'] = "客服发送信息";
        $opercodeOptions['2003'] = "客服接收消息";
        $schemas['opercode'] = array(
            'name' => '操作码',
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
                'items' => $opercodeOptions,
                'help' => '操作码，2002（客服发送信息），2003（客服接收消息）',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $opercodeOptions,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $opercodeOptions,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['text'] = array(
            'name' => '聊天记录',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
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
        $schemas['msgrecord_time'] = array(
            'name' => '操作时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
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

        return $schemas;
    }

    protected function getName()
    {
        return '聊天记录';
    }

    protected function getModel()
    {
        return $this->modelMsgRecord;
    }
}
