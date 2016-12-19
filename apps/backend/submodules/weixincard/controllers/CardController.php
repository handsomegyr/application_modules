<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CardType;
use App\Backend\Submodules\Weixincard\Models\CodeType;
use App\Backend\Submodules\Weixincard\Models\Color;
use App\Backend\Submodules\Weixincard\Models\Card;
use App\Backend\Submodules\Weixincard\Models\DateInfoType;

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

    public function initialize()
    {
        $this->modelCard = new Card();
        $this->modelCardType = new CardType();
        $this->modelCodeType = new CodeType();
        $this->modelColor = new Color();
        $this->modelDateInfoType = new DateInfoType();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['card_id'] = array(
            'name' => '微信卡券ID',
            'data' => array(
                'type' => 'string',
                'length' => '50'
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
                'length' => '30',
                'defaultValue' => 'GENERAL_COUPON'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
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
        
        $schemas['code_type'] = array(
            'name' => 'code码展示类型',
            'data' => array(
                'type' => 'string',
                'length' => '30',
                'defaultValue' => 'CODE_TYPE_TEXT'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
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
        
        // 商户名字,字数上限为12个汉字
        $schemas['brand_name'] = array(
            'name' => '商户名字(字数上限为12个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '50',
                'defaultValue' => '来伊份'
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
                'length' => '50',
                'defaultValue' => '全场限时券'
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
        
        // 券名的副标题，字数上限为18个汉字
        $schemas['sub_title'] = array(
            'name' => '副标题(字数上限为18个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '50',
                'defaultValue' => '满88立减8元'
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
        // 券颜色。按色彩规范标注填写Color010-Color100
        $schemas['color'] = array(
            'name' => '券颜色',
            'data' => array(
                'type' => 'string',
                'length' => '30',
                'defaultValue' => '#63B359'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
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
        // 使用提醒，字数上限为9个汉字。（一句话描述，展示在首页，示例：请出示二维码核销卡券）
        $schemas['notice'] = array(
            'name' => '使用提醒(字数上限为9个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '20',
                'defaultValue' => '前9位卡号后4位密码'
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
        
        // 客服电话
        $schemas['service_phone'] = array(
            'name' => '客服电话',
            'data' => array(
                'type' => 'string',
                'length' => '10',
                'defaultValue' => '4008819777'
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
        
        // 第三方来源名，例如同程旅游、 格瓦拉
        $schemas['source'] = array(
            'name' => '第三方来源名(例如同程旅游、 格瓦拉)',
            'data' => array(
                'type' => 'string',
                'length' => '50',
                'defaultValue' => '来伊份'
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
        
        // 使用说明。长文本描述，可以分行，上限为1000个汉字。
        $schemas['description'] = array(
            'name' => '使用说明(字数上限为1000个汉字)',
            'data' => array(
                'type' => 'string',
                'length' => '1000',
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
        
        // 上架的数量。(不支持填写0或无限大)
        $schemas['sku_quantity'] = array(
            'name' => '上架数量(不支持填写0或无限大)',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 10
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
        
        // 每人最大领取次数，不填写默认等于quantity
        $schemas['get_limit'] = array(
            'name' => '每人最大领取次数(不填写默认等于上架数量)',
            'data' => array(
                'type' => 'integer',
                'length' => 10,
                'defaultValue' => 10
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
        
        // 是否自定义code 码。填写true或false，不填代表默认为 false
        $schemas['use_custom_code'] = array(
            'name' => '是否自定义code码',
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
        
        // 是否指定用户领取，填写true或false。不填代表默认为否。
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
        
        // 领取卡券原生页面是否可分享，填写true或false，true代表可分享。默认可分享。
        $schemas['can_share'] = array(
            'name' => '领取卡券原生页面是否可分享',
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
        // 卡券是否可转赠，填写true或false,true代表可转赠。默认可转赠。
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
        
        $schemas['location_id_list'] = array(
            'name' => '门店位置ID',
            'data' => array(
                'type' => 'string',
                'length' => '1000'
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
        
        $schemas['date_info_type'] = array(
            'name' => '使用时间类型',
            'data' => array(
                'type' => 'integer',
                'length' => '1',
                'defaultValue' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
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
        
        // 固定日期区间专用，表示起用时间。从1970 年1 月1 日 00:00:00 至起用时间的秒数， 最终需转换为字符串形态传入， 下同。（单位为秒）
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
        // 固定日期区间专用，表示结束时间。（单位为秒）
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
        // 固定时长专用，表示自领取后多少天内有效。（单位为天）
        $schemas['date_info_fixed_term'] = array(
            'name' => '领取后多少天内有效(单位为天)',
            'data' => array(
                'type' => 'integer',
                'length' => 10
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
        
        // 固定时长专用，表示自领取后多少天开始生效。（单位为天）
        $schemas['date_info_fixed_begin_term'] = array(
            'name' => '领取后多少天开始生效(单位为天)',
            'data' => array(
                'type' => 'integer',
                'length' => 10
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
        
        // 商户自定义入口名称，与custom_url 字段共同使用，长度限制在 5 个汉字内。
        $schemas['custom_url_name'] = array(
            'name' => '商户自定义入口名称(限制在5个汉字内)',
            'data' => array(
                'type' => 'string',
                'length' => '10'
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
        
        // 商户自定义入口跳转外链的地址链接,跳转页面内容需与自定义cell 名称保持匹配。
        $schemas['custom_url'] = array(
            'name' => '商户自定义入口跳转外链的地址链接',
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
        // 显示在入口右侧的 tips，长度限制在 6 个汉字内。
        $schemas['custom_url_sub_title'] = array(
            'name' => '显示在入口右侧的tips(限制在 6个汉字内)',
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
        // 微信摇一摇
        // 获取自定义code的方式
        $schemas['get_custom_code_mode'] = array(
            'name' => '微信摇一摇，获取自定义code的方式',
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
        
        $schemas['promotion_url_name'] = array(
            'name' => '微信摇一摇，营销场景的自定义入口',
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
        
        $schemas['promotion_url'] = array(
            'name' => '微信摇一摇，入口跳转外链的地址链接',
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
        
        // 显示在入口右侧的 tips，长度限制在 6 个汉字内。
        $schemas['promotion_url_sub_title'] = array(
            'name' => '微信摇一摇，显示在入口右侧的tips(限制在 6个汉字内)',
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
        
        $schemas['default_detail'] = array(
            'name' => '通用券专用，描述文本',
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
        
        $schemas['deal_detail'] = array(
            'name' => '团购券专用，团购详情',
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
        // 折扣券专用，表示打折额度（百分比）。填30就是七折。
        $schemas['discount'] = array(
            'name' => '折扣券专用，打折额度（百分比）填30就是七折',
            'data' => array(
                'type' => 'integer',
                'length' => 10
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
        
        $schemas['gift'] = array(
            'name' => '礼品券专用，礼品名字',
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
        
        // 代金券专用，表示起用金额（单位为分）。
        $schemas['least_cost'] = array(
            'name' => '代金券专用，起用金额(单位为分)',
            'data' => array(
                'type' => 'integer',
                'length' => 10
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
        // 代金券专用，表示减免金额（单位为分）。
        $schemas['reduce_cost'] = array(
            'name' => '代金券专用，减免金额(单位为分)',
            'data' => array(
                'type' => 'integer',
                'length' => 10
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
        
        // 是否支持积分，填写 true 或 false，如填写 true，积分相关字段均为必填。填写 false，积分字段无需填写。储值字段处理方式相同。
        $schemas['supply_bonus'] = array(
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
        $schemas['supply_balance'] = array(
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
        // 自定义会员信息类目，会员卡激活后显示。 否
        $schemas['custom_field1'] = array(
            'name' => '会员卡专用，自定义会员信息类目1',
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
        
        $schemas['custom_field2'] = array(
            'name' => '会员卡专用，自定义会员信息类目2',
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
        
        $schemas['custom_field3'] = array(
            'name' => '会员卡专用，自定义会员信息类目3',
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
        
        $schemas['bonus_cleared'] = array(
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
        
        $schemas['bonus_rules'] = array(
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
        
        $schemas['balance_rules'] = array(
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
        
        $schemas['prerogative'] = array(
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
        $schemas['bind_old_card_url'] = array(
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
        $schemas['activate_url'] = array(
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
        $schemas['need_push_on_view'] = array(
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
        
        // 会员卡类型专属营销入口，会员卡激活前后均显示。否
        $schemas['custom_cell1'] = array(
            'name' => '会员卡专用，会员卡类型专属营销入口1',
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
        
        $schemas['custom_cell2'] = array(
            'name' => '会员卡专用，会员卡类型专属营销入口2',
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
            
            // $item['article_time'] = date("Y-m-d H:i:s", $item['article_time']->sec);
        }
        return $list;
    }
}