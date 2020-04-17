<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Kf\Session;
use App\Backend\Submodules\Weixin2\Models\Kf\Account;

/**
 * @title({name="客服会话"})
 *
 * @name 客服会话
 */
class KfsessionController extends BaseController
{
    private $modelSession;
    private $modelAccount;
    public function initialize()
    {
        $this->modelSession = new Session();
        $this->modelAccount = new Account();
        $this->accountItems = $this->modelAccount->getAll();
        parent::initialize();
    }
    protected $accountItems = null;

    protected function getHeaderTools2($tools)
    {
        $tools['getwaitcase'] = array(
            'title' => '获取未接入会话列表',
            'action' => 'getwaitcase',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getFormTools2($tools)
    {
        $tools['closesession'] = array(
            'title' => '关闭会话',
            'action' => 'closesession',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) &&
                    !empty($row['kf_account']) && !empty($row['openid']) && !empty($row['is_created'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['createsession'] = array(
            'title' => '创建会话',
            'action' => 'createsession',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) &&
                    !empty($row['kf_account']) && !empty($row['openid']) && empty($row['is_created'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['getsession'] = array(
            'title' => '获取客户会话状态',
            'action' => 'getsession',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) &&
                    !empty($row['kf_account']) && !empty($row['openid'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['getsessionlist'] = array(
            'title' => '获取客服会话列表',
            'action' => 'getsessionlist',
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) && !empty($row['kf_account'])) {
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
     * @title({name="获取未接入会话列表"})
     *
     * @name 获取未接入会话列表
     */
    public function getwaitcaseAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/kfsession/getwaitcase?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();
                $title = "获取未接入会话列表";
                $row = array();
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('kfsession_component_appid'));
                $authorizer_appid = trim($this->request->get('kfsession_authorizer_appid'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }

                $weixinopenService = new \App\Weixin2\Services\Service1($authorizer_appid, $component_appid);
                $res = $weixinopenService->getWeixinObject()
                    ->getCustomServiceManager()
                    ->kfsessionGetWaitCase();
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
     * @title({name="关闭会话"})
     *
     * @name 关闭会话
     */
    public function closesessionAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/kfsession/closesession?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelSession->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->closeKfSession($id);

            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="创建会话"})
     *
     * @name 创建会话
     */
    public function createsessionAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/kfsession/createsession?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelSession->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->createKfSession($id);
            return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取客户会话状态"})
     *
     * @name 获取客户会话状态
     */
    public function getsessionAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/kfsession/getsession?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelSession->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->getWeixinObject()
                ->getCustomServiceManager()
                ->kfsessionGetSession($data['openid']);
            if (empty($res['errcode'])) {
                return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            } else {
                return $this->makeJsonError($res['errmsg']);
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取客服会话列表"})
     *
     * @name 获取客服会话列表
     */
    public function getsessionlistAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/kfsession/getsessionlist?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelSession->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\Service1($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->getWeixinObject()
                ->getCustomServiceManager()
                ->kfsessionGetSessionList($data['kf_account']);
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
        $fields['kfsession_component_appid'] = array(
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
        $fields['kfsession_authorizer_appid'] = array(
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
        $schemas['kf_account'] = array(
            'name' => '完整客服账号',
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
                'help' => '完整客服账号，格式为：账号前缀@公众号微信号',
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
            'name' => '粉丝的openid',
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
        $schemas['is_created'] = array(
            'name' => '是否是创建客服会话',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => false
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
        $schemas['kfsession_time'] = array(
            'name' => '客服会话创建时间',
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
        return '客服会话';
    }

    protected function getModel()
    {
        return $this->modelSession;
    }
}
