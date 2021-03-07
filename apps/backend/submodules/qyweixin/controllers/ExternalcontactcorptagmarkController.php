<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\CorpTagMark;

/**
 * @title({name="编辑客户企业标签"})
 *
 * @name 编辑客户企业标签
 */
class ExternalcontactcorptagmarkController extends BaseController
{
    private $modelExternalcontactCorpTagMark;

    public function initialize()
    {
        $this->modelExternalcontactCorpTagMark = new CorpTagMark();
        parent::initialize();
    }

    protected function getFormTools2($tools)
    {
        $tools['marktag'] = array(
            'title' => '编辑客户企业标签',
            'action' => 'marktag',
            'is_show' => function ($row) {
                if (!empty($row['userid']) && !empty($row['external_userid']) && (!empty($row['add_tag']) || !empty($row['remove_tag']))) {
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
     * @title({name="编辑客户企业标签"})
     *
     * @name 编辑客户企业标签
     */
    public function marktagAction()
    {
        // http://www.myapplicationmodule.com.com/admin/qyweixin/externalcontactcorptagmark/marktag?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactCorpTagMark->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "获取加入企业二维码";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = trim($this->request->get('corptagmark_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->markCorpTag($data);

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4FormTool()
    {
        $fields = array();
        $fields['corptagmark_rec_id'] = array(
            'name' => 'ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );
        $fields['external_user_id'] = array(
            'name' => '企业成员的userid',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );
        $fields['external_external_userid'] = array(
            'name' => '外部联系人的userid',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );

        $fields['corptagmark_name'] = array(
            'name' => '名称',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
            ),
        );

        $fields['corptagmark_agentid'] = array(
            'name' => '微信企业应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->agentItems,
            ),
        );

        return $fields;
    }

    protected function getSchemas2($schemas)
    {
        $schemas['provider_appid'] = array(
            'name' => '第三方服务商应用ID',
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
                'items' => $this->providerItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->providerItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->providerItems
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
            'name' => '名称',
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
        $schemas['userid'] = array(
            'name' => '企业成员的userid',
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
        $schemas['external_userid'] = array(
            'name' => '外部联系人的userid',
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
        $schemas['add_tag'] = array(
            'name' => '要标记的标签列表',
            'data' => array(
                'type' => 'array',
                'length' => 1024,
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
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
        $schemas['remove_tag'] = array(
            'name' => '要移除的标签列表',
            'data' => array(
                'type' => 'array',
                'length' => 1024,
                'defaultValue' => '[]'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
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
        $schemas['mark_tag_time'] = array(
            'name' => '编辑客户企业标签时间',
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
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
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

        return $schemas;
    }

    protected function getName()
    {
        return '编辑客户企业标签';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactCorpTagMark;
    }
}
