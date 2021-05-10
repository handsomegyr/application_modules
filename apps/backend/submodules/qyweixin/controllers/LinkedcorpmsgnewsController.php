<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\News;
use App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\LinkedcorpMsg;
use App\Backend\Submodules\Qyweixin\Models\Media\Media;

/**
 * @title({name="互联企业消息图文设置"})
 *
 * @name 互联企业消息图文设置
 */
class LinkedcorpmsgnewsController extends BaseController
{
    private $modelNews;
    private $modelLinkedcorpMsg;
    public function initialize()
    {
        $this->modelNews = new News();
        $this->modelLinkedcorpMsg = new LinkedcorpMsg();
        $this->modelMedia = new Media();

        $this->linkedcorpMsgItems = $this->modelLinkedcorpMsg->getAllByType("news", "_id");
        $this->mediaItems = $this->modelMedia->getAllByType("image", "_id");
        parent::initialize();
    }
    protected $linkedcorpMsgItems = null;
    protected $mediaItems = null;

    protected function getSchemas2($schemas)
    {
        $schemas['linkedcorp_msg_id'] = array(
            'name' => '所属互联企业消息ID',
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
                'items' => $this->linkedcorpMsgItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->linkedcorpMsgItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->linkedcorpMsgItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['title'] = array(
            'name' => '图文消息标题',
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
        $schemas['description'] = array(
            'name' => '图文消息描述',
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
            'name' => '点击图文消息跳转链接',
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
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['big_pic_url'] = array(
            'name' => '图文消息的图片链接',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelNews->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true,
                'items' => '',
                'help' => '图文消息的图片链接，支持JPG、PNG格式，较好的效果为大图 1068*455，小图150*150。',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['small_pic_url'] = array(
            'name' => '小图图片链接',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'defaultValue' => '',
                'file' => array(
                    'path' => $this->modelNews->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true,
                'items' => '',
                'help' => '小图图片链接，支持JPG、PNG格式，较好的效果为小图200*200',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => 'img',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );


        $schemas['thumb_media'] = array(
            'name' => '图文消息缩略图的临时素材',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->mediaItems,
                'help' => '图文消息缩略图的media_id, 可以通过素材管理接口获得。此处thumb_media_id即上传接口返回的media_id',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->mediaItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->mediaItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['author'] = array(
            'name' => '图文消息的作者',
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
                'help' => '图文消息的作者，不超过64个字节',
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
                'help' => '图文消息的描述，不超过512个字节，超过会自动截断（支持id转译）',
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

        $schemas['index'] = array(
            'name' => '文章在图文消息中的位置',
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
                'help' => '要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0',
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
        return '互联企业消息图文设置';
    }

    protected function getModel()
    {
        return $this->modelNews;
    }
}
