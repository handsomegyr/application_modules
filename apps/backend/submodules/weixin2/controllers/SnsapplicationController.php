<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\SnsApplication;
use App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer;
use App\Backend\Submodules\Weixin2\Models\Component\Component;

/**
 * @title({name="授权应用设置"})
 *
 * @name 授权应用设置
 */
class SnsapplicationController extends \App\Backend\Controllers\FormController
{
    private $modelSnsApplication;
    private $modelAuthorizer;
    private $modelComponent;

    public function initialize()
    {
        $this->modelSnsApplication = new SnsApplication();
        $this->modelAuthorizer = new Authorizer();
        $this->modelComponent = new Component();

        $this->componentItems = $this->modelComponent->getAll();
        $this->authorizerItems = $this->modelAuthorizer->getAll();
        parent::initialize();
    }
    protected $componentItems = null;
    protected $authorizerItems = null;

    protected function getFormTools2($tools)
    {
        $tools['encodehexappid'] = array(
            'title' => '生成应用ID',
            'action' => 'encodehexappid',
            'is_show' => function ($row) {
                if (!empty($row) && empty($row['appid'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['encodehexappkey'] = array(
            'title' => '重新生成应用密钥',
            'action' => 'encodehexappkey',
            'is_show' => function ($row) {
                if (!empty($row)) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['encodehexsecretkey'] = array(
            'title' => '重新生成签名密钥',
            'action' => 'encodehexsecretkey',
            'is_show' => function ($row) {
                if (!empty($row)) {
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
     * @title({name="生成应用ID"})
     *
     * @name 生成应用ID
     */
    public function encodehexappidAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/snsapplication/encodehexappid?id=xxx
        try {
            

            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelSnsApplication->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $appid = \iHashids::encodeHex($id);
            $updateData = array();
            $updateData['appid'] = $appid;
            $this->modelSnsApplication->update(array('_id' => $id), array('$set' => $updateData));
            return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
    /**
     * @title({name="重新生成应用密钥"})
     *
     * @name 重新生成应用密钥
     */
    public function encodehexappkeyAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/snsapplication/encodehexappkey?id=xxx
        try {
            

            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelSnsApplication->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $appkey = \iHashids::encodeHex(time());
            $updateData = array();
            $updateData['appkey'] = $appkey;
            $this->modelSnsApplication->update(array('_id' => $id), array('$set' => $updateData));

            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
    /**
     * @title({name="重新生成签名密钥"})
     *
     * @name 重新生成签名密钥
     */
    public function encodehexsecretkeyAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/snsapplication/encodehexsecretkey?id=xxx
        try {
            

            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelSnsApplication->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $secretKey = \iHashids::encodeHex(\uniqid());
            $updateData = array();
            $updateData['secretKey'] = $secretKey;
            $this->modelSnsApplication->update(array('_id' => $id), array('$set' => $updateData));
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {        $schemas['component_appid'] = array(
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
        $schemas['name'] = array(
            'name' => '应用名称',
            'data' => array(
                'type' => 'string',
                'length' => 100,
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
        $schemas['appid'] = array(
            'name' => '应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 100,
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
        $schemas['appkey'] = array(
            'name' => '应用密钥',
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
        $schemas['secretKey'] = array(
            'name' => '签名密钥',
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
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['start_time'] = array(
            'name' => '开始时间',
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
        $schemas['end_time'] = array(
            'name' => '结束时间',
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
        $schemas['is_active'] = array(
            'name' => '是否开启',
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
        $schemas['is_ip_check'] = array(
            'name' => '是否开启IP检查',
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
        $schemas['is_cb_url_check'] = array(
            'name' => '是否开启回调域名检查',
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

        return $schemas;
    }

    protected function getName()
    {
        return '授权应用设置';
    }

    protected function getModel()
    {
        return $this->modelSnsApplication;
    }
}
