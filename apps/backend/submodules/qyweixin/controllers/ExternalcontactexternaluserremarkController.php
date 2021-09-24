<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\ExternalUserRemark;

/**
 * @title({name="修改客户备注信息"})
 *
 * @name 修改客户备注信息
 */
class ExternalcontactexternaluserremarkController extends BaseController
{
    private $modelExternalcontactExternalUserRemark;

    public function initialize()
    {
        $this->modelExternalcontactExternalUserRemark = new ExternalUserRemark();
        parent::initialize();
    }

    protected function addCustomTool4Form(\Encore\Admin\Form $form)
    {
        $model = $form->model();
        //request()->route()=>{"uri":"admin\/order\/order\/{order}\/edit","methods":["GET","HEAD"],"action":{"middleware":["web","App\\Http\\Middleware\\VerifyCsrfToken","admin"],"as":"order.edit","uses":"App\\Admin\\Controllers\\Order\\OrderController@edit","controller":"App\\Admin\\Controllers\\Order\\OrderController@edit","namespace":"App\\Admin\\Controllers","prefix":"admin\/order","where":[]},"isFallback":false,"controller":{},"defaults":[],"wheres":[],"parameters":{"order":"6"},"parameterNames":["order"],"computedMiddleware":["web","App\\Http\\Middleware\\VerifyCsrfToken","admin"],"compiled":{}}
        $id = request()->route("ecexternaluserremark");

        $form->tools(function (\Encore\Admin\Form\Tools $tools) use ($model, $id) {
            if (!empty($id)) {
                $model = $model->find($id);
                $weixinopenService = new \App\Components\Qyweixin\Services\QyService($model->authorizer_appid, $model->provider_appid, 0);

                if ((!empty($model->remark_pic_media) && $weixinopenService->isMediaTimeExpired($model->remark_pic_mediaid, $model->remark_pic_media_created_at))) {
                    $action = new \App\Admin\Controllers\Qyweixin\Actions\ExternalContact\ExternalUserRemark\UploadMedia();
                    $action->setName('上传临时素材');
                    $action->setModel($model);
                    $tools->append($action);
                }

                if (!empty($model->userid) && !empty($model->external_userid)) {
                    $action = new \App\Admin\Controllers\Qyweixin\Actions\ExternalContact\ExternalUserRemark\RemarkExternalUser();
                    $action->setName('修改客户备注信息');
                    $action->setModel($model);
                    $tools->append($action);
                }
            }
        });
    }

    protected function getFormTools2($tools)
    {
        $tools['uploadmedia'] = array(
            'title' => '上传临时素材',
            'action' => 'uploadmedia',
            'is_show' => function ($row) {
                $weixinopenService = new \App\Qyweixin\Services\QyService($row['authorizer_appid'], $row['provider_appid'], 0);
                if ((!empty($row['remark_pic_media']) && $weixinopenService->isMediaTimeExpired($row['remark_pic_mediaid'], $row['remark_pic_media_created_at']))) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['remarkexternaluser'] = array(
            'title' => '修改客户备注信息',
            'action' => 'remarkexternaluser',
            'is_show' => function ($row) {
                if (!empty($row['userid']) && !empty($row['external_userid'])) {
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
     * @title({name="上传临时素材"})
     *
     * @name 上传临时素材
     */
    public function uploadmediaAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactexternaluserremark/uploadmedia?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactExternalUserRemark->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "上传临时素材";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = trim($this->request->get('external_user_remark_img_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }

                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                if (empty($data['remark_pic_media_created_at'])) {
                    $image_media_created_at = '2020-01-01 00:00:00';
                } else {
                    $image_media_created_at = $data['remark_pic_media_created_at'];
                }
                if (!empty($data['remark_pic_media'])) {
                    $res = $weixinopenService->uploadMediaByApi($data['remark_pic_media'], 'image', $data['remark_pic_mediaid'], $image_media_created_at);
                    // 发生了改变就更新
                    if ($res['media_id'] != $data['remark_pic_mediaid']) {
                        $updateData = array();
                        $updateData['remark_pic_mediaid'] = $res['media_id'];
                        $updateData['remark_pic_media_created_at'] = getCurrentTime($res['created_at']);
                        $this->modelExternalcontactExternalUserRemark->update(array('_id' => $id), array('$set' => $updateData));
                    }
                }

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \\App\Common\Utils\Helper::myJsonEncode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
    /**
     * @title({name="修改客户备注信息"})
     *
     * @name 修改客户备注信息
     */
    public function remarkexternaluserAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactexternaluserremark/remarkexternaluser?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactExternalUserRemark->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "修改客户备注信息";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = trim($this->request->get('external_user_remark_img_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->remarkExternalUser($data);

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \\App\Common\Utils\Helper::myJsonEncode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4FormTool()
    {
        $fields = array();
        $fields['external_user_remark_img_rec_id'] = array(
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
        $fields['external_user_remark_img_name'] = array(
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
        $fields['external_user_remark_img_agentid'] = array(
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
            'name' => '外部联系人的userid列表',
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
        $schemas['remark'] = array(
            'name' => '备注信息',
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
                'help' => '此用户对外部联系人的备注',
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
        $schemas['description'] = array(
            'name' => '描述',
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
                'help' => '此用户对外部联系人的描述',
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
        $schemas['remark_company'] = array(
            'name' => '公司名称',
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
                'help' => '此用户对外部联系人备注的所属公司名称',
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
        $schemas['remark_mobiles'] = array(
            'name' => '备注的手机号',
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
                'items' => '',
                'help' => '此用户对外部联系人备注的手机号',
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
        $schemas['remark_pic_media'] = array(
            'name' => '备注图片',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true,
                'items' => '',
                'help' => '图片',
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
        $schemas['remark_pic_mediaid'] = array(
            'name' => '备注图片的mediaid',
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
                'help' => '备注图片的mediaid,可以通过素材管理接口获得',
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
        $schemas['remark_pic_media_created_at'] = array(
            'name' => '创建备注图片的media_id的时间',
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
        $schemas['update_remark_time'] = array(
            'name' => '修改备注时间',
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
        return '修改客户备注信息';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactExternalUserRemark;
    }
}
