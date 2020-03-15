<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\DataCube\UserShareHour;
use App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer;
use App\Backend\Submodules\Weixin2\Models\Component\Component;
use App\Backend\Submodules\Weixin2\Models\RefHour;

/**
 * @title({name="图文分享转发分时数据"})
 *
 * @name 图文分享转发分时数据
 */
class DatacubeusersharehourController extends \App\Backend\Controllers\FormController
{
    private $modelUserShareHour;
    private $modelAuthorizer;
    private $modelComponent;
    private $modelRefHour;
    public function initialize()
    {
        $this->modelUserShareHour = new UserShareHour();
        $this->modelAuthorizer = new Authorizer();
        $this->modelComponent = new Component();
        $this->modelRefHour = new RefHour();

        $this->componentItems = $this->modelComponent->getAll();
        $this->authorizerItems = $this->modelAuthorizer->getAll();
        $this->refHourItems = $this->modelRefHour->getAll();
        parent::initialize();
    }
    protected $componentItems = null;
    protected $authorizerItems = null;
    protected $refHourItems = null;

    protected function getHeaderTools2($tools)
    {
        $tools['syncusersharehour'] = array(
            'title' => '获取图文分享转发分时数据',
            'action' => 'syncusersharehour',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="获取图文分享转发分时数据"})
     *
     * @name 获取图文分享转发分时数据
     */
    public function syncusersharehourAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/datacubeusersharehour/syncusersharehour?id=xxx
        try {
            $this->view->disable();

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                // 起始日期
                $fields['usersharehour_start_ref_date'] = array(
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
                $fields['usersharehour_end_ref_date'] = array(
                    'name' => '结束日期',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'datetimepicker',
                        'is_show' => true
                    ),
                );
                $title = "获取图文分享转发分时数据";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('usersharehour_component_appid'));
                $authorizer_appid = trim($this->request->get('usersharehour_authorizer_appid'));
                $start_ref_date = trim($this->request->get('usersharehour_start_ref_date'));
                $end_ref_date = trim($this->request->get('usersharehour_end_ref_date'));
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

                $weixinopenService = new \App\Weixin2\Services\Service1($authorizer_appid, $component_appid);
                $res = $weixinopenService->syncUserShareHour($start_ref_date, $end_ref_date);
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['usersharehour_component_appid'] = array(
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
        $fields['usersharehour_authorizer_appid'] = array(
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

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
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
        $schemas['ref_hour'] = array(
            'name' => '数据的小时',
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
                'items' => $this->refHourItems,
                'help' => '数据的小时，包括从000到2300，分别代表的是[000,100)到[2300,2400)，即每日的第1小时和最后1小时',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->refHourItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->refHourItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        // 分享的场景 1代表好友转发 2代表朋友圈 3代表腾讯微博 255代表其他
        $shareSceneOptions = array();
        $shareSceneOptions["1"] = "1:好友转发";
        $shareSceneOptions["2"] = "2:朋友圈";
        $shareSceneOptions["3"] = "3:腾讯微博";
        $shareSceneOptions["255"] = "255:其他";

        $schemas['share_scene'] = array(
            'name' => '分享的场景',
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
                'items' => $shareSceneOptions,
                'help' => '分享的场景 1代表好友转发 2代表朋友圈 3代表腾讯微博 255代表其他',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $shareSceneOptions,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $shareSceneOptions,
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

        return $schemas;
    }

    protected function getName()
    {
        return '图文分享转发分时数据';
    }

    protected function getModel()
    {
        return $this->modelUserShareHour;
    }
}
