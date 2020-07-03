<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CardType;
use App\Backend\Submodules\Weixincard\Models\CodeType;
use App\Backend\Submodules\Weixincard\Models\Color;
use App\Backend\Submodules\Weixincard\Models\Card;
use App\Backend\Submodules\Weixincard\Models\DateInfoType;
use App\Backend\Submodules\Weixincard\Models\Logo;
use App\Backend\Submodules\Weixincard\Models\CodeDeposit;

/**
 * @title({name="卡券"})
 *
 * @name 卡券
 */
class CardController extends \App\Backend\Controllers\FormController
{

    private $modelCard;

    private $modelCardType;

    private $modelCodeType;

    private $modelColor;

    private $modelDateInfoType;

    private $modelLogo;

    private $modelCodeDeposit;

    public function initialize()
    {
        $this->modelCard = new Card();
        $this->modelCardType = new CardType();
        $this->modelCodeType = new CodeType();
        $this->modelColor = new Color();
        $this->modelDateInfoType = new DateInfoType();
        $this->modelLogo = new Logo();
        $this->modelCodeDeposit = new CodeDeposit();

        $this->cardTypeList = $this->modelCardType->getAll();
        $this->codeTypeList = $this->modelCodeType->getAll();
        $this->colorList = $this->modelColor->getAll();
        $this->dateInfoTypeList = $this->modelDateInfoType->getAll();

        parent::initialize();
    }

    private $cardTypeList = null;
    private $codeTypeList = null;
    private $colorList = null;
    private $dateInfoTypeList = null;


    protected function getFormTools2($tools)
    {

        $tools['createcard'] = array(
            'title' => '创建卡券',
            'action' => 'createcard',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (empty($item['card_id'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['getcardinfo'] = array(
            'title' => '获取卡券信息',
            'action' => 'getcardinfo',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['card_id'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['masssend'] = array(
            'title' => '发送卡券消息',
            'action' => 'masssend',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['card_id'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['updatecardinfo'] = array(
            'title' => '更新卡券',
            'action' => 'updatecardinfo',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['card_id'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['modifystock'] = array(
            'title' => '修改库存',
            'action' => 'modifystock',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['card_id'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['depositecode'] = array(
            'title' => '导入自定义code',
            'action' => 'depositecode',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['card_id'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['checkcode'] = array(
            'title' => '核查自定义code',
            'action' => 'checkcode',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['card_id'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['getdepositcodecount'] = array(
            'title' => '查询导入code数目',
            'action' => 'getdepositcodecount',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['card_id'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );

        return $tools;
    }

    protected function getSchemas2($schemas)
    {
        $schemas['_id']['list']['is_show'] = false;
        $use_custom_code = true;
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);


        $schemas['card_id'] = array(
            'name' => '微信公众平台的卡券ID',
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
        $schemas['card_type'] = array(
            'name' => '卡券类型',
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
                'items' => $this->cardTypeList,
                'help' => '卡券类型 例如GENERAL_COUPON',
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->cardTypeList,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->cardTypeList,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['logo_url'] = array(
            'name' => '卡券的商户logo',
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
                'name' => '卡券的商户logo，尺寸为 300*300 例如http://mmbiz.qpic.cn/',
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
        $schemas['code_type'] = array(
            'name' => 'code码展示类型',
            'data' => array(
                'type' => 'string',
                'length' => 16,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->codeTypeList,
                'help' => 'code码展示类型 例如CODE_TYPE_TEXT',
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->codeTypeList,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->codeTypeList,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['brand_name'] = array(
            'name' => '商户名字',
            'data' => array(
                'type' => 'string',
                'length' => 20,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '商户名字,字数上限为12个汉字 例如海底捞',
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
        $schemas['title'] = array(
            'name' => '券名',
            'data' => array(
                'type' => 'string',
                'length' => 27,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '券名，字数上限为9个汉字 例如双人套餐100元兑换券',
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
        $schemas['sub_title'] = array(
            'name' => '券名的副标题',
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
                'items' => '',
                'help' => '券名的副标题，字数上限为18个汉字 例如鸳鸯锅底+牛肉1份+土豆一份',
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
        $schemas['color'] = array(
            'name' => '券颜色',
            'data' => array(
                'type' => 'string',
                'length' => 16,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->colorList,
                'help' => '券颜色。按色彩规范标注填写Color010-Color100',
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->colorList,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->colorList,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['notice'] = array(
            'name' => '卡券使用提醒',
            'data' => array(
                'type' => 'string',
                'length' => 20,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '卡券使用提醒，字数上限为16个汉字。例如请出示二维码核销卡券',
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
            'name' => '卡券使用说明',
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
                'help' => '卡券使用说明，字数上限为1024个汉字。例如不可与其他优惠同享/n如需团购券发票，请向店员提出要求。',
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
        $schemas['sku_quantity'] = array(
            'name' => '卡券库存的数量',
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
                'items' => '',
                'help' => '卡券库存的数量，上限为100000000 例如10000',
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
        $schemas['date_info_type'] = array(
            'name' => '使用时间的类型',
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
                'items' => $this->dateInfoTypeList,
                'help' => '使用时间的类型，旧文档采用的1和2依然生效',
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->dateInfoTypeList,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->dateInfoTypeList,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['date_info_begin_timestamp'] = array(
            'name' => '起用时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true,
                'items' => '',
                'help' => 'type为DATE_TYPE_FIX_TIME_RANGE时专用，表示起用时间。从1970年1月1日00:00:00至起用时间的秒数，最终需转换为字符串形态传入。（东八区时间，单位为秒）',

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
        $schemas['date_info_end_timestamp'] = array(
            'name' => '卡券统一的结束时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 30 - 1)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true,
                'items' => '',
                'help' => 'type为DATE_TYPE_FIX_TIME_RANGE时，表示卡券统一的结束时间，建议设置为截止日期的23:59:59过期。（东八区时间，单位为秒）',

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
        $schemas['date_info_fixed_term'] = array(
            'name' => '自领取后多少天内有效',
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
                'help' => 'type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天内有效，不支持填写0。例如15',
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
        $schemas['date_info_fixed_begin_term'] = array(
            'name' => '自领取后多少天开始生效',
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
                'help' => '	type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天开始生效，领取后当天生效填写0。（单位为天）例如0',
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
        $schemas['date_info_fixed_end_timestamp'] = array(
            'name' => '卡券统一过期时间',
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
                'items' => '',
                'help' => '可用于DATE_TYPE_FIX_TERM时间类型，表示卡券统一过期时间，建议设置为截止日期的23:59:59过期。（东八区时间，单位为秒），设置了fixed_term卡券，当时间达到end_timestamp时卡券统一过期',
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
        $schemas['use_custom_code'] = array(
            'name' => '是否自定义Code码',
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
                'help' => '是否自定义Code码。填写true或false，默认为false。通常自有优惠码系统的开发者选择自定义Code码，并在卡券投放时带入Code码，详情见是否自定义Code码。',
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
        $schemas['bind_openid'] = array(
            'name' => '是否指定用户领取',
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
                'help' => '是否指定用户领取，填写true或false。默认为false。通常指定特殊用户群体投放卡券或防止刷券时选择指定用户领取。',
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
        $schemas['service_phone'] = array(
            'name' => '客服电话',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '客服电话 例如40012234',
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
        $schemas['location_id_list'] = array(
            'name' => '门店位置ID',
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
                'items' => '',
                'help' => '门店位置ID 例如1234,2312',
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
        $schemas['source'] = array(
            'name' => '第三方来源名',
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
                'items' => '',
                'help' => '第三方来源名，例如同程旅游、 格瓦拉',
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
        $schemas['center_title'] = array(
            'name' => '卡券顶部居中的按钮',
            'data' => array(
                'type' => 'string',
                'length' => 18,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '卡券顶部居中的按钮，仅在卡券状态正常(可以核销)时显示，建议开发者设置此按钮时code_type选择CODE_TYPE_NONE类型。例如立即使用',
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
        $schemas['center_sub_title'] = array(
            'name' => '入口下方的提示语',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '显示在入口下方的提示语，仅在卡券状态正常(可以核销)时显示。例如立即享受优惠',
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
        $schemas['center_url'] = array(
            'name' => '顶部居中的url',
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
                'help' => '顶部居中的url，仅在卡券状态正常(可以核销)时显示。例如www.xxx.com',
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
        $schemas['custom_url_name'] = array(
            'name' => '自定义跳转外链的入口名字',
            'data' => array(
                'type' => 'string',
                'length' => 15,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '自定义跳转外链的入口名字。详情见活用自定义入口 例如立即使用',
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
        $schemas['custom_url'] = array(
            'name' => '自定义跳转的URL',
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
                'help' => '自定义跳转的URL。例如xxxx.com',
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
        $schemas['custom_url_sub_title'] = array(
            'name' => '入口右侧的提示语',
            'data' => array(
                'type' => 'string',
                'length' => 18,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '显示在入口右侧的提示语。例如更多惊喜',
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
        $schemas['promotion_url_name'] = array(
            'name' => '营销场景的自定义入口名称',
            'data' => array(
                'type' => 'string',
                'length' => 15,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '营销场景的自定义入口名称。例如产品介绍',
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
        $schemas['promotion_url'] = array(
            'name' => '入口跳转外链的地址链接',
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
                'help' => '入口跳转外链的地址链接。例如xxxx.com',
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
        $schemas['promotion_url_sub_title'] = array(
            'name' => '营销入口右侧的提示语',
            'data' => array(
                'type' => 'string',
                'length' => 18,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '显示在营销入口右侧的提示语。例如更多惊喜',
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
        $schemas['use_limit'] = array(
            'name' => '每人使用次数限制',
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
        $schemas['get_limit'] = array(
            'name' => '每人可领券的数量限制',
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
                'help' => '每人可领券的数量限制,不填写默认为50',
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
        $schemas['can_share'] = array(
            'name' => '卡券领取页面是否可分享',
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
        $schemas['can_give_friend'] = array(
            'name' => '卡券是否可转赠。',
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
        $schemas['status'] = array(
            'name' => '卡券状态',
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
        $schemas['get_custom_code_mode'] = array(
            'name' => '获取自定义code的方式',
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
        $schemas['shake_slogan_title'] = array(
            'name' => '新年祝语标题',
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
        $schemas['shake_slogan_sub_title'] = array(
            'name' => '新年祝语正文',
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
        $schemas['can_shake'] = array(
            'name' => '参加摇礼券活动的标志位',
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
        $schemas['weixin_title'] = array(
            'name' => '微信公众推送标题',
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
        $schemas['weixin_image'] = array(
            'name' => '微信公众推送封面图片',
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
        $schemas['weixin_desc'] = array(
            'name' => '微信公众推送摘要',
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
        $schemas['weixin_url'] = array(
            'name' => '微信公众推送URL',
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
        $schemas['general_coupon_default_detail'] = array(
            'name' => '优惠详情',
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
                'help' => '优惠券专用，优惠详情。例如音乐木盒。',
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
        $schemas['groupon_deal_detail'] = array(
            'name' => '团购详情',
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
                'help' => '团购券专用，团购详情。例如双人套餐\n -进口红酒一支。\n孜然牛肉一份',
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
        $schemas['discount_discount'] = array(
            'name' => '打折额度（百分比）',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'name' => '折扣券专用，表示打折额度（百分比）。填30就是七折。',
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
        $schemas['gift_gift'] = array(
            'name' => '兑换内容',
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
                'items' => '',
                'help' => '兑换券专用，兑换内容。例如可兑换音乐木盒一个。',
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
        $schemas['cash_least_cost'] = array(
            'name' => '起用金额（单位为分）',
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
                'help' => '代金券专用，表示起用金额（单位为分）。,如果无起用门槛则填0。',
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
        $schemas['cash_reduce_cost'] = array(
            'name' => '减免金额（单位为分）',
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
                'help' => '代金券专用，表示减免金额（单位为分）。,如果无起用门槛则填0。',
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
        $schemas['member_card_supply_bonus'] = array(
            'name' => '是否支持积分',
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
                'items' => $this->trueOrFalseDatas,
                'help' => '是否支持积分，填写 true 或 false，如填写 true，积分相关字段均为必填。填写 false，积分字段无需填写。储值字段处理方式相同。',
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
        $schemas['member_card_supply_balance'] = array(
            'name' => '是否支持储值',
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
                'items' => $this->trueOrFalseDatas,
                'help' => '是否支持储值，填写 true 或false。 （该权限申请及说明详见Q&A 文档)',
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
        $schemas['member_card_custom_field1_name_type'] = array(
            'name' => '自定义会员信息类目1名称',
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
        $schemas['member_card_custom_field1_url'] = array(
            'name' => '自定义会员信息类目1自定义跳转url',
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
        $schemas['member_card_custom_field2_name_type'] = array(
            'name' => '自定义会员信息类目2名称',
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
        $schemas['member_card_custom_field2_url'] = array(
            'name' => '自定义会员信息类目2自定义跳转url',
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
        $schemas['member_card_custom_field3_name_type'] = array(
            'name' => '自定义会员信息类目3名称',
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
        $schemas['member_card_custom_field3_url'] = array(
            'name' => '自定义会员信息类目3自定义跳转url',
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
        $schemas['member_card_bonus_cleared'] = array(
            'name' => '积分清零规则',
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
        $schemas['member_card_bonus_rules'] = array(
            'name' => '积分规则',
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
        $schemas['member_card_balance_rules'] = array(
            'name' => '储值说明',
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
        $schemas['member_card_prerogative'] = array(
            'name' => '特权说明',
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
        $schemas['member_card_bind_old_card_url'] = array(
            'name' => '绑定旧卡的url',
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
                'help' => '绑定旧卡的 url，与“ activate_url”字段二选一必填，用户领取会员卡后显示“ 绑定会员卡” 。',
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
        $schemas['member_card_activate_url'] = array(
            'name' => '激活会员卡的url',
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
                'help' => '激活会员卡的 url，与“ bind_old_card_url”字段二选一必填。 用户领取会员卡后显示“激活会员卡”。',
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
        $schemas['member_card_need_push_on_view'] = array(
            'name' => '用户点击进入会员卡时是否推送事件',
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
                'items' => $this->trueOrFalseDatas,
                'help' => 'true 为用户点击进入会员卡时是否推送事件。',
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
        $schemas['member_card_custom_cell1_name'] = array(
            'name' => '营销入口1入口名称',
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
                'help' => '会员卡类型专属营销入口1入口名称',
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
        $schemas['member_card_custom_cell1_tips'] = array(
            'name' => '营销入口1入口右侧提示语',
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
                'items' => '',
                'help' => '会员卡类型专属营销入口1入口右侧提示语，6个汉字内',
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
        $schemas['member_card_custom_cell1_url'] = array(
            'name' => '销入口1入口跳转链接',
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
                'help' => '会员卡类型专属营销入口1入口跳转链接',
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
        $schemas['member_card_custom_cell2_name'] = array(
            'name' => '营销入口2入口名称',
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
                'help' => '会员卡类型专属营销入口2入口名称',
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
        $schemas['member_card_custom_cell2_tips'] = array(
            'name' => '营销入口2入口右侧提示语',
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
                'items' => '',
                'help' => '会员卡类型专属营销入口2入口右侧提示语，6个汉字内',
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
        $schemas['member_card_custom_cell2_url'] = array(
            'name' => '营销入口2入口跳转链接',
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
                'help' => '会员卡类型专属营销入口2入口跳转链接',
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
        $schemas['scenic_ticket_ticket_class'] = array(
            'name' => '票类型',
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
                'items' => '',
                'help' => '景区门票专用，票类型 例如平日全票，套票等。',
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
        $schemas['scenic_ticket_guide_url'] = array(
            'name' => '景区导览图url',
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
                'items' => '',
                'help' => '景区门票专用，导览图url 例如xxx.com',
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
        $schemas['movie_ticket_detail'] = array(
            'name' => '电影票详情',
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
                'items' => '',
                'help' => '电影票类型专用，电影票详情 例如电影名：xxx，电影简介：xxx。',
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
        $schemas['boarding_pass_from'] = array(
            'name' => '起点',
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
                'items' => '',
                'help' => '飞机票类型专用，起点，上限为18个汉字。例如成都',
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
        $schemas['boarding_pass_to'] = array(
            'name' => '终点',
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
                'items' => '',
                'help' => '飞机票类型专用，终点，上限为18个汉字。例如广州',
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
        $schemas['boarding_pass_flight'] = array(
            'name' => '航班',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '飞机票类型专用，航班 例如CE123',
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
        $schemas['boarding_pass_gate'] = array(
            'name' => '入口',
            'data' => array(
                'type' => 'string',
                'length' => 12,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '飞机票类型专用，入口，上限为4个汉字。例如A11',
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
        $schemas['boarding_pass_check_in_url'] = array(
            'name' => '在线值机的链接',
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
                'help' => '飞机票类型专用，在线值机的链接 例如xxx.com',
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
        $schemas['boarding_pass_air_model'] = array(
            'name' => '飞机机型',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '飞机票类型专用，机型，上限为8个汉字。例如空客A320',
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
        $schemas['boarding_pass_departure_time'] = array(
            'name' => '飞机起飞时间',
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
                'items' => '',
                'help' => '飞机票类型专用，起飞时间。Unix时间戳格式。',
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
        $schemas['boarding_pass_landing_time'] = array(
            'name' => '飞机降落时间',
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
                'items' => '',
                'help' => '飞机票类型专用，降落时间。Unix时间戳格式。',
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
        $schemas['meeting_ticket_meeting_detail'] = array(
            'name' => '会议详情',
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
                'items' => '',
                'help' => '会议门票类型专用，会议详情 例如本次会议于2015年5月10号在广州举行，会场地点：xxxx。',
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
        $schemas['meeting_ticket_map_url'] = array(
            'name' => '会场导览图',
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
                'items' => '',
                'help' => '会议门票类型专用，会场导览图 例如xxx.com',
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
        $schemas['received_num'] = array(
            'name' => '领取数',
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
        $schemas['consumed_num'] = array(
            'name' => '核销数',
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
        $schemas['deleted_num'] = array(
            'name' => '删除数',
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
        $schemas['unavailable_num'] = array(
            'name' => '失效数',
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
        $schemas['give_by_friend_num'] = array(
            'name' => '转赠朋友数',
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
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
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

        return $schemas;
    }

    protected function getName()
    {
        return '微信卡券';
    }

    protected function getModel()
    {
        return $this->modelCard;
    }

    /**
     * @title({name="创建卡券"})
     * 在微信公众平台上创建卡券的Hook
     *
     * @name 创建卡券
     */
    public function createcardAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/card/createcard?id=xxxx
        try {


            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);

            $id = $this->get('id', '');
            if (empty($id)) {
                $cards = $this->modelCard->getAll();
            } else {
                $cardInfo = $this->modelCard->getInfoById($id);
                $cards = array(
                    $cardInfo
                );
            }

            $colors = $this->modelColor->getAll();
            if (!empty($cards)) {
                foreach ($cards as $card) {
                    if (!empty($card['card_id'])) { // 如果已生成的话
                        continue;
                    }
                    $this->modelCard->create($card, $colors);
                }
            }
            // $this->makeJsonResult();
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功创建卡券');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="获取卡券信息"})
     * 从微信公众平台上获取最新的卡券信息
     *
     * @name 获取卡券信息
     */
    public function getcardinfoAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/card/getcardinfo?card_id=pgW8rt5vzjJ7nFLYxskYGBtxZP3k
        try {


            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);

            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", -1);
            }
            $cardInfo = $this->modelCard->getInfoById($id);
            if (empty($cardInfo)) {
                throw new \Exception("id不正确", -2);
            }
            if (empty($cardInfo['card_id'])) {
                throw new \Exception("card_id未生成", -3);
            }
            $card_id = $cardInfo['card_id'];

            $ret = $weixin->getCardManager()->get($card_id);
            // $this->makeJsonResult($ret);
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功获取卡券信息:' . \json_encode($ret));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="发送卡券消息"})
     *
     * @name 发送卡券消息
     */
    public function masssendAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/card/masssend?card_id=pgW8rt5vzjJ7nFLYxskYGBtxZP3k&toUsers=o4ELSvz-B4_DThF0Vpfrverk3IpY,o4ELSv7CChC3YKmM8WKXX4kXSr8c&preview=1
        try {


            $weixin = $this->getWeixin();

            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelCard->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = array();
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );
                $fields['card_id'] = array(
                    'name' => '微信卡券ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );

                $fields['toUsers'] = array(
                    'name' => '发送的微信openid列表,逗号分隔',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'textarea',
                        'is_show' => true,
                        // 'readonly' => true,
                    ),
                );
                $fields['preview'] = array(
                    'name' => '是否预览',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'radio',
                        'is_show' => true,
                        'items' => $this->trueOrFalseDatas
                        // 'readonly' => true,
                    ),
                );

                $title = "修改所属活动";
                return $this->showModal($title, $fields, $row);
            } else {

                $card_id = $row['card_id']; //$this->get('card_id', '');
                $toUsers = $this->get('toUsers', '');
                $preview = intval($this->get('preview', '1'));
                $toUsers = explode(',', $toUsers);

                if ($preview) {
                    $signature = $this->getSignature($card_id, '', $toUsers[0]);
                    $params = array(
                        'touser' => $toUsers[0],
                        'wxcard' => array(
                            'card_id' => $card_id,
                            'card_ext' => $signature['card_ext']
                        ),
                        'msgtype' => 'wxcard'
                    );
                    $ret = $weixin->getMsgManager()
                        ->getMassSender()
                        ->preview($params);
                } else {
                    $ret = $weixin->getMsgManager()
                        ->getMassSender()
                        ->sendWxcardByOpenid($toUsers, $card_id);
                }

                // $this->makeJsonResult($ret);
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功修改库存:' . \json_encode($ret));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="更新卡券消息"})
     *
     * 从微信公众平台上获取最新的卡券信息更新本地的Hook
     *
     * @name 更新卡券消息
     */
    public function updatecardinfoAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/card/updatecardinfo?id=xxx
        try {


            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);

            $id = $this->get('id', '');

            if (empty($id)) { // 如果没有指定卡券ID,那么根据卡券类别获取列表
                $cards = $this->modelCard->getAll();
            } else {
                $cardInfo = $this->modelCard->getInfoById($id);
                $cards = array(
                    $cardInfo
                );
            }
            if (!empty($cards)) {
                foreach ($cards as $item) {
                    if (empty($item["card_id"])) {
                        continue;
                    }
                    // 获取最新的卡券信息并且更新本地的信息
                    $this->modelCard->getAndUpdateCardInfo($item["card_id"]);
                }
            }
            // $this->makeJsonResult();
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功更新卡券消息');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="修改库存"})
     *
     * @name 修改库存
     */
    public function modifystockAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/card/modifystock?card_id=p4ELSv6c5tkQvR6ihip_Z1KdNm8c&increase_stock_value=1&reduce_stock_value=0
        try {
            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);

            $id = trim($this->request->get('id'));
            if (empty($id)) {
                return $this->makeJsonError("记录ID未指定");
            }
            $row = $this->modelCard->getInfoById($id);
            if (empty($row)) {
                return $this->makeJsonError("id：{$id}的记录不存在");
            }

            // 如果是GET请求的话返回modal的内容
            if ($this->request->isGet()) {
                // 构建modal里面Form表单内容
                $fields = array();
                $fields['_id'] = array(
                    'name' => '记录ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'hidden',
                        'is_show' => true
                    ),
                );
                $fields['card_id'] = array(
                    'name' => '微信卡券ID',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'text',
                        'is_show' => true,
                        'readonly' => true,
                    ),
                );

                $fields['increase_stock_value'] = array(
                    'name' => '增加的库存数',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'number',
                        'is_show' => true,
                        // 'readonly' => true,
                    ),
                );
                $fields['reduce_stock_value'] = array(
                    'name' => '减少的库存数',
                    'validation' => array(
                        'required' => true
                    ),
                    'form' => array(
                        'input_type' => 'number',
                        'is_show' => true,
                        // 'readonly' => true,
                    ),
                );

                $title = "修改所属活动";
                return $this->showModal($title, $fields, $row);
            } else {
                $card_id = $row['card_id']; //$this->get('card_id', '');
                $increase_stock_value = intval($this->get('increase_stock_value', '0'));
                $reduce_stock_value = intval($this->get('reduce_stock_value', '0'));
                if (empty($card_id)) {
                    throw new \Exception("card_id未指定", -1);
                }
                if ($increase_stock_value < 0) {
                    throw new \Exception("increase_stock_value未指定", -2);
                }
                if ($reduce_stock_value < 0) {
                    throw new \Exception("reduce_stock_value未指定", -2);
                }
                $cardManager = $weixin->getCardManager();
                $rst = $cardManager->modifyStock($card_id, $increase_stock_value, $reduce_stock_value);

                if (!empty($rst['errcode'])) {
                    // 如果有异常，会在errcode 和errmsg 描述出来。
                    throw new \Exception($rst['errmsg'], $rst['errcode']);
                }

                // 获取最新的卡券信息并且更新本地的信息
                $this->modelCard->getAndUpdateCardInfo($card_id);

                // $this->makeJsonResult(json_encode($rst));
                return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功修改库存:' . \json_encode($rst));
            }
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="导入code"})
     *
     * @name 导入code
     *      
     *       开发者需调用该接口将自定义code 导入微信卡券后台，由微信侧代理存储并下发
     *       code，本接口仅用于支持微信摇卡券活动。
     *       注：
     *       1）单次调用接口传入code 的数量上限为100 个。
     *       2）每一个 code 均不能为空串，且不能重复填入相同code，否则会导入失败。
     *       3）导入失败支持重复导入，提示成功为止。
     *      
     *       一次性处理,不用计划任务配置
     *       如果导入的数据量过大,那么可以使用多进程 来导入数据
     *       可以先用getidsAction获取每个进程的数据处理范围
     */
    public function depositecodeAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/card/depositecode?id=xxx
        try {
            $weixin = $this->getWeixin();
            $this->modelCodeDeposit->setWeixin($weixin);

            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", -1);
            }
            $cardInfo = $this->modelCard->getInfoById($id);
            if (empty($cardInfo)) {
                throw new \Exception("id不正确", -2);
            }
            if (empty($cardInfo['card_id'])) {
                throw new \Exception("card_id未生成", -3);
            }

            // 导入自定义卡券code处理
            $rst = $this->modelCodeDeposit->depositeCode($cardInfo['card_id']);

            // $this->makeJsonResult();
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功修改库存:' . \json_encode($rst));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="核查code"})
     *
     * @name 核查code
     */
    public function checkcodeAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/card/checkcode?id=xxx
        try {
            $weixin = $this->getWeixin();
            $this->modelCodeDeposit->setWeixin($weixin);

            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", -1);
            }
            $cardInfo = $this->modelCard->getInfoById($id);
            if (empty($cardInfo)) {
                throw new \Exception("id不正确", -2);
            }
            if (empty($cardInfo['card_id'])) {
                throw new \Exception("card_id未生成", -3);
            }

            // 核查code处理
            $rst = $this->modelCodeDeposit->checkCode($cardInfo['card_id']);

            // $this->makeJsonResult();
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功核查code:' . \json_encode($rst));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="查询导入code数目"})
     *
     * 从微信公众平台上获取最新的卡券信息
     *
     * @name 查询导入code数目
     */
    public function getdepositcodecountAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/card/getdepositcodecount?card_id=xxx
        try {


            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);

            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", -1);
            }
            $cardInfo = $this->modelCard->getInfoById($id);
            if (empty($cardInfo)) {
                throw new \Exception("id不正确", -2);
            }
            if (empty($cardInfo['card_id'])) {
                throw new \Exception("card_id未生成", -3);
            }

            $card_id = $cardInfo['card_id'];

            $ret = $weixin->getCardManager()->codeGetDepositCount($card_id);
            // $this->makeJsonResult($ret);
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功查询导入code数目' . \json_encode($ret));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
