<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Menu\ConditionalMatchrule;
use App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer;
use App\Backend\Submodules\Weixin2\Models\Component\Component;
use App\Backend\Submodules\Weixin2\Models\User\Tag;
use App\Backend\Submodules\Weixin2\Models\Language;

/**
 * @title({name="个性化菜单匹配规则设置"})
 *
 * @name 个性化菜单匹配规则设置
 */
class MenuconditionalmatchruleController extends \App\Backend\Controllers\FormController
{
    private $modelConditionalMatchrule;
    private $modelAuthorizer;
    private $modelComponent;

    private $modelUserTag;
    private $modelLanguage;
    public function initialize()
    {
        $this->modelConditionalMatchrule = new ConditionalMatchrule();
        $this->modelAuthorizer = new Authorizer();
        $this->modelComponent = new Component();

        $this->modelUserTag = new Tag();
        $this->modelLanguage = new Language();

        $this->componentItems = $this->modelComponent->getAll();
        $this->authorizerItems = $this->modelAuthorizer->getAll();

        $this->userTagItems = $this->modelUserTag->getAllByType("tag_id");
        $this->languageItems = $this->modelLanguage->getAll();

        parent::initialize();
    }
    protected $componentItems = null;
    protected $authorizerItems = null;
    protected $userTagItems = null;
    protected $languageItems = null;

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
        $schemas['matchrule_name'] = array(
            'name' => '匹配规则名',
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
        $schemas['tag_id'] = array(
            'name' => '用户标签的id',
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
                'items' => $this->userTagItems,
                'help' => '用户标签的id，可通过用户标签管理接口获取',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->userTagItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->userTagItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        // sex 性别：男（1）女（2），不填则不做匹配
        $sexOptions[0] = "";
        $sexOptions[1] = "男";
        $sexOptions[2] = "女";
        $schemas['sex'] = array(
            'name' => '性别',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $sexOptions,
                'help' => '性别：男（1）女（2），不填则不做匹配',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $sexOptions,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $sexOptions,
            ),
            'export' => array(
                'is_show' => true
            )
        );

        // client_platform_type 客户端版本，当前只具体到系统型号：IOS(1), Android(2),Others(3)，不填则不做匹配
        $clientPlatformTypeOptions[0] = "";
        $clientPlatformTypeOptions[1] = "IOS";
        $clientPlatformTypeOptions[2] = "Android";
        $clientPlatformTypeOptions[3] = "Others";

        $schemas['client_platform_type'] = array(
            'name' => '客户端版本',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $clientPlatformTypeOptions,
                'help' => '客户端版本，当前只具体到系统型号：IOS(1), Android(2),Others(3)，不填则不做匹配',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $clientPlatformTypeOptions,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $clientPlatformTypeOptions,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['country'] = array(
            'name' => '国家信息',
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
                'help' => '国家信息，是用户在微信中设置的地区，具体请参考地区信息表',
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
        $schemas['province'] = array(
            'name' => '省份信息',
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
                'help' => '省份信息，是用户在微信中设置的地区，具体请参考地区信息表',
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
        $schemas['city'] = array(
            'name' => '城市信息',
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
                'help' => '城市信息，是用户在微信中设置的地区，具体请参考地区信息表',
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
        $schemas['language'] = array(
            'name' => '语言信息',
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
                'items' => $this->languageItems,
                'help' => '语言信息，是用户在微信中设置的语言，具体请参考语言表： 1、简体中文 "zh_CN" 2、繁体中文TW "zh_TW" 3、繁体中文HK "zh_HK" 4、英文 "en" 5、印尼 "id" 6、马来 "ms" 7、西班牙 "es" 8、韩国 "ko" 9、意大利 "it" 10、日本 "ja" 11、波兰 "pl" 12、葡萄牙 "pt" 13、俄国 "ru" 14、泰文 "th" 15、越南 "vi" 16、阿拉伯语 "ar" 17、北印度 "hi" 18、希伯来 "he" 19、土耳其 "tr" 20、德语 "de" 21、法语 "fr"',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->languageItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->languageItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '个性化菜单匹配规则设置';
    }

    protected function getModel()
    {
        return $this->modelConditionalMatchrule;
    }
}
