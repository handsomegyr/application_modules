<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Material\Material;
use App\Backend\Submodules\Weixin2\Models\Media\Type;

/**
 * @title({name="永久素材设置"})
 *
 * @name 永久素材设置
 */
class MaterialController extends BaseController
{
    private $modelMaterial;
    private $modelMediaType;
    public function initialize()
    {
        $this->modelMaterial = new Material();
        $this->modelMediaType = new Type();
        $this->mediaTypeItems = $this->modelMediaType->getAll();
        parent::initialize();
    }
    protected $mediaTypeItems = null;

    protected function getHeaderTools2($tools)
    {
        $tools['batchgetmaterial'] = array(
            'title' => '获取素材列表',
            'action' => 'batchgetmaterial',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        $tools['getmaterialcount'] = array(
            'title' => '获取素材总数',
            'action' => 'getmaterialcount',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['addmaterial'] = array(
            'title' => '生成永久素材',
            'action' => 'addmaterial',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) &&
                    !empty($row['type']) && !empty($row['media']) && empty($row['media_id']) && ($row['type'] != "news")
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['addnews'] = array(
            'title' => '新增永久图文素材',
            'action' => 'addnews',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) &&
                    !empty($row['type']) && empty($row['media_id']) && ($row['type'] == "news")
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['updatenews'] = array(
            'title' => '修改永久图文素材',
            'action' => 'updatenews',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) &&
                    !empty($row['type']) && !empty($row['media_id']) && ($row['type'] == "news")
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['deletematerial'] = array(
            'title' => '删除永久素材',
            'action' => 'deletematerial',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) && !empty($row['media_id'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['getmaterial'] = array(
            'title' => '获取永久素材',
            'action' => 'getmaterial',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) && !empty($row['media_id'])
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
     * @title({name="获取素材列表"})
     *
     * @name 获取素材列表
     */
    public function batchgetmaterialAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/material/batchgetmaterial?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                // 单选框 type 是 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
                $fields['material_type'] = array(
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
                //offset 是 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
                $fields['material_offset'] = array(
                    'name' => '偏移位置开始返回,0表示从第一个素材',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'number',
                        'is_show' => true
                    ),
                );
                //count 是 返回素材的数量，取值在1到20之间
                //$this->text('material_count', '返回素材的数量,取值在1到20之间')->value(20);

                $title = "获取素材列表";
                $row = array();
                // 初始值
                $row['material_offset'] = 0;
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('material_component_appid'));
                $authorizer_appid = trim($this->request->get('material_authorizer_appid'));
                $type = trim($this->request->get('material_type'));
                $offset = intval($this->request->get('material_offset'));
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
                $weixinopenService = new \App\Weixin2\Services\Service1($authorizer_appid, $component_appid);
                $res = $weixinopenService->getWeixinObject()
                    ->getMaterialManager()
                    ->batchGetMaterial($type, $offset, 20);
                // if (empty($res['errcode'])) {
                //     return print_r($res);
                //     return true;
                // }
                // return 'errcode:' . $res['errcode'] . '  msg:' . $res['errmsg'];
                if (empty($res['errcode'])) {
                    return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
                } else {
                    return $this->makeJsonError($res['errmsg']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取素材总数"})
     *
     * @name 获取素材总数
     */
    public function getmaterialcountAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/material/getmaterialcount?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取素材总数";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('material_component_appid'));
                $authorizer_appid = trim($this->request->get('material_authorizer_appid'));
                $user_id = trim($this->request->get('material_user_id'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                $weixinopenService = new \App\Weixin2\Services\Service1($authorizer_appid, $component_appid);
                $res = $weixinopenService->getWeixinObject()
                    ->getMaterialManager()
                    ->getMaterialCount();
                // if (empty($res['errcode'])) {
                //     return print_r($res);
                //     return true;
                // }
                // return 'errcode:' . $res['errcode'] . '  msg:' . $res['errmsg'];
                if (empty($res['errcode'])) {
                    return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
                } else {
                    return $this->makeJsonError($res['errmsg']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="生成永久素材"})
     *
     * @name 生成永久素材
     */
    public function addmaterialAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/material/addmaterial?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelMaterial->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->addMaterial($id);

            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="新增永久图文素材"})
     *
     * @name 新增永久图文素材
     */
    public function addnewsAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/material/addnews?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelMaterial->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->addNews($id);
            return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="修改永久图文素材"})
     *
     * @name 修改永久图文素材
     */
    public function updatenewsAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/material/updatenews?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelMaterial->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->updateNews($id);
            return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="删除永久素材"})
     *
     * @name 删除永久素材
     */
    public function deletematerialAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/material/deletematerial?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelMaterial->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->deleteMaterial($id);
            return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取永久素材"})
     *
     * @name 获取永久素材
     */
    public function getmaterialAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/material/getmaterial?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelMaterial->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->getWeixinObject()
                ->getMaterialManager()
                ->getMaterial($data->media_id);
            if (empty($res['errcode'])) {
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
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
        $fields['material_component_appid'] = array(
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
        $fields['material_authorizer_appid'] = array(
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
        $schemas['agentid'] = array(
            'name' => '代理应用ID',
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
        $schemas['name'] = array(
            'name' => '素材名',
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
        $schemas['type'] = array(
            'name' => '媒体文件类型',
            'data' => array(
                'type' => 'string',
                'length' => 10,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->mediaTypeItems,
                'help' => '媒体文件类型，分别有图片（image）、语音（voice）、视频（video）、缩略图（thumb）和图文（news）'
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->mediaTypeItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->mediaTypeItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['media'] = array(
            'name' => '媒体文件',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelMaterial->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    //display()方法来通过传入的回调函数来处理当前列的值：
                    return $column->display(function () use ($column) {

                        // 如果这一列的status字段的值等于1，直接显示title字段
                        if ($this->type == 'image' || $this->type == 'thumb') {
                            return $column->image("", 50, 50);
                        } else {
                            return $column->downloadable();
                        }
                    });
                }
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['title'] = array(
            'name' => '素材的标题',
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
                'items' => '',
                'help' => '素材的标题，当类型为视频（video）有用',
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
        $schemas['introduction'] = array(
            'name' => '素材的描述',
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
                'items' => '',
                'help' => '素材的描述，当类型为视频（video）有用',
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
        $schemas['media_id'] = array(
            'name' => '永久素材的media_id',
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
        $schemas['url'] = array(
            'name' => '图片素材的图片URL',
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
                'help' => '图片素材的图片URL（仅新增图片素材时会返回该字段）',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['media_time'] = array(
            'name' => '永久素材生成时间',
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
        $schemas['delete_media_time'] = array(
            'name' => '永久素材删除时间',
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
        return '永久素材设置';
    }

    protected function getModel()
    {
        return $this->modelMaterial;
    }
}
