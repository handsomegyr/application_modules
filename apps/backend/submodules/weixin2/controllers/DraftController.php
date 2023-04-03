<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Draft\Draft;

/**
 * @title({name="草稿箱"})
 *
 * @name 草稿箱
 */
class DraftController extends BaseController
{
    private $modelDraft;

    public function initialize()
    {
        $this->modelDraft = new Draft();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['batchgetdraft'] = array(
            'title' => '获取草稿列表',
            'action' => 'batchgetdraft',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        $tools['getdraftcount'] = array(
            'title' => '获取草稿总数',
            'action' => 'getdraftcount',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['adddraft'] = array(
            'title' => '生成草稿',
            'action' => 'adddraft',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && empty($row['media_id'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['deletedraft'] = array(
            'title' => '删除草稿',
            'action' => 'deletedraft',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['media_id'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['getdraft'] = array(
            'title' => '获取草稿',
            'action' => 'getdraft',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['media_id'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['publishdraft'] = array(
            'title' => '发布草稿',
            'action' => 'publishdraft',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['media_id']) && empty($row['publish_id'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['getdraftpublishinfo'] = array(
            'title' => '草稿发布状态轮询',
            'action' => 'getdraftpublishinfo',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['media_id']) && !empty($row['publish_id'])
                ) {
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
     * @title({name="获取草稿列表"})
     *
     * @name 获取草稿列表
     */
    public function batchgetdraftAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/draft/batchgetdraft?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                // 单选框 type 是 草稿的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
                $fields['draft_type'] = array(
                    'name' => '媒体文件类型',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'select',
                        'is_show' => true,
                        'items' => $this->mediaTypeItems
                    ),
                );
                //offset 是 从全部草稿的该偏移位置开始返回，0表示从第一个草稿 返回
                $fields['draft_offset'] = array(
                    'name' => '偏移位置开始返回,0表示从第一个草稿',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'number',
                        'is_show' => true
                    ),
                );
                //count 是 返回草稿的数量，取值在1到20之间
                //$this->text('draft_count', '返回草稿的数量,取值在1到20之间')->value(20);

                $title = "获取草稿列表";
                $row = array();
                // 初始值
                $row['draft_offset'] = 0;
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('draft_component_appid'));
                $authorizer_appid = trim($this->request->get('draft_authorizer_appid'));
                $type = trim($this->request->get('draft_type'));
                $offset = intval($this->request->get('draft_offset'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                if (empty($type)) {
                    return $this->makeJsonError("媒体文件类型未设定");
                }
                if ($offset < 0) {
                    return $this->makeJsonError("偏移位置未设定");
                }
                $weixinopenService = new \App\Weixin2\Services\WeixinService($authorizer_appid, $component_appid);
                $res = $weixinopenService->getWeixinObject()
                    ->getdraftManager()
                    ->batchGetdraft($type, $offset, 20);
                // if (empty($res['errcode'])) {
                //     return print_r($res);
                //     return true;
                // }
                // return 'errcode:' . $res['errcode'] . '  msg:' . $res['errmsg'];
                if (empty($res['errcode'])) {
                    return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
                } else {
                    return $this->makeJsonError($res['errmsg']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取草稿总数"})
     *
     * @name 获取草稿总数
     */
    public function getdraftcountAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/draft/getdraftcount?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取草稿总数";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('draft_component_appid'));
                $authorizer_appid = trim($this->request->get('draft_authorizer_appid'));
                $user_id = trim($this->request->get('draft_user_id'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                $weixinopenService = new \App\Weixin2\Services\WeixinService($authorizer_appid, $component_appid);
                $res = $weixinopenService->getWeixinObject()
                    ->getdraftManager()
                    ->getdraftCount();
                // if (empty($res['errcode'])) {
                //     return print_r($res);
                //     return true;
                // }
                // return 'errcode:' . $res['errcode'] . '  msg:' . $res['errmsg'];
                if (empty($res['errcode'])) {
                    return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
                } else {
                    return $this->makeJsonError($res['errmsg']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="生成草稿"})
     *
     * @name 生成草稿
     */
    public function adddraftAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/draft/adddraft?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modeldraft->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->adddraft($data);

            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="删除草稿"})
     *
     * @name 删除草稿
     */
    public function deletedraftAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/draft/deletedraft?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modeldraft->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->deletedraft($data);
            return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取草稿"})
     *
     * @name 获取草稿
     */
    public function getdraftAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/draft/getdraft?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modeldraft->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->getWeixinObject()
                ->getdraftManager()
                ->getdraft($data['media_id']);
            if (empty($res['errcode'])) {
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            } else {
                return $this->makeJsonError($res['errmsg']);
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="发布草稿"})
     *
     * @name 发布草稿
     */
    public function publishdraftAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/draft/publishdraft?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modeldraft->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->publishDraft($data);
            if (empty($res['errcode'])) {
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            } else {
                return $this->makeJsonError($res['errmsg']);
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="草稿发布状态轮询"})
     *
     * @name 草稿发布状态轮询
     */
    public function getdraftpublishinfoAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/draft/getdraftpublishinfo?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modeldraft->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->getDraftPublishInfo($data);
            if (empty($res['errcode'])) {
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            } else {
                return $this->makeJsonError($res['errmsg']);
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
    
    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['draft_component_appid'] = array(
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
        $fields['draft_authorizer_appid'] = array(
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
        $schemas['name'] = array(
            'name' => '草稿箱名称',
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
        $schemas['media_id'] = array(
            'name' => '上传后的获取标志media_id',
            'data' => array(
                'type' => 'string',
                'length' => 190,
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
        $schemas['media_time'] = array(
            'name' => '草稿箱上传时间',
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
        $schemas['publish_id'] = array(
            'name' => '发布任务的id',
            'data' => array(
                'type' => 'string',
                'length' => 190,
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
        $schemas['msg_data_id'] = array(
            'name' => '消息的数据ID',
            'data' => array(
                'type' => 'string',
                'length' => 190,
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
        $schemas['publish_time'] = array(
            'name' => '发布任务时间',
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
        return $schemas;
    }

    protected function getName()
    {
        return '草稿箱';
    }

    protected function getModel()
    {
        return $this->modelDraft;
    }
}
