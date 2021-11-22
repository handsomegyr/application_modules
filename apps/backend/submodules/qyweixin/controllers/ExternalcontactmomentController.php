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
        $tools['getimagemedia'] = array(
            'title' => '获取图片',
            'action' => 'getimagemedia',
            'is_show' => function ($row) {
                if (!empty($row['image_media_id']) && empty($row['image_media'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['getvideomedia'] = array(
            'title' => '获取视频',
            'action' => 'getvideomedia',
            'is_show' => function ($row) {
                if (!empty($row['video_media_id']) && empty($row['video_media'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['getvideothumbmedia'] = array(
            'title' => '获取视频封面图片',
            'action' => 'getvideothumbmedia',
            'is_show' => function ($row) {
                if (!empty($row['video_thumb_media_id']) && empty($row['video_thumb_media'])) {
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
     * @title({name="获取图片"})
     *
     * @name 获取图片
     */
    public function getimagemediaAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactmoment/getimagemedia?id=xxx
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
                $title = "获取图片";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = '9999999';
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $file_ext = "jpg";
                $media_id = $data['image_media_id'];
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->getQyWeixinObject()
                    ->getMediaManager()
                    ->download($media_id, $file_ext);
                if (empty($res['errcode'])) {
                    if (!empty($res['name'])) {
                        $filename = $res['name'];
                    } else {
                        $filename = 'qymedia_' . \uniqid() . '.' . $file_ext;
                    }

                    $object = 'moment/' . $filename;
                    $r = file_put_contents(APP_PATH . '/public/' . $object, $res['bytes']); // 返回的是字节数
                    if (!$r) {
                        throw new \Exception('保存文件失败');
                    }
                    // $object = "moment/" . $filename;
                    // $ossService = new \App\Services\OssService();
                    // $res4Oss = $ossService->upload_file_by_content($res['bytes'], $object);
                    // if ($res4Oss->status != 200) {
                    //     throw new \Exception('保存文件失败');
                    // }
                    $updateData = array();
                    $updateData['image_media'] = $object;
                    $this->modelExternalcontactMoment->update(array('_id' => $id), array('$set' => $updateData));
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
                } else {
                    return $this->makeJsonError($res['errmsg']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取视频"})
     *
     * @name 获取视频
     */
    public function getvideomediaAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactmoment/getvideomedia?id=xxx
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
                $title = "获取视频";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = '9999999';
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $file_ext = "mp4";
                $media_id = $data['video_media_id'];
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->getQyWeixinObject()
                    ->getMediaManager()
                    ->download($media_id, $file_ext);
                if (empty($res['errcode'])) {
                    if (!empty($res['name'])) {
                        $filename = $res['name'];
                    } else {
                        $filename = 'qymedia_' . \uniqid() . '.' . $file_ext;
                    }
                    $object = 'moment/' . $filename;
                    $r = file_put_contents(APP_PATH . '/public/' . $object, $res['bytes']); // 返回的是字节数
                    if (!$r) {
                        throw new \Exception('保存文件失败');
                    }
                    // $object = "moment/" . $filename;
                    // $ossService = new \App\Services\OssService();
                    // $res4Oss = $ossService->upload_file_by_content($res['bytes'], $object);
                    // if ($res4Oss->status != 200) {
                    //     throw new \Exception('保存文件失败');
                    // }
                    $updateData = array();
                    $updateData['video_media'] = $object;
                    $this->modelExternalcontactMoment->update(array('_id' => $id), array('$set' => $updateData));
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
                } else {
                    return $this->makeJsonError($res['errmsg']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取视频封面图片"})
     *
     * @name 获取视频封面图片
     */
    public function getvideothumbmediaAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/externalcontactmoment/getvideothumbmedia?id=xxx
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
                $title = "获取视频封面图片";
                $row = $data;
                return $this->showModal($title, $fields, $row);
            } else {
                $agent_agentid = '9999999';
                if (empty($agent_agentid)) {
                    return $this->makeJsonError("企业应用ID未设定");
                }
                $file_ext = "jpg";
                $media_id = $data['video_thumb_media_id'];
                $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $agent_agentid);
                $res = $weixinopenService->getQyWeixinObject()
                    ->getMediaManager()
                    ->download($media_id, $file_ext);
                if (empty($res['errcode'])) {
                    if (!empty($res['name'])) {
                        $filename = $res['name'];
                    } else {
                        $filename = 'qymedia_' . \uniqid() . '.' . $file_ext;
                    }
                    $object = 'moment/' . $filename;
                    $r = file_put_contents(APP_PATH . '/public/' . $object, $res['bytes']); // 返回的是字节数
                    if (!$r) {
                        throw new \Exception('保存文件失败');
                    }
                    // $object = "moment/" . $filename;
                    // $ossService = new \App\Services\OssService();
                    // $res4Oss = $ossService->upload_file_by_content($res['bytes'], $object);
                    // if ($res4Oss->status != 200) {
                    //     throw new \Exception('保存文件失败');
                    // }
                    $updateData = array();
                    $updateData['video_thumb_media'] = $object;
                    $this->modelExternalcontactMoment->update(array('_id' => $id), array('$set' => $updateData));
                    return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功');
                } else {
                    return $this->makeJsonError($res['errmsg']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getFields4FormTool()
    {
        $fields = array();
        $fields['_id'] = array(
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
        $fields['moment_id'] = array(
            'name' => '朋友圈id',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'readonly' => true
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
                'help' => '图片的media_id，可以通过获取临时素材下载资源',
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
            'name' => '视频的media_id',
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
                'help' => '视频的media_id，可以通过获取临时素材下载资源',
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
        $schemas['video_media'] = array(
            'name' => '视频',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true,
                'items' => '',
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
                'help' => '视频封面图片的media_id，可以通过获取临时素材下载资源',
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
