<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\ReplyType;
use App\Backend\Submodules\Weixin\Models\Reply;

/**
 * @title({name="微信回复管理"})
 *
 * @name 微信回复管理
 */
class ReplyController extends \App\Backend\Controllers\FormController
{

    private $modelReplyType;

    private $modelReply;

    public function initialize()
    {
        $this->modelReplyType = new ReplyType();
        $this->modelReply = new Reply();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['reply_type'] = array(
            'name' => '回复类型',
            'data' => array(
                'type' => 'integer',
                'length' => '1'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelReplyType->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'reply_type_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['keyword'] = array(
            'name' => '回复名称',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['title'] = array(
            'name' => '标题(图文)',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['url'] = array(
            'name' => '网址链接(图文)',
            'data' => array(
                'type' => 'string',
                'length' => '100'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['description'] = array(
            'name' => '内容(图文|文本)',
            'data' => array(
                'type' => 'html',
                'length' => '1000'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'ueditor',
                // 'input_type' => 'ckeditor',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['picture'] = array(
            'name' => 'banner图片(图文)',
            'data' => array(
                'type' => 'file',
                'length' => '100',
                'file' => array(
                    'path' => $this->modelReply->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['icon'] = array(
            'name' => '小图标(图文)',
            'data' => array(
                'type' => 'file',
                'length' => '100',
                'file' => array(
                    'path' => $this->modelReply->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['music'] = array(
            'name' => '音乐(音乐)',
            'data' => array(
                'type' => 'file',
                'length' => '100',
                'file' => array(
                    'path' => $this->modelReply->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['voice'] = array(
            'name' => '音频(音频)',
            'data' => array(
                'type' => 'file',
                'length' => '100',
                'file' => array(
                    'path' => $this->modelReply->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['video'] = array(
            'name' => '视频(视频)',
            'data' => array(
                'type' => 'file',
                'length' => '100',
                'file' => array(
                    'path' => $this->modelReply->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['image'] = array(
            'name' => '图片(图片)',
            'data' => array(
                'type' => 'file',
                'length' => '100',
                'file' => array(
                    'path' => $this->modelReply->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['priority'] = array(
            'name' => '优先级',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['page'] = array(
            'name' => '自定义页面',
            'data' => array(
                'type' => 'string',
                'length' => '100'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['show_times'] = array(
            'name' => '展示次数',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['click_times'] = array(
            'name' => '阅读次数',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '微信回复';
    }

    protected function getModel()
    {
        return $this->modelReply;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $replyTypeList = $this->modelReplyType->getAll();
        foreach ($list['data'] as &$item) {
            $item['reply_type_name'] = isset($replyTypeList[$item['reply_type']]) ? $replyTypeList[$item['reply_type']] : "--";
        }
        return $list;
    }
}