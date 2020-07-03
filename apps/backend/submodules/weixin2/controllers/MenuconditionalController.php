<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Menu\Conditional;
use App\Backend\Submodules\Weixin2\Models\Menu\Type;
use App\Backend\Submodules\Weixin2\Models\Menu\ConditionalMatchrule;

/**
 * @title({name="个性化菜单设置"})
 *
 * @name 个性化菜单设置
 */
class MenuconditionalController extends BaseController
{
    private $modelConditional;
    private $modelMenuType;
    private $modelConditionalMatchrule;
    public function initialize()
    {
        $this->modelConditional = new Conditional();
        $this->modelMenuType = new Type();
        $this->modelConditionalMatchrule = new ConditionalMatchrule();
        $this->menuTypeItems = $this->modelMenuType->getAll();
        $this->conditionalMatchruleItems = $this->modelConditionalMatchrule->getAll();
        parent::initialize();
    }
    protected $menuTypeItems = null;
    protected $conditionalMatchruleItems = null;

    protected function getHeaderTools2($tools)
    {
        $tools['getmenu'] = array(
            'title' => '查询个性化菜单',
            'action' => 'getmenu',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        $tools['trymatch'] = array(
            'title' => '测试个性化菜单匹配结果',
            'action' => 'trymatch',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['addconditional'] = array(
            'title' => '生成个性化菜单',
            'action' => 'addconditional',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) &&
                    empty($row['menuid']) && !empty($row['matchrule'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['deleteconditional'] = array(
            'title' => '删除个性化菜单',
            'action' => 'deleteconditional',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) &&
                    !empty($row['menuid']) && !empty($row['matchrule'])
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
     * @title({name="查询个性化菜单"})
     *
     * @name 查询个性化菜单
     */
    public function getmenuAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/menuconditional/getmenu?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "查询个性化菜单";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('menuconditional_component_appid'));
                $authorizer_appid = trim($this->request->get('menuconditional_authorizer_appid'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                $weixinopenService = new \App\Weixin2\Services\WeixinService($authorizer_appid, $component_appid);
                $res = $weixinopenService->getWeixinObject()
                    ->getMenuManager()
                    ->get();
                // if (empty($res['errcode'])) {
                //     return print_r($res); // true;
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
     * @title({name="测试个性化菜单匹配结果"})
     *
     * @name 测试个性化菜单匹配结果
     */
    public function trymatchAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/menuconditional/trymatch?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $fields['menuconditional_user_id'] = array(
                    'name' => '测试用户微信名',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true
                    ),
                );
                $title = "测试个性化菜单匹配结果";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('menuconditional_component_appid'));
                $authorizer_appid = trim($this->request->get('menuconditional_authorizer_appid'));
                $user_id = trim($this->request->get('menuconditional_user_id'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                if (empty($user_id)) {
                    return $this->makeJsonError("测试用户微信名未设定");
                }
                $weixinopenService = new \App\Weixin2\Services\WeixinService($authorizer_appid, $component_appid);
                //$user_id = "Guo-YR";
                $res = $weixinopenService->getWeixinObject()
                    ->getMenuManager()
                    ->trymatch($user_id);
                // if (empty($res['errcode'])) {
                //     return print_r($res); // true;
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
     * @title({name="生成个性化菜单"})
     *
     * @name 生成个性化菜单
     */
    public function addconditionalAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/menuconditional/addconditional?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelConditional->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->createConditionalMenu($data['matchrule']);

            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="删除个性化菜单"})
     *
     * @name 删除个性化菜单
     */
    public function deleteconditionalAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/menuconditional/deleteconditional?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelConditional->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->deleteConditionalMenu($data['matchrule'], $data['menuid']);
            return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['menuconditional_component_appid'] = array(
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
        $fields['menuconditional_authorizer_appid'] = array(
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
        
        $schemas['type'] = array(
            'name' => '菜单的响应动作类型',
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
                'items' => $this->menuTypeItems,
                'help' => '菜单的响应动作类型，view表示网页类型，click表示点击类型，miniprogram表示小程序类型',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->menuTypeItems,
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    $column->style('width:10%;word-break:break-all;');
                }
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->menuTypeItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['name'] = array(
            'name' => '菜单标题',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '菜单标题，不超过16个字节，子菜单不超过60个字节',
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
        $schemas['key'] = array(
            'name' => '菜单KEY值',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '菜单KEY值，用于消息接口推送，不超过128字节,click等点击类型必须',
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
            'name' => '网页链接',
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
                'help' => '网页链接，用户点击菜单可打开链接，不超过1024字节。view、miniprogram类型必须',
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
            'name' => '素材ID',
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
                'help' => '素材ID,调用新增永久素材接口返回的合法media_id, media_id类型和view_limited类型必须',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    $column->style('width:10%;word-break:break-all;');
                }
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
                'help' => '小程序的appid（仅认证公众号可配置）,miniprogram类型必须',
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
                'help' => '小程序的页面路径,miniprogram类型必须',
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
        $schemas['parent'] = array(
            'name' => '上级菜单',
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
                'items' => $this->modelConditional->getList4Tree('')
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->modelConditional->getList4Tree('')
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['priority'] = array(
            'name' => '权重',
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
                'help' => '权重，用于显示顺序',
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
        $schemas['matchrule'] = array(
            'name' => '菜单匹配规则',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->conditionalMatchruleItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->conditionalMatchruleItems
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['menuid'] = array(
            'name' => '菜单id',
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
        $schemas['menu_time'] = array(
            'name' => '菜单生成时间',
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
        return '个性化菜单设置';
    }

    protected function getModel()
    {
        return $this->modelConditional;
    }
}
