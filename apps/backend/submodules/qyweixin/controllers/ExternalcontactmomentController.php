<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\Moment;

/**
 * @title({name="客户朋友圈管理"})
 *
 * @name 客户朋友圈管理
 */
class ExternalcontactmomentController extends BaseController
{
    private $modelExternalcontactMoment;

    public function initialize()
    {
        $this->modelExternalcontactMoment = new Moment();
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
                    (!empty($row['image_media']) && $weixinopenService->isMediaTimeExpired($row['image_media_id'], $row['image_media_created_at'])) ||
                    (!empty($row['miniprogram_pic_media']) && $weixinopenService->isMediaTimeExpired($row['miniprogram_pic_media_id'], $row['miniprogram_pic_media_created_at']))
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
                if (!empty($row['image_media']) && empty($row['image_pic_url'])) {
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
        // http://www.applicationmodule.com/admin/qyweixin/externalcontactgroupwelcometemplate/uploadmedia?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactMoment->getInfoById($id);
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
                $agent_agentid = trim($this->request->get('groupwelcometemplate_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                if (empty($data['image_media_created_at'])) {
                    $image_media_created_at = '2020-01-01 00:00:00';
                } else {
                    $image_media_created_at = $data['image_media_created_at'];
                }
                if (empty($data['miniprogram_pic_media_created_at'])) {
                    $miniprogram_pic_media_created_at = '2020-01-01 00:00:00';
                } else {
                    $miniprogram_pic_media_created_at = $data['miniprogram_pic_media_created_at'];
                }

                if (!empty($data['image_media'])) {
                    $res = $weixinopenService->uploadMediaByApi($data['image_media'], 'image', $data['image_media_id'], $image_media_created_at);
                    // 发生了改变就更新
                    if ($res['media_id'] != $data['image_media_id']) {
                        $updateData = array();
                        $updateData['image_media_id'] = $res['media_id'];
                        $updateData['image_media_created_at'] = getCurrentTime($res['created_at']);
                        $this->modelExternalcontactMoment->update(array('_id' => $id), array('$set' => $updateData));
                    }
                }

                if (!empty($data['miniprogram_pic_media'])) {
                    $res = $weixinopenService->uploadMediaByApi($data['miniprogram_pic_media'], 'image', $data['miniprogram_pic_media_id'], $miniprogram_pic_media_created_at);
                    // 发生了改变就更新
                    if ($res['media_id'] != $data['miniprogram_pic_media_id']) {
                        $updateData = array();
                        $updateData['miniprogram_pic_media_id'] = $res['media_id'];
                        $updateData['miniprogram_pic_media_created_at'] = getCurrentTime($res['created_at']);
                        $this->modelExternalcontactMoment->update(array('_id' => $id), array('$set' => $updateData));
                    }
                }
                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
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
        // http://www.applicationmodule.com/admin/qyweixin/externalcontactgroupwelcometemplate/uploadmediaimg?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelExternalcontactMoment->getInfoById($id);
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
                $agent_agentid = trim($this->request->get('groupwelcometemplate_agentid'));
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->uploadMediaImgByApi($data['image_media']);

                $updateData = array();
                $updateData['image_pic_url'] = $res['url'];
                $this->modelExternalcontactMoment->update(array('_id' => $id), array('$set' => $updateData));

                $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \json_encode($res));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4FormTool()
    {
        $fields = array();
        $fields['groupwelcometemplate_rec_id'] = array(
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
        $fields['groupwelcometemplate_name'] = array(
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
        $fields['groupwelcometemplate_agentid'] = array(
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

        $schemas['moment_id'] = array(
            'name' => '朋友圈id',
            'data' => array(
                'type' => 'string',
                'length' => 190,
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

        $schemas['creator'] = array(
            'name' => '朋友圈创建者userid',
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

        $create_type_options = array();
        $create_type_options['0'] = '企业';
        $create_type_options['1'] = '个人';
        $schemas['create_type'] = array(
            'name' => '朋友圈创建来源',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $create_type_options
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $create_type_options
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $create_type_options
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $visible_type_options = array();
        $visible_type_options['0'] = '部分可见';
        $visible_type_options['1'] = '公开';
        $schemas['visible_type'] = array(
            'name' => '可见范围类型',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $visible_type_options
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $visible_type_options
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $visible_type_options
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['text_content'] = array(
            'name' => '文本消息',
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

        $schemas['image_media_id'] = array(
            'name' => '图片的media_id',
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
                'help' => '视频图片的media_id，可以通过获取临时素材下载资源',
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
        $schemas['image_media'] = array(
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

        $schemas['video_media_id'] = array(
            'name' => '视频图片的media_id',
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
                'help' => '图片的media_id，可以通过获取临时素材下载资源',
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
        $schemas['video_media'] = array(
            'name' => '视频图片',
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
        $schemas['video_thumb_media_id'] = array(
            'name' => '视频封面图片的media_id',
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
                'help' => '图片的media_id，可以通过获取临时素材下载资源',
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
        $schemas['video_thumb_media'] = array(
            'name' => '视频封面图片',
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

        $schemas['link_title'] = array(
            'name' => '网页链接标题',
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
        $schemas['link_url'] = array(
            'name' => '网页链接url',
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
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['location_latitude'] = array(
            'name' => '地理位置纬度',
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
        $schemas['location_longitude'] = array(
            'name' => '地理位置经度',
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
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['location_name'] = array(
            'name' => '地理位置名称',
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

        $schemas['create_time'] = array(
            'name' => '朋友圈创建时间',
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
        return '客户朋友圈管理';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactMoment;
    }
}
