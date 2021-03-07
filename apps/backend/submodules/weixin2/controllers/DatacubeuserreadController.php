<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\DataCube\UserRead;

/**
 * @title({name="图文统计数据"})
 *
 * @name 图文统计数据
 */
class DatacubeuserreadController extends BaseController
{
    private $modelUserRead;
    public function initialize()
    {
        $this->modelUserRead = new UserRead();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['syncuserread'] = array(
            'title' => '获取图文统计数据',
            'action' => 'syncuserread',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="获取图文统计数据"})
     *
     * @name 获取图文统计数据
     */
    public function syncuserreadAction()
    {
        // http://www.myapplicationmodule.com.com/admin/weixin2/datacubeuserread/syncuserread?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                // 起始日期
                $fields['userread_start_ref_date'] = array(
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
                $fields['userread_end_ref_date'] = array(
                    'name' => '结束日期',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $title = "获取图文统计数据";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('userread_component_appid'));
                $authorizer_appid = trim($this->request->get('userread_authorizer_appid'));
                $start_ref_date = trim($this->request->get('userread_start_ref_date'));
                $end_ref_date = trim($this->request->get('userread_end_ref_date'));
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
                $res = $weixinopenService->syncUserRead($start_ref_date, $end_ref_date);
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['userread_component_appid'] = array(
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
        $fields['userread_authorizer_appid'] = array(
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
        $schemas['int_page_read_user'] = array(
            'name' => '图文页的阅读人数',
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
                'help' => '图文页（点击群发图文卡片进入的页面）的阅读人数',
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
        $schemas['int_page_read_count'] = array(
            'name' => '图文页的阅读次数',
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
        $schemas['ori_page_read_user'] = array(
            'name' => '原文页的阅读人数',
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
                'help' => '原文页（点击图文页“阅读原文”进入的页面）的阅读人数，无原文页时此处数据为0',
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
        $schemas['ori_page_read_count'] = array(
            'name' => '原文页的阅读次数',
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
        $schemas['share_user'] = array(
            'name' => '分享的人数',
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
        $schemas['share_count'] = array(
            'name' => '分享的次数',
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
        $schemas['add_to_fav_user'] = array(
            'name' => '收藏的人数',
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
        $schemas['add_to_fav_count'] = array(
            'name' => '收藏的次数',
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
        return '图文统计数据';
    }

    protected function getModel()
    {
        return $this->modelUserRead;
    }
}
