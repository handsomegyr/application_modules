<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Draft\News;
use App\Backend\Submodules\Weixin2\Models\Draft\Draft;
use App\Backend\Submodules\Weixin2\Models\Material\Material;

/**
 * @title({name="草稿箱图文设置"})
 *
 * @name 草稿箱图文设置
 */
class DraftnewsController extends BaseController
{
    private $modelNews;
    private $modelDraft;
    private $modelMaterial;
    public function initialize()
    {
        $this->modelNews = new News();
        $this->modelDraft = new Draft();
        $this->modelMaterial = new Material();

        $this->draftItems = $this->modelDraft->getAllByType("mpnews");
        $this->thumbmediaItems = $this->modelMaterial->getAllByType("thumb", "_id");
        $this->thumbMediaIdItems = $this->modelMaterial->getAllByType("thumb", "media_id");
        parent::initialize();
    }
    protected $draftItems = null;
    protected $thumbmediaItems = null;
    protected $thumbMediaIdItems = null;

    protected function getHeaderTools2($tools)
    {
        $tools['batchgetdraftnews'] = array(
            'title' => '获取草稿列表',
            'action' => 'batchgetdraftnews',
            'is_show' => true,
            'is_export' => false,
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    /**
     * @title({name="获取草稿列表"})
     *
     * @name 获取草稿列表
     */
    public function batchgetdraftnewsAction()
    {
        // http://www.myapplicationmodule.com/admin/weixin2/draftnews/batchgetdraftnews?id=xxx
        try {
            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = $this->getFields4HeaderTool();

                //offset 是 从全部草稿的该偏移位置开始返回，0表示从第一个草稿 返回
                $fields['draftnews_offset'] = array(
                    'name' => '偏移位置开始返回,0表示从第一个草稿',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'number',
                        'is_show' => true
                    ),
                );
                //count 是 返回草稿的数量，取值在1到20之间
                //$this->text('draftnews_count', '返回草稿的数量,取值在1到20之间')->value(20);

                $title = "获取草稿列表";
                $row = array();
                // 初始值
                $row['draftnews_offset'] = 0;
                return $this->showModal($title, $fields, $row);
            } else {
                $component_appid = trim($this->request->get('draftnews_component_appid'));
                $authorizer_appid = trim($this->request->get('draftnews_authorizer_appid'));
                $offset = intval($this->request->get('draftnews_offset'));
                if (empty($component_appid)) {
                    return $this->makeJsonError("第三方平台应用ID未设定");
                }
                if (empty($authorizer_appid)) {
                    return $this->makeJsonError("授权方应用ID未设定");
                }
                if ($offset < 0) {
                    return $this->makeJsonError("偏移位置未设定");
                }
                $weixinopenService = new \App\Weixin2\Services\WeixinService($authorizer_appid, $component_appid);
                $res = $weixinopenService->batchgetDraftNews($offset);
                // if (empty($res['errcode'])) {
                //     return print_r($res);
                //     return true;
                // }
                // return 'errcode:' . $res['errcode'] . '  msg:' . $res['errmsg'];
                if (empty($res['errcode'])) {
                    return  $this->makeJsonResult(array('then' => array('action' => 'refresh')), '操作成功:' . \App\Common\Utils\Helper::myJsonEncode($res));
                } else {
                    return $this->makeJsonError($res['errmsg']);
                }
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
    protected function getFields4HeaderTool()
    {
        $fields = array();
        $fields['draftnews_component_appid'] = array(
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
        $fields['draftnews_authorizer_appid'] = array(
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
                'required' => false
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
        $schemas['draftnews_id'] = array(
            'name' => '所属草稿箱ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->draftItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->draftItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->draftItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['title'] = array(
            'name' => '标题',
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
        $schemas['author'] = array(
            'name' => '作者',
            'data' => array(
                'type' => 'string',
                'length' => 30,
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
        $schemas['digest'] = array(
            'name' => '图文消息的摘要',
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
                'help' => '图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空。',
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
        $schemas['content'] = array(
            'name' => '图文消息的具体内容',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS。',
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
        $schemas['content_source_url'] = array(
            'name' => '图文消息的原文地址',
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
                'help' => '图文消息的原文地址，即点击“阅读原文”后的URL',
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
        // $schemas['thumb_media'] = array(
        //     'name' => '图文消息的封面图片永久素材',
        //     'data' => array(
        //         'type' => 'integer',
        //         'length' => 11,
        //         'defaultValue' => 0
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'select',
        //         'is_show' => true,
        //         'items' => $this->thumbmediaItems,
        //         'help' => '缩略图/小程序卡片图片的媒体ID，小程序卡片图片建议大小为520*416，临时素材记录ID,(视频,音乐,小程序消息用)',
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => '',
        //         'items' => $this->thumbmediaItems,
        //     ),
        //     'search' => array(
        //         'input_type' => 'select',
        //         'is_show' => true,
        //         'items' => $this->thumbmediaItems,
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );
        $schemas['thumb_media_id'] = array(
            'name' => '图文消息的封面图片素材id（一定是永久MediaID）',
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
                'items' => $this->thumbMediaIdItems,
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->thumbMediaIdItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->thumbMediaIdItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['show_cover_pic'] = array(
            'name' => '是否在正文显示封面',
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
                'items' => $this->trueOrFalseDatas,
                'help' => '是否在正文显示封面。平台已不支持此功能，因此默认为0，即不展示',
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
        $schemas['need_open_comment'] = array(
            'name' => '是否打开评论',
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
                'items' => $this->trueOrFalseDatas,
                'help' => '是否打开评论，0不打开(默认)，1打开',
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
        //是否粉丝才可评论，0所有人可评论，1粉丝才可评论
        $onlyFansCanCommentOptions = array();
        $onlyFansCanCommentOptions[0] = "所有人可评论";
        $onlyFansCanCommentOptions[1] = "粉丝才可评论";
        $schemas['only_fans_can_comment'] = array(
            'name' => '是否粉丝才可评论',
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
                'items' => $onlyFansCanCommentOptions,
                'help' => '是否粉丝才可评论，0所有人可评论，1粉丝才可评论',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $onlyFansCanCommentOptions,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $onlyFansCanCommentOptions,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['url'] = array(
            'name' => '草稿的临时链接',
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
        $schemas['index'] = array(
            'name' => '要更新的文章在图文消息中的位置',
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
                'name' => '要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0',
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
        $schemas['media_id'] = array(
            'name' => '图文消息的id',
            'data' => array(
                'type' => 'string',
                'length' => 190,
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

        $schemas['create_time'] = array(
            'name' => '这篇图文消息素材的创建时间',
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
        $schemas['update_time'] = array(
            'name' => '这篇图文消息素材的最后更新时间',
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

        $schemas['is_exist'] = array(
            'name' => '是否存在',
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

        return $schemas;
    }

    protected function getName()
    {
        return '草稿箱图文设置';
    }

    protected function getModel()
    {
        return $this->modelNews;
    }
}
