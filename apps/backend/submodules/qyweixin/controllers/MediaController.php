<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\Media\Media;
use App\Backend\Submodules\Qyweixin\Models\Media\Type;

/**
 * @title({name="临时素材设置"})
 *
 * @name 临时素材设置
 */
class MediaController extends BaseController
{
    private $modelMedia;
    private $modelMediaType;
    public function initialize()
    {
        $this->modelMedia = new Media();
        $this->modelMediaType = new Type();
        $this->mediaTypeItems = $this->modelMediaType->getAll();
        parent::initialize();
    }
    protected $mediaTypeItems = null;

    protected function getFormTools2($tools)
    {
        $tools['getmedia'] = array(
            'title' => '获取临时素材',
            'action' => 'getmedia',
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['authorizer_appid']) && !empty($row['media_id'])) {
                    return true;
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        $tools['uploadmedia'] = array(
            'title' => '上传临时素材',
            'action' => 'uploadmedia',
            'is_show' => function ($row) {
                if (!empty($row) && !empty($row['authorizer_appid']) && !empty($row['type'])) {
                    // 媒体文件在微信后台保存时间为3天，即3天后media_id失效。
                    $expire_seconds = 24 * 3600 * 2.5;
                    if (!empty($row['media_id']) && ((strtotime($row['media_time']) + $expire_seconds) > time())) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        return $tools;
    }

    /**
     * @title({name="获取临时素材"})
     *
     * @name 获取临时素材
     */
    public function getmediaAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/media/getmedia?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelMedia->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            $file_ext = "";
            if ($data['type'] == "voice") {
                $file_ext = "mp3";
            } elseif ($data['type'] == "video") {
                $file_ext = "mp4";
            }
            $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $data['agentid']);
            $res = $weixinopenService->getQyWeixinObject()
                ->getMediaManager()
                ->download($data['media_id'], $file_ext);
            // 如果返回的是视频消息素材，则内容如下：
            // {
            // "video_url":DOWN_URL
            // }
            if (empty($res['errcode'])) {
                if ($data['type'] == "video") {
                    // [name] => 5cd941b1e8a4b.mp4
                    // [bytes] => {"video_url":"http://203.205.158.72/vweixinp.tc.qq.com/1007_7fad4951eb4a4a1da466a78355446f4f.f10.mp4?vkey=788BC4E9CB3FC5CCF876C0CCBE669C043EDF5999ED9AE8831B6FEE3EC057B41A11E815C640B0391C87BC5C1691DF96C8B5C8EFBB7D6E45EB37D5AB0127DA0C010D4039D0258E012DBEE0136E4A3BBF3BCE75D71B17B78674&sha=0&save=1"}
                    $res['bytes'] = \json_decode($res['bytes'], true);
                    // $fileContent = file_get_contents($res['bytes']['video_url']);
                    //"then":{"action":"download","value":"http:\/\/190821fg0463demo.jdytoy.com\/admin\/admin-build\/download-file?file_id=20200203192717.csv"},
                    return $this->makeJsonResult(array('then' => array('action' => 'download', 'value' => $res['bytes']['video_url'])), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
                    //return Response::redirectTo($res['bytes']['video_url'], 302);
                } else {
                    if (!empty($res['name'])) {
                        $filename = $res['name'];
                    } else {
                        $filename = 'qymedia_' . \uniqid() . '.' . $file_ext;
                    }
                    $path = APP_PATH . '/upload/qymedia/' . $filename;
                    // $path = \tempnam(\sys_get_temp_dir(), 'media_');
                    $fp = fopen($path, 'w');
                    $fileContent = $res['bytes'];
                    fwrite($fp, $fileContent);
                    fclose($fp);
                    $fileName = basename($path);
                    $url = $this->url->get("service/file/index") . '?upload_path=qymedia&id=' . $fileName;
                    return $this->makeJsonResult(array('then' => array('action' => 'download', 'value' => $url)), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
                    //return Response::download($path, $filename);
                }
            } else {
                return $this->makeJsonError($res['errmsg']);
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="上传临时素材"})
     *
     * @name 上传临时素材
     */
    public function uploadmediaAction()
    {
        // http://www.myapplicationmodule.com/admin/qyweixin/media/uploadmedia?id=xxx
        try {
            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $data = $this->modelMedia->getInfoById($id);
            if (empty($data)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }
            $weixinopenService = new \App\Qyweixin\Services\QyService($data['authorizer_appid'], $data['provider_appid'], $data['agentid']);
            $res = $weixinopenService->uploadMedia($data);
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
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
        $schemas['agentid'] = array(
            'name' => '应用ID',
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
        $schemas['name'] = array(
            'name' => '素材名',
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
        $schemas['type'] = array(
            'name' => '媒体文件类型',
            'data' => array(
                'type' => 'string',
                'length' => 10,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->mediaTypeItems,
                'help' => '媒体文件类型，分别有图片（image）、语音（voice）、视频（video）、缩略图（thumb，主要用于视频与音乐格式的缩略图）'
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->mediaTypeItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->mediaTypeItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['media'] = array(
            'name' => '媒体文件',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelMedia->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    //display()方法来通过传入的回调函数来处理当前列的值：
                    return $column->display(function () use ($column) {
                        // return '<a href="http://intonewxcrm.oss-cn-hangzhou.aliyuncs.com/media/PL8xEhxtvJjY1m9JmGmX.mp3" download="PL8xEhxtvJjY1m9JmGmX.mp3" target="_blank" class="text-muted">
                        //     <i class="fa fa-download"></i> PL8xEhxtvJjY1m9JmGmX.mp3;
                        // </a>';
                        // 如果这一列的status字段的值等于1，直接显示title字段
                        if ($this->type == 'image' || $this->type == 'thumb') {
                            return $column->image("", 50, 50);
                        } else {
                            return $column->downloadable();
                        }
                    });
                }
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['media_id'] = array(
            'name' => '临时素材的media_id',
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
        $schemas['media_time'] = array(
            'name' => '临时素材生成时间',
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
        $schemas['url'] = array(
            'name' => '永久图片url',
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
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '临时素材设置';
    }

    protected function getModel()
    {
        return $this->modelMedia;
    }
}
