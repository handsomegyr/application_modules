<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\AppchatMsg\AppchatMsg;
use App\Backend\Submodules\Qyweixin\Models\AppchatMsg\Type;
use App\Backend\Submodules\Qyweixin\Models\Media\Media;

/**
 * @title({name="群聊会话消息"})
 *
 * @name 群聊会话消息
 */
class AppchatmsgController extends BaseController
{
    private $modelAppchatMsg;

    private $modelType;
    private $modelMedia;

    public function initialize()
    {
        $this->modelAppchatMsg = new AppchatMsg();
        $this->modelType = new Type();
        $this->modelMedia = new Media();

        $this->typeItems = $this->modelType->getAll();
        $this->mediaItems = $this->modelMedia->getAllByType("", "_id");

        parent::initialize();
    }

    protected $typeItems = null;
    protected $mediaItems = null;

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

        // $schemas['chatid'] = array(
        //     'name' => '群聊id',
        //     'data' => array(
        //         'type' => 'string',
        //         'length' => 32,
        //         'defaultValue' => ''
        //     ),
        //     'validation' => array(
        //         'required' => true
        //     ),
        //     'form' => array(
        //         'input_type' => 'text',
        //         'is_show' => true,
        //         'items' => ''
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => ''
        //     ),
        //     'search' => array(
        //         'is_show' => true
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );

        $schemas['name'] = array(
            'name' => '群聊会话消息名称',
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
            'name' => '群聊会话消息类型',
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
        // $schemas['media_id'] = array(
        //     'name' => '永久素材',
        //     'data' => array(
        //         'type' => 'string',
        //         'length' => 255,
        //         'defaultValue' => ''
        //     ),
        //     'validation' => array(
        //         'required' => false
        //     ),
        //     'form' => array(
        //         'input_type' => 'select',
        //         'is_show' => true,
        //         'items' => $this->materialItems,
        //         'help' => '发送的图片/语音/视频/图文消息（点击跳转到图文消息页）的媒体ID，永久素材媒体ID,(图片,语音,视频,mpnews图文消息用)',
        //     ),
        //     'list' => array(
        //         'is_show' => true,
        //         'list_type' => '',
        //         'render' => '',
        //         'items' => $this->materialItems,
        //     ),
        //     'search' => array(
        //         'input_type' => 'select',
        //         'items' => $this->materialItems,
        //         'is_show' => true
        //     ),
        //     'export' => array(
        //         'is_show' => true
        //     )
        // );

        $schemas['title'] = array(
            'name' => '标题',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '标题,(音乐,视频,图文消息用)',
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
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '描述,(文本,音乐,视频,图文消息用)',
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
            'name' => '点击后跳转的链接',
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
                'help' => '点击后跳转的链接。最长2048字节，请确保包含了协议头(http/https)',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => ''
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['btntxt'] = array(
            'name' => '按钮文字',
            'data' => array(
                'type' => 'string',
                'length' => 10,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '按钮文字。 默认为“详情”， 不超过4个文字，超过自动截断。',
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

        $schemas['safe'] = array(
            'name' => '是否是保密消息',
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
        $schemas['priority'] = array(
            'name' => '优先级',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
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

        return $schemas;
    }

    protected function getName()
    {
        return '群聊会话消息';
    }

    protected function getModel()
    {
        return $this->modelAppchatMsg;
    }
}
