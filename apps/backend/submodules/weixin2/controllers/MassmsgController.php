<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\MassMsg\MassMsg;
use App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer;
use App\Backend\Submodules\Weixin2\Models\Component\Component;

use App\Backend\Submodules\Weixin2\Models\MassMsg\Type;
use App\Backend\Submodules\Weixin2\Models\Media\Media;
use App\Backend\Submodules\Weixin2\Models\Material\Material;

/**
 * @title({name="群发消息"})
 *
 * @name 群发消息
 */
class MassmsgController extends \App\Backend\Controllers\FormController
{
    private $modelMassMsg;
    private $modelAuthorizer;
    private $modelComponent;
    private $modelType;
    private $modelMedia;
    private $modelMaterial;

    public function initialize()
    {
        $this->modelMassMsg = new MassMsg();
        $this->modelAuthorizer = new Authorizer();
        $this->modelComponent = new Component();
        $this->modelType = new Type();
        $this->modelMedia = new Media();
        $this->modelMaterial = new Material();

        $this->componentItems = $this->modelComponent->getAll();
        $this->authorizerItems = $this->modelAuthorizer->getAll();

        $this->typeItems = $this->modelType->getAll();
        $this->mediaItems = $this->modelMedia->getAllByType("", "_id");
        $this->thumbmediaItems = $this->modelMedia->getAllByType("thumb", "_id");
        $this->materialItems = $this->modelMaterial->getAllByType("", "media_id");
        $this->thumbmediaidItems = $this->modelMaterial->getAllByType("thumb", "media_id");

        parent::initialize();
    }
    protected $componentItems = null;
    protected $authorizerItems = null;
    protected $typeItems = null;
    protected $mediaItems = null;
    protected $thumbmediaItems = null;
    protected $materialItems = null;
    protected $thumbmediaidItems = null;

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['component_appid'] = array(
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
        $schemas['name'] = array(
            'name' => '群发消息名称',
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
        $schemas['msg_type'] = array(
            'name' => '群发消息类型',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->typeItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->typeItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->typeItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['media'] = array(
            'name' => '临时素材',
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
                'help' => '发送的图片/语音/视频/图文消息（点击跳转到图文消息页）的媒体ID，临时素材记录ID,(图片,语音,视频消息用)',
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
        $schemas['media_id'] = array(
            'name' => '永久素材',
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
                'items' => $this->materialItems,
                'help' => '发送的图片/语音/视频/图文消息（点击跳转到图文消息页）的媒体ID，永久素材媒体ID,(图片,语音,视频,mpnews图文消息用)',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->materialItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'items' => $this->materialItems,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['thumb_media'] = array(
            'name' => '缩略图的临时素材',
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
                'items' => $this->thumbmediaItems,
                'help' => '缩略图/小程序卡片图片的媒体ID，小程序卡片图片建议大小为520*416，临时素材记录ID,(视频,音乐,小程序消息用)',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->thumbmediaItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->thumbmediaItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['thumb_media_id'] = array(
            'name' => '缩略图的永久素材',
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
                'items' => $this->thumbmediaidItems,
                'help' => '缩略图/小程序卡片图片的媒体ID，小程序卡片图片建议大小为520*416永久素材媒体ID,(视频,音乐,小程序消息用)',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->thumbmediaidItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->thumbmediaidItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['title'] = array(
            'name' => '标题',
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
                'help' => '消息的标题(群发消息类型是视频时有用)',
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
                'help' => '消息的描述(群发消息类型是视频和文本时有用)',
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
        $schemas['card_id'] = array(
            'name' => '卡券ID',
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
                'help' => '微信公众平台的卡券ID(群发消息类型是卡券时有用)',
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
        $schemas['card_ext'] = array(
            'name' => '卡券的扩展信息',
            'data' => array(
                'type' => 'json',
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
                'render' => '',
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['upload_media_id'] = array(
            'name' => '上传视频后的media_id',
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
        $schemas['upload_media_created_at'] = array(
            'name' => '上传视频后的创建时间',
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
        $schemas['upload_media_type'] = array(
            'name' => '上传图文后的媒体类型',
            'data' => array(
                'type' => 'string',
                'length' => 20,
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

        return $schemas;
    }

    protected function getName()
    {
        return '群发消息';
    }

    protected function getModel()
    {
        return $this->modelMassMsg;
    }
}
