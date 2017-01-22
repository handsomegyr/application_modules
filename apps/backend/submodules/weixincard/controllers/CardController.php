<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CardType;
use App\Backend\Submodules\Weixincard\Models\CodeType;
use App\Backend\Submodules\Weixincard\Models\Color;
use App\Backend\Submodules\Weixincard\Models\Card;
use App\Backend\Submodules\Weixincard\Models\DateInfoType;
use App\Backend\Submodules\Weixincard\Models\Logo;

/**
 * @title({name="微信卡券管理"})
 *
 * @name 微信卡券管理
 */
class CardController extends \App\Backend\Controllers\FormController
{

    private $modelCard;

    private $modelCardType;

    private $modelCodeType;

    private $modelColor;

    private $modelDateInfoType;

    private $modelLogo;

    public function initialize()
    {
        $this->modelCard = new Card();
        $this->modelCardType = new CardType();
        $this->modelCodeType = new CodeType();
        $this->modelColor = new Color();
        $this->modelDateInfoType = new DateInfoType();
        $this->modelLogo = new Logo();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['_id']['list']['is_show'] = false;
        $use_custom_code = true;
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['card_id'] = array(
            'name' => '微信卡券ID',
            'data' => array(
                'type' => 'string',
                'length' => '32'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            )
        );
        
        $schemas['card_type'] = array(
            'name' => '卡券类型',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => 'GENERAL_COUPON'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelCardType->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'card_type_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['status'] = array(
            'name' => '卡券状态',
            'data' => array(
                'type' => 'string',
                'length' => '30',
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['code_type'] = array(
            'name' => 'code码展示类型',
            'data' => array(
                'type' => 'string',
                'length' => '16',
                'defaultValue' => 'CODE_TYPE_TEXT'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelCodeType->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'code_type_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 获取最新的上传logo
        $logoInfo = $this->modelLogo->getLatestLogo();
        if (empty($logoInfo)) {
            $logo_url = '';
        } else {
            $logo_url = $logoInfo['logo_url'];
        }
        $schemas['logo_url'] = array(
            'name' => '商户logo，尺寸为 300*300',
            'data' => array(
                'type' => 'string',
                'length' => 128,
                'defaultValue' => $logo_url
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 商户名字,字数上限为12个汉字
        $schemas['brand_name'] = array(
            'name' => '商户名字(字数上限为12个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '36',
                'defaultValue' => '海底捞'
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
                'is_show' => true
            )
        );
        
        // 券名，字数上限为9个汉字
        $schemas['title'] = array(
            'name' => '券名(字数上限为9个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '27',
                'defaultValue' => '双人套餐100元兑换券'
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
        
        // 券名的副标题，字数上限为18个汉字
        $schemas['sub_title'] = array(
            'name' => '副标题(字数上限为18个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '54',
                'defaultValue' => '鸳鸯锅底+牛肉1份+土豆一份'
            ),
            'validation' => array(
                'required' => false
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
        // 券颜色。按色彩规范标注填写Color010-Color100
        $schemas['color'] = array(
            'name' => '券颜色',
            'data' => array(
                'type' => 'string',
                'length' => '16',
                'defaultValue' => '#63B359'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelColor->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'color_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 卡券使用提醒，字数上限为16个汉字。
        $schemas['notice'] = array(
            'name' => '使用提醒(字数上限为16个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '48',
                'defaultValue' => '请出示二维码核销卡券'
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
        
        // 卡券使用说明，字数上限为1024个汉字。
        $schemas['description'] = array(
            'name' => '使用说明(字数上限为1024个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '3072',
                'defaultValue' => '•       优惠券使用规则：
•       使用时间为：即日起至2016年12月31日，单份优惠重复使用无效。
•       使用范围：本券仅限用于在全国来伊份门店、官方商城 www.laiyifen.com，来伊份APP。
•       使用规则：全品类通用券，消费满88元可抵用8元，多买多抵。
•       本券限量20000张，领完为止，优惠金额不可兑换现金，不设找零，不可累积使用，不可与店内其他优惠叠加使用。
•       本活动奖品只作优惠抵用，不能兑换现金，非销售行为，恕不提供优惠金额部分发票。
•       此券不可用于其他商品优惠，也不适用会员卡充值及伊点卡购买。
•       咨询热线：400-8819-777（来伊份热线营运时间：08:00-21:00，市内话费自付。）'
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
        
        // 卡券库存的数量，上限为100000000
        $schemas['sku_quantity'] = array(
            'name' => '库存数量，上限为100000000',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => $use_custom_code ? 0 : 10
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
        
        // 使用时间的类型，旧文档采用的1和2依然生效。DATE_TYPE_FIX_TIME_RANGE 表示固定日期区间，DATE_TYPE_FIX_TERM表示固定时长（自领取后按天算。
        $schemas['date_info_type'] = array(
            'name' => '使用时间类型',
            'data' => array(
                'type' => 'string',
                'length' => '30',
                'defaultValue' => 'DATE_TYPE_FIX_TIME_RANGE'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelDateInfoType->getAll();
                }
            ),
            'list' => array(
                'is_show' => false,
                'list_data_name' => 'date_info_type_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // type为DATE_TYPE_FIX_TIME_RANGE时专用，表示起用时间。从1970年1月1日00:00:00至起用时间的秒数，最终需转换为字符串形态传入。（东八区时间，单位为秒）
        $schemas['date_info_begin_timestamp'] = array(
            'name' => '固定日期区间-起用时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // type为DATE_TYPE_FIX_TIME_RANGE时，表示卡券统一的结束时间，建议设置为截止日期的23:59:59过期。（东八区时间，单位为秒）
        $schemas['date_info_end_timestamp'] = array(
            'name' => '固定日期区间-结束时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 30 - 1)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天内有效，不支持填写0。
        $schemas['date_info_fixed_term'] = array(
            'name' => '固定时长-领取后多少天内有效(单位为天)',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 15
            ),
            'validation' => array(
                'required' => false
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
        
        // type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天开始生效，领取后当天生效填写0。（单位为天）
        $schemas['date_info_fixed_begin_term'] = array(
            'name' => '固定时长-领取后多少天开始生效(单位为天)',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
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
        
        // 可用于DATE_TYPE_FIX_TERM时间类型，表示卡券统一过期时间，建议设置为截止日期的23:59:59过期。（东八区时间，单位为秒），设置了fixed_term卡券，当时间达到end_timestamp时卡券统一过期
        $schemas['date_info_fixed_end_timestamp'] = array(
            'name' => '固定时长-领取后统一过期时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 30 - 1)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 是否自定义Code码。填写true或false，默认为false。通常自有优惠码系统的开发者选择自定义Code码，并在卡券投放时带入Code码，详情见是否自定义Code码。
        $schemas['use_custom_code'] = array(
            'name' => '是否自定义code码',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => $use_custom_code
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
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 是否指定用户领取，填写true或false。默认为false。通常指定特殊用户群体投放卡券或防止刷券时选择指定用户领取。
        $schemas['bind_openid'] = array(
            'name' => '是否指定用户领取',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
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
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 客服电话
        $schemas['service_phone'] = array(
            'name' => '客服电话',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => '4008819777'
            ),
            'validation' => array(
                'required' => false
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
        
        // 门店位置poiid。调用POI门店管理接口获取门店位置poiid。具备线下门店的商户为必填 例如1234，2312
        $schemas['location_id_list'] = array(
            'name' => '门店位置ID',
            'data' => array(
                'type' => 'string',
                'length' => '3072'
            ),
            'validation' => array(
                'required' => false
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
        
        // 第三方来源名，例如同程旅游、 格瓦拉
        $schemas['source'] = array(
            'name' => '第三方来源名(例如同程旅游、 格瓦拉)',
            'data' => array(
                'type' => 'string',
                'length' => '36',
                'defaultValue' => '大众点评'
            ),
            'validation' => array(
                'required' => false
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
        
        // 卡券顶部居中的按钮，仅在卡券状态正常(可以核销)时显示，建议开发者设置此按钮时code_type选择CODE_TYPE_NONE类型。
        $schemas['center_title'] = array(
            'name' => '卡券顶部居中的按钮',
            'data' => array(
                'type' => 'string',
                'length' => '15',
                'defaultValue' => '立即使用'
            ),
            'validation' => array(
                'required' => false
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
        
        // 顶部居中的url，仅在卡券状态正常(可以核销)时显示。
        $schemas['center_url'] = array(
            'name' => '顶部居中的url',
            'data' => array(
                'type' => 'string',
                'length' => '128',
                'defaultValue' => 'xxxx.com'
            ),
            'validation' => array(
                'required' => false
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
        // 显示在入口下方的提示语，仅在卡券状态正常(可以核销)时显示。例如立即享受优惠
        $schemas['center_sub_title'] = array(
            'name' => '显示在入口下方的提示语',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => '立即享受优惠'
            ),
            'validation' => array(
                'required' => false
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
        
        // 自定义跳转外链的入口名字。详情见活用自定义入口
        $schemas['custom_url_name'] = array(
            'name' => '自定义跳转外链的入口名字',
            'data' => array(
                'type' => 'string',
                'length' => '15',
                'defaultValue' => '立即使用'
            ),
            'validation' => array(
                'required' => false
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
        
        // 自定义跳转的URL。
        $schemas['custom_url'] = array(
            'name' => '自定义跳转的URL',
            'data' => array(
                'type' => 'string',
                'length' => '128',
                'defaultValue' => 'xxxx.com'
            ),
            'validation' => array(
                'required' => false
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
        // 显示在入口右侧的提示语
        $schemas['custom_url_sub_title'] = array(
            'name' => '显示在入口右侧的提示语',
            'data' => array(
                'type' => 'string',
                'length' => '18',
                'defaultValue' => '更多惊喜'
            ),
            'validation' => array(
                'required' => false
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
        // 营销场景的自定义入口名称
        $schemas['promotion_url_name'] = array(
            'name' => '营销场景的自定义入口名称',
            'data' => array(
                'type' => 'string',
                'length' => '15',
                'defaultValue' => '产品介绍'
            ),
            'validation' => array(
                'required' => false
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
        // 入口跳转外链的地址链接。例如xxxx.com
        $schemas['promotion_url'] = array(
            'name' => '入口跳转外链的地址链接',
            'data' => array(
                'type' => 'string',
                'length' => '128',
                'defaultValue' => 'xxxx.com'
            ),
            'validation' => array(
                'required' => false
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
        
        // 显示在营销入口右侧的提示语。例如更多惊喜
        $schemas['promotion_url_sub_title'] = array(
            'name' => '显示在营销入口右侧的提示语',
            'data' => array(
                'type' => 'string',
                'length' => '18',
                'defaultValue' => '卖场大优惠。'
            ),
            'validation' => array(
                'required' => false
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
        
        // 每人可领券的数量限制,不填写默认为50
        $schemas['get_limit'] = array(
            'name' => '每人最大领取次数(不填写默认等于上架数量)',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 1
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
        
        // 卡券领取页面是否可分享
        $schemas['can_share'] = array(
            'name' => '卡券领取页面是否可分享',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => true
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
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 卡券是否可转赠。
        $schemas['can_give_friend'] = array(
            'name' => '卡券是否可转赠',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => true
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
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['use_limit'] = array(
            'name' => '每人使用次数限制',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 1
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
        
        // 微信摇一摇
        // 获取自定义code的方式
        $schemas['get_custom_code_mode'] = array(
            'name' => '微信摇一摇，获取自定义code的方式',
            'data' => array(
                'type' => 'string',
                'length' => '30',
                'defaultValue' => $use_custom_code ? 'GET_CUSTOM_CODE_MODE_DEPOSIT' : ''
            ),
            'validation' => array(
                'required' => false
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
        // 新年祝语标题
        $schemas['shake_slogan_title'] = array(
            'name' => '微信摇一摇，新年祝语标题',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['shake_slogan_sub_title'] = array(
            'name' => '微信摇一摇，新年祝语正文',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['can_shake'] = array(
            'name' => '微信摇一摇，参加摇礼券活动的标志位',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
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
                'is_show' => false,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 优惠券专用，优惠详情。例如音乐木盒。
        $schemas['general_coupon_default_detail'] = array(
            'name' => '通用券专用，描述文本',
            'data' => array(
                'type' => 'string',
                'length' => '3072',
                'defaultValue' => '音乐木盒。'
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
        // 团购券专用，团购详情。例如双人套餐\n -进口红酒一支。\n孜然牛肉一份
        $schemas['groupon_deal_detail'] = array(
            'name' => '团购券专用，团购详情',
            'data' => array(
                'type' => 'string',
                'length' => '3072',
                'defaultValue' => '双人套餐。'
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
        // 折扣券专用，表示打折额度（百分比）。填30就是七折。
        $schemas['discount_discount'] = array(
            'name' => '折扣券专用，打折额度（百分比）填30就是七折',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 30
            ),
            'validation' => array(
                'required' => false
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
        // 兑换券专用，兑换内容。例如可兑换音乐木盒一个。
        $schemas['gift_gift'] = array(
            'name' => '兑换券专用，兑换内容',
            'data' => array(
                'type' => 'string',
                'length' => '3072',
                'defaultValue' => '可兑换音乐木盒一个。'
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
        
        // 代金券专用，表示起用金额（单位为分）。,如果无起用门槛则填0。
        $schemas['cash_least_cost'] = array(
            'name' => '代金券专用，起用金额(单位为分)',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
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
        // 代金券专用，表示减免金额（单位为分）。,如果无起用门槛则填0。
        $schemas['cash_reduce_cost'] = array(
            'name' => '代金券专用，减免金额(单位为分)',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => false
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
        
        // 景区门票专用，票类型 例如平日全票，套票等。
        $schemas['scenic_ticket_ticket_class'] = array(
            'name' => '景区门票专用，票类型',
            'data' => array(
                'type' => 'string',
                'length' => '3072',
                'defaultValue' => '平日全票'
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
        
        // 景区门票专用，导览图url 例如xxx.com
        $schemas['scenic_ticket_guide_url'] = array(
            'name' => '景区门票专用，导览图url',
            'data' => array(
                'type' => 'string',
                'length' => '128',
                'defaultValue' => 'xxxx.com'
            ),
            'validation' => array(
                'required' => false
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
        
        // 电影票专用，电影票详情 例如电影名：xxx，电影简介：xxx。
        $schemas['movie_ticket_detail'] = array(
            'name' => '电影票专用，电影票详情',
            'data' => array(
                'type' => 'string',
                'length' => '3072',
                'defaultValue' => '电影名：xxx，电影简介：xxx'
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
        
        // 会议门票专用，会议详情 例如本次会议于2015年5月10号在广州举行，会场地点：xxxx。
        $schemas['meeting_ticket_meeting_detail'] = array(
            'name' => '会议门票专用，会议详情',
            'data' => array(
                'type' => 'string',
                'length' => '3072',
                'defaultValue' => '本次会议于2015年5月10号在广州举行，会场地点：xxxx。'
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
        
        // 会议门票专用，会场导览图 例如xxx.com
        $schemas['meeting_ticket_map_url'] = array(
            'name' => '会议门票专用，会场导览图',
            'data' => array(
                'type' => 'string',
                'length' => '128',
                'defaultValue' => 'xxxx.com'
            ),
            'validation' => array(
                'required' => false
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
        
        // 飞机票类型专用，起点，上限为18个汉字。例如成都
        $schemas['boarding_pass_from'] = array(
            'name' => '飞机票专用，起点',
            'data' => array(
                'type' => 'string',
                'length' => '54',
                'defaultValue' => '成都'
            ),
            'validation' => array(
                'required' => false
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
        // 飞机票专用，终点，上限为18个汉字。例如广州
        $schemas['boarding_pass_to'] = array(
            'name' => '飞机票专用，终点',
            'data' => array(
                'type' => 'string',
                'length' => '54',
                'defaultValue' => '广州'
            ),
            'validation' => array(
                'required' => false
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
        // 飞机票专用，航班 例如CE123
        $schemas['boarding_pass_flight'] = array(
            'name' => '飞机票专用，航班',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => 'CE123'
            ),
            'validation' => array(
                'required' => false
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
        // 飞机票专用，入口，上限为4个汉字。例如A11
        $schemas['boarding_pass_gate'] = array(
            'name' => '飞机票专用，入口',
            'data' => array(
                'type' => 'string',
                'length' => '12',
                'defaultValue' => 'A11'
            ),
            'validation' => array(
                'required' => false
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
        // 飞机票专用，在线值机的链接 例如xxx.com
        $schemas['boarding_pass_check_in_url'] = array(
            'name' => '飞机票专用，在线值机的链接',
            'data' => array(
                'type' => 'string',
                'length' => '128',
                'defaultValue' => 'xxxx.com'
            ),
            'validation' => array(
                'required' => false
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
        // 飞机票类型专用，机型，上限为8个汉字。例如空客A320
        $schemas['boarding_pass_air_model'] = array(
            'name' => '飞机票专用，机型',
            'data' => array(
                'type' => 'string',
                'length' => '24',
                'defaultValue' => '空客A320'
            ),
            'validation' => array(
                'required' => false
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
        // 飞机票类型专用，起飞时间。Unix时间戳格式。
        $schemas['boarding_pass_departure_time'] = array(
            'name' => '飞机票专用，起飞时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 飞机票类型专用，降落时间。Unix时间戳格式。
        $schemas['boarding_pass_landing_time'] = array(
            'name' => '飞机票专用，降落时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 是否支持积分，填写 true 或 false，如填写 true，积分相关字段均为必填。填写 false，积分字段无需填写。储值字段处理方式相同。
        $schemas['member_card_supply_bonus'] = array(
            'name' => '会员卡专用，是否支持积分',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
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
                'is_show' => false,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 是否支持储值，填写 true 或false。 （该权限申请及说明详见Q&A 文档)
        $schemas['member_card_supply_balance'] = array(
            'name' => '会员卡专用，是否支持积分',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
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
                'is_show' => false,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 自定义会员信息类目1名称
        $schemas['member_card_custom_field1_name_type'] = array(
            'name' => '会员卡专用，自定义会员信息类目1名称',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        // 自定义会员信息类目1自定义跳转url
        $schemas['member_card_custom_field1_url'] = array(
            'name' => '会员卡专用，自定义会员信息类目1自定义跳转url',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 自定义会员信息类目2名称
        $schemas['member_card_custom_field2_name_type'] = array(
            'name' => '会员卡专用，自定义会员信息类目2名称',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        // 自定义会员信息类目2自定义跳转url
        $schemas['member_card_custom_field2_url'] = array(
            'name' => '会员卡专用，自定义会员信息类目2自定义跳转url',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 自定义会员信息类目3名称
        $schemas['member_card_custom_field3_name_type'] = array(
            'name' => '会员卡专用，自定义会员信息类目3名称',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        // 自定义会员信息类目3自定义跳转url
        $schemas['member_card_custom_field3_url'] = array(
            'name' => '会员卡专用，自定义会员信息类目3自定义跳转url',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['member_card_bonus_cleared'] = array(
            'name' => '会员卡专用，积分清零规则',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['member_card_bonus_rules'] = array(
            'name' => '会员卡专用，积分规则',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['member_card_balance_rules'] = array(
            'name' => '会员卡专用，储值说明',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['member_card_prerogative'] = array(
            'name' => '会员卡专用，特权说明',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        // 绑定旧卡的 url，与“ activate_url”字段二选一必填，用户领取会员卡后显示“ 绑定会员卡” 。
        $schemas['member_card_bind_old_card_url'] = array(
            'name' => '会员卡专用，绑定旧卡的 url',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 激活会员卡的 url，与“ bind_old_card_url”字段二选一必填。 用户领取会员卡后显示“激活会员卡”。
        $schemas['member_card_activate_url'] = array(
            'name' => '会员卡专用，激活会员卡的 url',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        // true 为用户点击进入会员卡时是否推送事件。
        $schemas['member_card_need_push_on_view'] = array(
            'name' => '会员卡专用，进入会员卡时是否推送事件',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => true
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
                'is_show' => false,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 会员卡类型专属营销入口1入口名称
        $schemas['member_card_custom_cell1_name'] = array(
            'name' => '会员卡专用，会员卡类型专属营销入口1入口名称',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        // 会员卡类型专属营销入口1入口右侧提示语，6个汉字内
        $schemas['member_card_custom_cell1_tips'] = array(
            'name' => '会员卡专用，会员卡类型专属营销入口1入口右侧提示语，6个汉字内',
            'data' => array(
                'type' => 'string',
                'length' => '20'
            ),
            'validation' => array(
                'required' => false
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
        
        // 会员卡类型专属营销入口1入口跳转链接
        $schemas['member_card_custom_cell1_url'] = array(
            'name' => '会员卡专用，会员卡类型专属营销入口1入口跳转链接',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        // 会员卡类型专属营销入口2入口名称
        $schemas['member_card_custom_cell2_name'] = array(
            'name' => '会员卡专用，会员卡类型专属营销入口2入口名称',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        // 会员卡类型专属营销入口2入口右侧提示语，6个汉字内
        $schemas['member_card_custom_cell2_tips'] = array(
            'name' => '会员卡专用，会员卡类型专属营销入口2入口右侧提示语，6个汉字内',
            'data' => array(
                'type' => 'string',
                'length' => '20'
            ),
            'validation' => array(
                'required' => false
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
        
        // 会员卡类型专属营销入口2入口跳转链接
        $schemas['member_card_custom_cell2_url'] = array(
            'name' => '会员卡专用，会员卡类型专属营销入口2入口跳转链接',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => '1000'
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
            ),
            'export' => array(
                'is_show' => false
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

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $cardTypeList = $this->modelCardType->getAll();
        $codeTypeList = $this->modelCodeType->getAll();
        $colorList = $this->modelColor->getAll();
        $dateInfoTypeList = $this->modelDateInfoType->getAll();
        foreach ($list['data'] as &$item) {
            $item['card_type_name'] = isset($cardTypeList[$item['card_type']]) ? $cardTypeList[$item['card_type']] : '';
            $item['code_type_name'] = isset($codeTypeList[$item['code_type']]) ? $codeTypeList[$item['code_type']] : '';
            $item['color_name'] = isset($colorList[$item['color']]) ? $colorList[$item['color']] : '';
            $item['date_info_type'] = isset($dateInfoTypeList[$item['date_info_type']]) ? $dateInfoTypeList[$item['date_info_type']] : '';
            if (empty($item['card_id'])) {
                $item['card_id'] = $item['card_id'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要将本地卡券信息上传，在微信公众平台上生成卡券吗？\', \'createcard\')" class="halflings-icon user white"><i></i> 创建</a>';
            } else {
                $item['card_id'] = $item['card_id'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要从微信公众平台上拉取最新的卡券信息更新到本地吗？\', \'updatecardinfo\')" class="halflings-icon user white"><i></i> 拉取</a>';
            }
            // $item['article_time'] = date("Y-m-d H:i:s", $item['article_time']->sec);
        }
        return $list;
    }

    /**
     * 在微信公众平台上创建卡券的Hook
     */
    public function createcardAction()
    {
        // http://www.applicationmodule.com:10080/admin/weixincard/card/createcard?id=xxxx
        try {
            $this->view->disable();
            
            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);
            
            $card_id = $this->get('id', '');
            if (empty($card_id)) {
                $cards = $this->modelCard->getAll();
            } else {
                $cardInfo = $this->modelCard->getInfoById($card_id);
                $cards = array(
                    $cardInfo
                );
            }
            
            $colors = $this->modelColor->getAll();
            if (! empty($cards)) {
                foreach ($cards as $card) {
                    if (! empty($card['card_id'])) { // 如果已生成的话
                        continue;
                    }
                    $this->modelCard->create($card, $colors);
                }
            }
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * 从微信公众平台上获取最新的卡券信息
     */
    public function getcardinfoAction()
    {
        // http://www.applicationmodule.com:10080/admin/weixincard/card/getcardinfo?card_id=pgW8rt5vzjJ7nFLYxskYGBtxZP3k
        try {
            $this->view->disable();
            
            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);
            
            $card_id = $this->get('card_id', '');
            
            $ret = $weixin->getCardManager()->get($card_id);
            $this->makeJsonResult($ret);
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * 从微信公众平台上获取最新的卡券信息更新本地的Hook
     */
    public function updatecardinfoAction()
    {
        // http://www.applicationmodule.com:10080/admin/weixincard/card/updatecardinfo?id=pgW8rt5vzjJ7nFLYxskYGBtxZP3k
        try {
            $this->view->disable();
            
            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);
            
            $card_id = $this->get('id', '');
            
            if (empty($card_id)) { // 如果没有指定卡券ID,那么根据卡券类别获取列表
                $cards = $this->modelCard->getAll();
            } else {
                $cardInfo = $this->modelCard->getInfoById($card_id);
                $cards = array(
                    $cardInfo
                );
            }
            if (! empty($cards)) {
                foreach ($cards as $item) {
                    if (empty($item["card_id"])) {
                        continue;
                    }
                    // 获取最新的卡券信息并且更新本地的信息
                    $this->modelCard->getAndUpdateCardInfo($item["card_id"]);
                }
            }
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * 修改库存接口
     *
     * @throws \Exception
     */
    public function modifystockAction()
    {
        // http://www.applicationmodule.com:10080/admin/weixincard/card/modifystock?card_id=p4ELSv5zS98NBYuq8D1l2HcgRou0&increase_stock_value=10&reduce_stock_value=0
        try {
            $this->view->disable();
            $weixin = $this->getWeixin();
            $this->modelCard->setWeixin($weixin);
            
            $card_id = $this->get('card_id', '');
            $increase_stock_value = intval($this->get('increase_stock_value', '0'));
            $reduce_stock_value = intval($this->get('reduce_stock_value', '0'));
            if (empty($card_id)) {
                throw new \Exception("card_id未指定", - 1);
            }
            if ($increase_stock_value < 0) {
                throw new \Exception("increase_stock_value未指定", - 2);
            }
            if ($reduce_stock_value < 0) {
                throw new \Exception("reduce_stock_value未指定", - 2);
            }
            $cardManager = $weixin->getCardManager();
            $rst = $cardManager->modifyStock($card_id, $increase_stock_value, $reduce_stock_value);
            
            if (! empty($rst['errcode'])) {
                // 如果有异常，会在errcode 和errmsg 描述出来。
                throw new \Exception($rst['errmsg'], $rst['errcode']);
            }
            
            $ret = $weixin->getCardManager()->get($card_id);
            
            $this->makeJsonResult(json_encode($ret));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}