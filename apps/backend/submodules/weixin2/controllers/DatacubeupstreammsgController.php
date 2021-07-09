<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsg;

/**
 * @title({name="消息发送概况数据"})
 *
 * @name 消息发送概况数据
 */
class DatacubeupstreammsgController extends BaseController
{
    private $modelUpstreamMsg;
    public function initialize()
    {
        $this->modelUpstreamMsg = new UpstreamMsg();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['syncupstreammsg'] = array(
            'title' => '获取消息发送概况数据',
            'action' => 'syncupstreammsg',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="获取消息发送概况数据"})
     *
     * @name 获取消息发送概况数据
     */
    public function syncupstreammsgAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/datacubeupstreammsg/syncupstreammsg?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                // 起始日期
                $fields['upstreammsg_start_ref_date'] = array(
                    'name' => '起始日期',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );

                // 结束日期
                $fields['upstreammsg_end_ref_date'] = array(
                    'name' => '结束日期',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $title = "获取消息发送概况数据";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('upstreammsg_component_appid'));
                $authorizer_appid = trim($this->request->get('upstreammsg_authorizer_appid'));
                $start_ref_date = trim($this->request->get('upstreammsg_start_ref_date'));
                $end_ref_date = trim($this->request->get('upstreammsg_end_ref_date'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                if (empty($start_ref_date)) {
                    return $this->makeJsonError("起始日期未设定");
                }
                if (empty($end_ref_date)) {
                    return $this->makeJsonError("结束日期未设定");
                }
                $start_ref_date = strtotime($start_ref_date);
                $end_ref_date = strtotime($end_ref_date);
                if ($end_ref_date < $start_ref_date) {
                    return $this->makeJsonError("结束日期小于起始日期");
                }

                $weixinopenService = new \App\Weixin2\Services\WeixinService($authorizer_appid, $component_appid);
                $res = $weixinopenService->syncUpstreamMsg($start_ref_date, $end_ref_date);
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['upstreammsg_component_appid'] = array(
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
        $fields['upstreammsg_authorizer_appid'] = array(
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
                'required' => false
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
        
        $schemas['ref_date'] = array(
            'name' => '数据的日期',
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
        // 消息类型，代表含义如下： 1代表文字 2代表图片 3代表语音 4代表视频 6代表第三方应用消息（链接消息）
        $msgtypeOptions = array();
        $msgtypeOptions['1'] = "1:文字";
        $msgtypeOptions['2'] = "2:图片";
        $msgtypeOptions['3'] = "3:语音";
        $msgtypeOptions['4'] = "4:视频";
        $msgtypeOptions['6'] = "6:第三方应用消息（链接消息）";

        $schemas['msg_type'] = array(
            'name' => '消息类型',
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
                'items' => $msgtypeOptions,
                'help' => '消息类型，代表含义如下： 1代表文字 2代表图片 3代表语音 4代表视频 6代表第三方应用消息（链接消息）',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $msgtypeOptions,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $msgtypeOptions,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['msg_user'] = array(
            'name' => '上行发送了消息的用户数',
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
                'items' => '',
                'help' => '上行发送了（向公众号发送了）消息的用户数',
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
        $schemas['msg_count'] = array(
            'name' => '上行发送了消息的消息总数',
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
        return '消息发送概况数据';
    }

    protected function getModel()
    {
        return $this->modelUpstreamMsg;
    }
}
