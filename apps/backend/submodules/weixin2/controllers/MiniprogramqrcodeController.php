<?php
namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Miniprogram\Qrcode\Qrcode;

/**
 * @title({name="小程序二维码"})
 *
 * @name 小程序二维码
 */
class MiniprogramqrcodeController extends \App\Backend\Controllers\FormController
{
    private $modelQrcode;

    public function initialize()
    {
        $this->modelQrcode = new Qrcode();
        parent::initialize();
    }

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
    $schemas['authorizer_appid'] = array(
        'name' => '授权方应用ID',
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
        ),
        'search' => array(
            'is_show' => true
        ),
        'export' => array(
            'is_show' => true
        )
    );
    $schemas['name'] = array(
        'name' => '二维码名',
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
    $schemas['type'] = array(
        'name' => '二维码类型',
        'data' => array(
            'type' => 'string',
            'length' => 30,
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
    $schemas['path'] = array(
        'name' => '小程序页面路径，接口A和C所用，扫码进入的小程序页面路径，最大长度 128 字节，不能为空；对于小游戏，可以只传入 query 部分，来实现传参效果，如：传入 "?foo=bar"，即可在 wx.getLaunchOptionsSync 接口中的 query 参数获取到 {foo:"bar"}。',
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
        ),
        'search' => array(
            'is_show' => true
        ),
        'export' => array(
            'is_show' => true
        )
    );
    $schemas['page'] = array(
        'name' => '小程序页面，接口B所用，必须是已经发布的小程序存在的页面（否则报错），例如 pages/index/index, 根路径前不要填加 /,不能携带参数（参数请放在scene字段里），如果不填写这个字段，默认跳主页面',
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
        ),
        'search' => array(
            'is_show' => true
        ),
        'export' => array(
            'is_show' => true
        )
    );
    $schemas['scene'] = array(
        'name' => '场景，接口B所用，最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）',
        'data' => array(
            'type' => 'string',
            'length' => 32,
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
    $schemas['width'] = array(
        'name' => '二维码的宽度，单位 px，最小 280px，最大 1280px',
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
    $schemas['auto_color'] = array(
        'name' => '是否自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调，默认 false',
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
    $schemas['line_color'] = array(
        'name' => '线条颜色,auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示',
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
            'is_show' => true
        ),
        'export' => array(
            'is_show' => true
        )
    );
    $schemas['is_hyaline'] = array(
        'name' => '是否需要透明底色，为 true 时，生成透明底色的小程序',
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
    $schemas['url'] = array(
        'name' => '二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片',
        'data' => array(
            'type' => 'string',
            'length' => 255,
            'defaultValue' => ''
        ),
        'validation' => array(
            'required' => false
        ),
        'form' => array(
            'input_type' => 'image',
            'is_show' => true,
            'items' => ''
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
    $schemas['qrcode_time'] = array(
        'name' => 'qrcode时间',
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
    $schemas['is_created'] = array(
        'name' => '是否已创建',
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

        return $schemas;
    }

    protected function getName()
    {
        return '小程序二维码';
    }

    protected function getModel()
    {
        return $this->modelQrcode;
    }    
}