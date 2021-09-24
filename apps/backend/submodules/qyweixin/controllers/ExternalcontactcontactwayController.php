<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\ContactWay;

/**
 * @title({name="客户联系「联系我」管理"})
 *
 * @name 客户联系「联系我」管理
 */
class ExternalcontactcontactwayController extends BaseController
{
    private $modelExternalcontactContactWay;

    public function initialize()
    {
        $this->modelExternalcontactContactWay = new ContactWay();
        parent::initialize();
    }

    protected function getFormTools2($tools)
    {
        $tools['uploadmedia'] = array(
            'title' => '上传临时素材',
            'action' => 'uploadmedia',
            'is_show' => function ($row) {
                $weixinopenService = new \App\Qyweixin\Services\QyService($row['authorizer_appid'], $row['provider_appid'], 0);
                if (
                    (!empty($row['conclusions_image_media']) && $weixinopenService->isMediaTimeExpired($row['conclusions_image_media_id'], $row['conclusions_image_media_created_at'])) ||
                    (!empty($row['conclusions_miniprogram_pic_media']) && $weixinopenService->isMediaTimeExpired($row['conclusions_miniprogram_pic_media_id'], $row['conclusions_miniprogram_pic_media_created_at']))
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['uploadmediaimg'] = array(
            'title' => '上传图片',
            'action' => 'uploadmediaimg',
            'is_show' => function ($row) {
                if (!empty($row['conclusions_image_media']) && empty($row['conclusions_image_pic_url'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['addcontactway'] = array(
            'title' => '配置客户联系「联系我」方式',
            'action' => 'addcontactway',
            'is_show' => function ($row) {
                if (!empty($row['config_id'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['getcontactway'] = array(
            'title' => '获取客户联系「联系我」方式',
            'action' => 'getcontactway',
            'is_show' => function ($row) {
                if (!empty($row['config_id'])) {
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
        // http://www.myapplicationmodule.com/admin/qyweixin/corpjoinqrcode/uploadmedia?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactContactWay->getInfoById($id);
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
                $agent_agentid = trim($this->request->get('contactway_img_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                if (empty($data['conclusions_image_media_created_at'])) {
                    $image_media_created_at = '2020-01-01 00:00:00';
                } else {
                    $image_media_created_at = $data['conclusions_image_media_created_at'];
                }
                if (empty($data['conclusions_miniprogram_pic_media_created_at'])) {
                    $miniprogram_pic_media_created_at = '2020-01-01 00:00:00';
                } else {
                    $miniprogram_pic_media_created_at = $data['conclusions_miniprogram_pic_media_created_at'];
                }
                if (!empty($data['conclusions_image_media'])) {
                    $res = $weixinopenService->uploadMediaByApi($data['conclusions_image_media'], 'image', $data['conclusions_image_media_id'], $image_media_created_at);
                    // 发生了改变就更新
                    if ($res['media_id'] != $data['conclusions_image_media_id']) {
                        $updateData = array();
                        $updateData['conclusions_image_media_id'] = $res['media_id'];
                        $updateData['conclusions_image_media_created_at'] = getCurrentTime($res['created_at']);
                        $this->modelExternalcontactContactWay->update(array('_id' => $id), array('$set' => $updateData));
                    }
                }

                if (!empty($data['conclusions_miniprogram_pic_media'])) {
                    $res = $weixinopenService->uploadMediaByApi($data['conclusions_miniprogram_pic_media'], 'image', $data['conclusions_miniprogram_pic_media_id'], $miniprogram_pic_media_created_at);
                    // 发生了改变就更新
                    if ($res['media_id'] != $data['conclusions_miniprogram_pic_media_id']) {
                        $updateData = array();
                        $updateData['conclusions_miniprogram_pic_media_id'] = $res['media_id'];
                        $updateData['conclusions_miniprogram_pic_media_created_at'] = getCurrentTime($res['created_at']);
                        $this->modelExternalcontactContactWay->update(array('_id' => $id), array('$set' => $updateData));
                    }
                }

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
    /**
     * @title({name="上传图片"})
     *
     * @name 上传图片
     */
    public function uploadmediaimgAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/corpjoinqrcode/uploadmediaimg?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactContactWay->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "上传图片";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = trim($this->request->get('contactway_img_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->uploadMediaImgByApi($data['conclusions_image_media']);

                $updateData = array();
                $updateData['conclusions_image_pic_url'] = $res['url'];
                $this->modelExternalcontactContactWay->update(array('_id' => $id), array('$set' => $updateData));

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="配置客户联系「联系我」方式"})
     *
     * @name 配置客户联系「联系我」方式
     */
    public function addcontactwayAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/corpjoinqrcode/addcontactway?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactContactWay->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "配置客户联系「联系我」方式";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = trim($this->request->get('contactway_img_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->addContactWay($data);

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取客户联系「联系我」方式"})
     *
     * @name 获取客户联系「联系我」方式
     */
    public function getcontactwayAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/corpjoinqrcode/getcontactway?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactContactWay->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {

                // 构建modal里面Form表单内容
                $fields = $this->getFields4FormTool();
                $title = "获取客户联系「联系我」方式";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = trim($this->request->get('contactway_img_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->getQyWeixinObject()
                    ->getExternalContactManager()
                    ->getContactWay($data['config_id']);
                if (!empty($res['errcode'])) {
                    throw new \Exception($res['errmsg'], $res['errcode']);
                }
                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4FormTool()
    {
        $fields = array();
        $fields['contactway_img_rec_id'] = array(
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
        $fields['contactway_img_name'] = array(
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
        $fields['contactway_img_agentid'] = array(
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
        $schemas['config_id'] = array(
            'name' => '联系方式的配置id',
            'data' => array(
                'type' => 'string',
                'length' => 30,
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
        $typeOptions = array();
        $typeOptions['1'] = '单人';
        $typeOptions['2'] = '多人';
        $schemas['type'] = array(
            'name' => '联系方式类型',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $typeOptions,
                'help' => '联系方式类型,1-单人, 2-多人',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $typeOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $typeOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $sceneOptions = array();
        $sceneOptions['1'] = '在小程序中联系';
        $sceneOptions['2'] = '通过二维码联系';

        $schemas['scene'] = array(
            'name' => '场景',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $sceneOptions,
                'help' => '场景，1-在小程序中联系，2-通过二维码联系',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $sceneOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $sceneOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $styleOptions = array();
        $styleOptions['1'] = '样式1';
        $styleOptions['2'] = '样式2';
        $styleOptions['3'] = '样式3';

        $schemas['style'] = array(
            'name' => '控件样式',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $styleOptions,
                'help' => '在小程序中联系时使用的控件样式，详见附表',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $styleOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $styleOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['remark'] = array(
            'name' => '联系方式的备注信息',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '联系方式的备注信息，用于助记，不超过30个字符',
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
        $schemas['skip_verify'] = array(
            'name' => '外部客户添加时是否无需验证',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => true
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas,
                'help' => '外部客户添加时是否无需验证，默认为true',
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
        $schemas['state'] = array(
            'name' => '企业自定义的state参数',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值，不超过30个字符',
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
        $schemas['user'] = array(
            'name' => '使用该联系方式的用户userID列表',
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
                'help' => '使用该联系方式的用户userID列表，在联系方式类型type为1时为必填，且只能有一个',
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
        $schemas['party'] = array(
            'name' => '使用该联系方式的部门id列表',
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
                'help' => '使用该联系方式的部门id列表，只在联系方式类型type为2时有效',
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
        $schemas['is_temp'] = array(
            'name' => '是否临时会话模式',
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
                'items' => $this->trueOrFalseDatas,
                'help' => '是否临时会话模式，true表示使用临时会话模式，默认为false',
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
        $schemas['expires_in'] = array(
            'name' => '临时会话二维码有效期',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => '',
                'help' => '临时会话二维码有效期，以秒为单位。该参数仅在is_temp为true时有效，默认7天',
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
        $schemas['chat_expires_in'] = array(
            'name' => '临时会话有效期',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => '',
                'help' => '临时会话有效期，以秒为单位。该参数仅在is_temp为true时有效，默认为添加好友后24小时',
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
        $schemas['unionid'] = array(
            'name' => '可进行临时会话的客户unionid',
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
                'help' => '可进行临时会话的客户unionid，该参数仅在is_temp为true时有效，如不指定则不进行限制',
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

        //结束语，会话结束时自动发送给客户，可参考“结束语定义”，仅在is_temp为true时有效
        $schemas['conclusions_text_content'] = array(
            'name' => '消息文本内容',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '结束语，消息文本内容,最长为3000字节',
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
        $schemas['conclusions_image_media'] = array(
            'name' => '图片',
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
        $schemas['conclusions_image_media_id'] = array(
            'name' => '图片的media_id',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '结束语，图片的media_id，可以通过素材管理接口获得',
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['conclusions_image_media_created_at'] = array(
            'name' => '创建图片的media_id的时间',
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
        $schemas['conclusions_image_pic_url'] = array(
            'name' => '图片的链接',
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
                'content_type' => 'url',
                'is_show' => true,
                'items' => '',
                'help' => '结束语，图片的链接，仅可使用上传图片接口得到的链接',
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
        $schemas['conclusions_link_title'] = array(
            'name' => '图文消息标题',
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
                'help' => '结束语，图文消息标题，最长为128字节',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['conclusions_link_picurl'] = array(
            'name' => '图文消息封面的url',
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
                'content_type' => 'url',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['conclusions_link_desc'] = array(
            'name' => '图文消息的描述',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '结束语，图文消息的描述，最长为512字节',
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
        $schemas['conclusions_link_url'] = array(
            'name' => '图文消息的链接',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['conclusions_miniprogram_title'] = array(
            'name' => '小程序消息标题',
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
                'help' => '结束语，小程序消息标题，最长为64字节',
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
        $schemas['conclusions_miniprogram_pic_media'] = array(
            'name' => '小程序消息封面图',
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
                'help' => '结束语，小程序消息封面图，封面图建议尺寸为520*416',
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
        $schemas['conclusions_miniprogram_pic_media_id'] = array(
            'name' => '小程序消息封面的mediaid',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
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
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['conclusions_miniprogram_pic_media_created_at'] = array(
            'name' => '创建小程序消息封面的mediaid的时间',
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
        $schemas['conclusions_miniprogram_appid'] = array(
            'name' => '小程序appid',
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
                'help' => '结束语，小程序appid，必须是关联到企业的小程序应用',
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
        $schemas['conclusions_miniprogram_page'] = array(
            'name' => '小程序page路径',
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
        $schemas['qr_code'] = array(
            'name' => '联系二维码的URL',
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
                'help' => '联系二维码的URL，仅在控件样式scene为2时返回',
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
        $schemas['sync_time'] = array(
            'name' => '同步时间',
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
        return '客户联系「联系我」管理';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactContactWay;
    }
}
