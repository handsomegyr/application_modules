<?php
namespace App\Backend\Controllers\Site;

use App\Backend\Models\Site\Site;

/**
 * @title({name="网站设置管理"})
 *
 * @name 网站设置管理
 */
class SiteController extends \App\Backend\Controllers\FormController
{

    private $modelSite = NULL;

    public function initialize()
    {
        $this->modelSite = new Site();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '网站名称',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
            'name' => '网站标题',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['keywords'] = array(
            'name' => '网站关键词',
            'data' => array(
                'type' => 'string',
                'length' => 100
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['description'] = array(
            'name' => '网站描述',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['logo'] = array(
            'name' => '网站Logo',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelSite->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['mobile_logo'] = array(
            'name' => '手机网站Logo',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelSite->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['member_logo'] = array(
            'name' => '会员中心Logo',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelSite->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['logowx'] = array(
            'name' => '微信二维码',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelSite->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['icp_number'] = array(
            'name' => 'ICP证书号',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
        $schemas['phone'] = array(
            'name' => '平台客服联系电话',
            'data' => array(
                'type' => 'string',
                'length' => 100
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['tel400'] = array(
            'name' => '前台客服电话',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['email'] = array(
            'name' => '电子邮件',
            'data' => array(
                'type' => 'string',
                'length' => 30
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['statistics_code'] = array(
            'name' => '第三方流量统计代码',
            'data' => array(
                'type' => 'string',
                'length' => 500
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['time_zone'] = array(
            'name' => '默认时区',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['status'] = array(
            'name' => '站点状态',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
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
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['closed_reason'] = array(
            'name' => '关闭原因',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['image_dir_type'] = array(
            'name' => '图片存放类型',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['image_max_filesize'] = array(
            'name' => '图片文件大小',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['image_allow_ext'] = array(
            'name' => '图片扩展名',
            'data' => array(
                'type' => 'string',
                'length' => 100
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['default_goods_image'] = array(
            'name' => '默认商品图片',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelSite->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['default_user_portrait'] = array(
            'name' => '默认会员头像',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelSite->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['hot_search'] = array(
            'name' => '热门搜索',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['md5_key'] = array(
            'name' => 'MD5密钥',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['guest_comment'] = array(
            'name' => '是否允许游客咨询',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
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
                'list_type' => 1,
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['copyrights'] = array(
            'name' => '版权信息',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '网站设置';
    }

    protected function getModel()
    {
        return $this->modelSite;
    }
}