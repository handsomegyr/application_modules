<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\User\UserToUserTag;
use App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer;
use App\Backend\Submodules\Weixin2\Models\Component\Component;

/**
 * @title({name="用户和用户标签对应设置"})
 *
 * @name 用户和用户标签对应设置
 */
class UsertousertagController extends \App\Backend\Controllers\FormController
{
    private $modelUserToUserTag;
    private $modelAuthorizer;
    private $modelComponent;

    public function initialize()
    {
        $this->modelUserToUserTag = new UserToUserTag();
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
        $tools['untaguser'] = array(
            'title' => '取消标签',
            'action' => 'untaguser',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) &&
                    !empty($row['openid']) && !empty($row['tag_id']) && !empty($row['is_tag'])
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['taguser'] = array(
            'title' => '用户打标签',
            'action' => 'taguser',
            'is_show' => function ($row) {
                if (
                    !empty($row) && !empty($row['authorizer_appid']) && !empty($row['component_appid']) &&
                    !empty($row['openid']) && !empty($row['tag_id']) && empty($row['is_tag'])
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
     * @title({name="取消标签"})
     *
     * @name 取消标签
     */
    public function untaguserAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/usertousertag/untaguser?id=xxx
        try {
            

            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelUserToUserTag->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->untagUser($id);

            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
    /**
     * @title({name="用户打标签"})
     *
     * @name 用户打标签
     */
    public function taguserAction()
    {
        // http://www.applicationmodule.com/admin/weixin2/usertousertag/taguser?id=xxx
        try {
            

            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelUserToUserTag->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $weixinopenService = new \App\Weixin2\Services\WeixinService($data['authorizer_appid'], $data['component_appid']);
            $res = $weixinopenService->tagUser($id);

            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
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
        $schemas['openid'] = array(
            'name' => '用户的标识',
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
                'help' => '用户的标识，微信用户ID',
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
        $schemas['tag_id'] = array(
            'name' => '微信标签ID',
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
        $schemas['is_tag'] = array(
            'name' => '是否打过标签',
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

        $schemas['tag_time'] = array(
            'name' => '打标签时间',
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
        $schemas['untag_time'] = array(
            'name' => '取消标签时间',
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
        return '用户和用户标签对应设置';
    }

    protected function getModel()
    {
        return $this->modelUserToUserTag;
    }
}
