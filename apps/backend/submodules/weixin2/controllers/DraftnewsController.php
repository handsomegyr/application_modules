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

    protected function getSchemas2($schemas)
    {
        $schemas['draft_id'] = array(
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
