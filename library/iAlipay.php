<?php
require_once APP_PATH . 'library/alipay-sdk-PHP-ALL20170926113544/AopSdk.php';

class iAlipay
{

    private $config = array(
        // 应用ID,您的APPID。
        'app_id' => "",
        
        // 商户私钥，您的原始格式私钥,一行字符串
        'merchant_private_key' => "",
        
        // 商户应用公钥,一行字符串
        'merchant_public_key' => "",
        
        // 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "",
        
        // 编码格式只支持GBK。
        'charset' => "GBK",
        
        // 支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        
        // 签名方式
        'sign_type' => "RSA2"
    );

    public function __construct($app_id, $merchant_private_key, $merchant_public_key, $alipay_public_key, $charset = "UTF-8", $gatewayUrl = "https://openapi.alipay.com/gateway.do", $sign_type = 'RSA2')
    {
        $this->config['app_id'] = $app_id;
        $this->config['merchant_private_key'] = $merchant_private_key;
        $this->config['merchant_public_key'] = $merchant_public_key;
        $this->config['alipay_public_key'] = $alipay_public_key;
        $this->config['charset'] = $charset;
        $this->config['gatewayUrl'] = $gatewayUrl;
        $this->config['sign_type'] = $sign_type;
    }

    /**
     * 第一步：URL拼接与scope详解
     *
     * url拼接规则：https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=APPID&scope=SCOPE&redirect_uri=ENCODED_URL
     *
     * 使用场景举例：开发者通过URL拼接方案，构造授权页面，并且引导用户授权。
     *
     * url参数说明
     *
     * 参数名 是否必须 描述
     * app_id
     *
     * 是
     *
     * 开发者应用的app_id
     *
     * scope
     *
     * 是
     *
     * 接口权限值，目前只支持auth_user和auth_base两个值
     *
     * redirect_uri
     *
     * 是
     *
     * 回调页面，是 经过转义 的url链接（url必须以http或者https开头），比如：http%3A%2F%2Fexample.com
     *
     * 在请求之前，开发者需要先到开发者中心对应应用内，配置授权回调地址。
     *
     * state
     *
     * 否
     *
     * 商户自定义参数，用户授权后，重定向到redirect_uri时会原样回传给商户。 为防止CSRF攻击，建议开发者请求授权时传入state参数，该参数要做到既不可预测，又可以证明客户端和当前第三方网站的登录认证状态存在关联。
     *
     * 关于redirect_uri的说明：
     * 接口会校验授权链接中配置的redirect_uri与应用中配置的授权链接是否一致。详细说明： 如果开发者在应用中配置的授权链接是：https://auth.example.com/authCallBack，则redirect-uri内容为https://auth.example.com/authCallBack的encode形式https%3A%2F%2Fauth.example.com%2FauthCallBack。授权回调地址对应的域名（auth.example.com）下的页面http://auth.example.com/authCallBack、https://auth.example.com/authRedirect、https://auth.example.com/都可以进行OAuth2.0授权。但与（auth.example.com）关联的二三级域名，如：http://www.example.com/、http://example.com/无法进行OAuth2.0授权。
     *
     * 关于scope的说明：
     *
     * auth_base：以auth_base为scope发起的网页授权，是用来获取进入页面的用户的userId的，并且是静默授权并自动跳转到回调页的。用户感知的就是直接进入了回调页（通常是业务页面）。
     * auth_user：以auth_user为scope发起的网页授权，是用来获取用户的基本信息的（比如头像、昵称等）。但这种授权需要用户手动同意，用户同意后，就可在授权后获取到该用户的基本信息。
     */
    public static function getAuthorizeUrl($redirectUri, $appid, $scope = 'auth_user', $state = '')
    {
        if (empty($state)) {
            $state = uniqid();
        }
        $redirectUri = urlencode($redirectUri);
        return "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id={$appid}&scope={$scope}&redirect_uri={$redirectUri}&state={$state}";
    }

    /**
     * 第三步：应用授权URL拼装
     *
     * 拼接规则:
     *
     * https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=2015101400446982&redirect_uri=http%3A%2F%2Fexample.com
     *
     * TIPS：沙箱拼接规则详见 关于沙箱。
     * 参数说明：
     *
     * 参数 参数名称 类型 必填 描述 范例
     * app_id
     *
     * 开发者应用的AppId
     *
     * String
     *
     * 是
     *
     * 开发者应用的AppId
     *
     * 2015101400446982
     *
     * redirect_uri
     *
     * 回调页面
     *
     * String
     *
     * 是
     *
     * 参数需要UrlEncode
     *
     * http%3A%2F%2Fexample.com
     *
     * TIPS：授权链接中配置的redirect_uri内容需要与应用中配置的授权回调地址完全一样，否则无法正常授权。
     * 使用场景举例：
     *
     * 商户使用开发者提供的应用授权链接在PC端进行应用授权
     */
    public static function getAuthorizeUrl4Appauth($redirectUri, $appid)
    {
        $redirectUri = urlencode($redirectUri);
        return "https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id={$appid}&redirect_uri={$redirectUri}";
    }

    /**
     * 第四步：调用接口获取用户信息
     *
     * 如果scope=auth_base，在第三步就可以获取到用户的userId，无需走第四步。如果scope=auth_user，才需要走第四步，通过access_token调用用户信息共享接口获取用户信息。
     *
     * 接口名称：alipay.user.info.share
     *
     * 使用场景举例：开发者可以使用该方案获取用户的userId、头像、昵称等基础信息。
     *
     * 接口请求示例
     *
     * REQUEST URL: https://openapi.alipay.com/gateway.do
     * REQUEST METHOD: POST
     * CONTENT:
     * app_id=2014070100171525
     * method=alipay.user.info.share
     * charset=GBK
     * sign_type=RSA2
     * timestamp=2014-01-01 08:08:08
     * sign=rXaTEfJ7WTDsP1DWRPHARW3uOr19+fzlngMCJBvbhP1XPEa9qZwGGng9oMDloABpJMT2SGeOj46+BUkqCGRO9fH90Vci3hOH01BfYnbhJz3ADK2h7gpjlponx4/sxELN6f2GXi51XKiHKnxMA9XpLLo68q+roY0M/ZFQ1UdnqeM=
     * version=1.0
     * grant_type=authorization_code
     * code=4b203fe6c11548bcabd8da5bb087a83b
     * auth_token=201208134b203fe6c11548bcabd8da5bb087a83b
     * 公共请求参数说明
     *
     * 参数 参数名称 类型（长度范围） 参数说明 是否可为空 样例
     * auth_token
     *
     * 授权令牌
     *
     * String
     *
     * 通过auth_code获取的access_token
     *
     * 不可空
     *
     * publicpB9ea460ff5b5c468c9ccf5e967dc34963
     *
     * 同步响应结果示例
     *
     * {
     * "alipay_user_info_share_response": {
     * "avatar": "https:\/\/tfsimg.alipay.com\/images\/partner\/T1k0xiXXRnXXXXXXXX",
     * "nick_name": "张三",
     * "city": "杭州",
     * "province": "浙江省",
     * "gender" : "M",
     * "user_type_value": "2",
     * "is_licence_auth": "F",
     * "is_certified": "T",
     * "is_certify_grade_a": "T",
     * "is_student_certified": "F",
     * "is_bank_auth": "T",
     * "is_mobile_auth": "T",
     * "alipay_user_id": "2088102015433735",
     * "user_id": "20881007434917916336963360919773",
     * "user_status": "T",
     * "is_id_auth": "T"
     * },
     * "sign": "jhoSkfE7BTIbwEx0L8/H0GU0Z2DOZYIJlrUMyJL8wwwInVeXfz+CWqx0V2b3FvhMQSrb74dkzDQpGXGdZQZMldGe4+FSEQU1V3tWijpO9ZisNJnEpF+U2lQ7IUMLsgjjx9a0IdMwvXlqz1HPrmFZQjG2dvlFyXhi07HcEnVOJZw="
     * }
     * 同步响应参数说明
     *
     * 参数 参数名称 类型（长度范围） 参数说明 是否可为空 样例
     * avatar
     *
     * 用户头像
     *
     * String
     *
     * 如果没有数据的时候不会返回该数据，请做好容错
     *
     * 可空
     *
     * https://tfsimg.alipay.com/images/partner/T1k0xiXXRnXXXXXXXX
     *
     * nick_name
     *
     * 用户昵称
     *
     * String
     *
     * 如果没有数据的时候不会返回该数据，请做好容错
     *
     * 可空
     *
     * 张三
     *
     * province
     *
     * 省份
     *
     * String
     *
     * 用户注册时填写的省份 如果没有数据的时候不会返回该数据，请做好容错
     *
     * 可空
     *
     * 浙江省
     *
     * city
     *
     * 城市
     *
     * String
     *
     * 用户注册时填写的城市， 如果没有数据的时候不会返回该数据，请做好容错
     *
     * 可空
     *
     * 杭州
     *
     * gender
     *
     * 用户性别
     *
     * String
     *
     * M为男性，F为女性， 如果没有数据的时候不会返回该数据，请做好容错
     *
     * 可空
     *
     * M
     *
     * alipay_user_id
     *
     * 当前用户的userId
     *
     * String
     *
     * 支付宝用户的userId
     *
     * 不可空
     *
     * 2088411964574197
     *
     * user_type
     *
     * 用户类型
     *
     * String
     *
     * 1代表公司账户2代表个人账户
     *
     * 可空
     *
     * 1
     *
     * user_status 用户状态 String Q代表快速注册用户 T代表已认证用户 B代表被冻结账户 W代表已注册，未激活的账户 可空 T
     * is_certified 是否通过实名认证 T是通过 F是没有实名认证 可空 T
     * is_student_certified 是否是学生 T是学生 F不是学生 可空 T
     */
    public function alipayUserInfoRequest($access_token, $type = 'info')
    {
        if ($type == 'info') {
            // alipay.user.info.share(支付宝会员授权信息查询接口)
            $request = new AlipayUserInfoShareRequest();
        } else {
            // alipay.user.userinfo.share(支付宝钱包用户信息共享)
            $request = new AlipayUserUserinfoShareRequest();
            // $request->setProdCode ( $token );
        }
        $result = $this->aopclient_request_execute($request, $access_token);
        
        return $result;
    }

    /**
     * 第三步：使用auth_code换取接口access_token及用户userId
     *
     * 接口名称：alipay.system.oauth.token
     *
     * 换取授权访问令牌，开发者可通过获取到的auth_code换取access_token和用户userId。auth_code作为换取access_token的票据，每次用户授权完成，回调地址中的auth_code将不一样，auth_code只能使用一次，一天未被使用自动过期。
     * 接口请求示例
     *
     *
     * REQUEST URL: https://openapi.alipay.com/gateway.do
     * REQUEST METHOD: POST
     * CONTENT:
     * app_id=2014070100171525
     * method=alipay.system.oauth.token
     * charset=GBK
     * sign_type=RSA2
     * timestamp=2014-01-01 08:08:08
     * sign=rXaTEfJ7WTDsP1DWRPHARW3uOr19+fzlngMCJBvbhP1XPEa9qZwGGng9oMDloABpJMT2SGeOj46+BUkqCGRO9fH90Vci3hOH01BfYnbhJz3ADK2h7gpjlponx4/sxELN6f2GXi51XKiHKnxMA9XpLLo68q+roY0M/ZFQ1UdnqeM=
     * version=1.0
     * grant_type=authorization_code
     * code=4b203fe6c11548bcabd8da5bb087a83b
     * refresh_token=201208134b203fe6c11548bcabd8da5bb087a83b
     * 请求参数说明
     *
     * 参数 参数名称 类型（长度范围） 参数说明 是否可为空 样例
     * grant_type
     *
     * 授权类型
     *
     * String
     *
     * 值为authorization_code时，代表用code换取；值为refresh_token时，代表用refresh_token换取
     *
     * 不可空
     *
     * authorization_code
     *
     * code
     *
     * 授权码
     *
     * String
     *
     * 用户对应用授权后得到，即第二步中开发者获取到的auth_code值
     *
     * 与refresh_token二选一
     *
     * 4b203fe6c11548bcabd8da5bb087a83b
     *
     * refresh_token
     *
     * 刷新令牌
     *
     * String
     *
     * 刷新access_token时使用
     *
     * 与code二选一
     *
     * 201208134b203fe6c11548bcabd8da5bb087a83b
     *
     * 同步响应结果示例
     *
     * {
     * "alipay_system_oauth_token_response": {
     * "access_token": "publicpBa869cad0990e4e17a57ecf7c5469a4b2",
     * "user_id": "2088411964574197",
     * "alipay_user_id": "20881007434917916336963360919773",
     * "expires_in": 300,
     * "re_expires_in": 300,
     * "refresh_token": "publicpB0ff17e364f0743c79b0b0d7f55e20bfc"
     * },
     * "sign": "xDffQVBBelDiY/FdJi4/a2iQV1I7TgKDFf/9BUCe6+l1UB55YDOdlCAir8CGlTfa0zLYdX0UaYAa43zY2jLhCTDG+d6EjhCBWsNY74yTdiM95kTNsREgAt4PkOkpsbyZVXdLIShxLFAqI49GIv82J3YtzBcVDDdDeqFcUhfasII="
     * }
     *
     * 同步响应参数说明
     *
     * 参数 参数名称 类型（长度范围） 参数说明 是否可为空 样例
     * access_token
     *
     * 交换令牌
     *
     * String
     *
     * 用于获取用户信息
     *
     * 不可空
     *
     * publicpBa869cad0990e4e17a57ecf7c5469a4b2
     *
     * user_id
     *
     * 用户的userId
     *
     * String
     *
     * 支付宝用户的唯一userId
     *
     * 不可空
     *
     * 2088411964574197
     *
     * alipay_user_id
     *
     * 用户的open_id（已废弃，请勿使用）
     *
     * String
     *
     * 已废弃，请勿使用
     *
     * 不可空
     *
     * 已废弃，请勿使用
     *
     * expires_in
     *
     * 令牌有效期
     *
     * Number
     *
     * 交换令牌的有效期，单位秒
     *
     * 不可空
     *
     * 300
     *
     * re_expires_in
     *
     * 刷新令牌有效期
     *
     * Number
     *
     * 刷新令牌有效期，单位秒
     *
     * 不可空
     *
     * 300
     *
     * refresh_token
     *
     * 刷新令牌
     *
     * String
     *
     * 通过该令牌可以刷新access_token
     *
     * 不可空
     *
     * publicpB0ff17e364f0743c79b0b0d7f55e20bfc
     */
    public function alipaySystemOauthTokenRequest($auth_code)
    {
        $request = new AlipaySystemOauthTokenRequest();
        $request->setCode($auth_code);
        $request->setGrantType("authorization_code");
        
        $result = $this->aopclient_request_execute($request);
        
        return $result;
    }

    /**
     * alipay.marketing.campaign.drawcamp.trigger(
     * 营销抽奖活动触发抽奖)
     * 版本号 1.1
     * 不需要授权
     * API应用场景说明
     * 营销抽奖活动触发抽奖
     * 公共参数
     * 请求地址
     * 公共请求参数
     * 请求参数
     * 环境 HTTPS请求地址
     * 正式环境 https://openapi.alipay.com/gateway.do
     * API应用场景说明 类型 是否
     * 必填
     * 最大
     * 长度
     * 描述 示例值
     * app_id String 是 32 支付宝分配给开发者的应用ID 2014072300007148
     * method String 是 128 接口名称 alipay.marketing.cam
     * paign.drawcamp.trigg
     * er
     * format String 否 40 仅支持JSON JSON
     * charset String 是 10 请求使用的编码格式，如utf-8,gbk,gb2312等 utf-8
     * sign_type String 是 10 商户生成签名字符串所使用的签名算法类型，目
     * 前支持RSA2和RSA，推荐使用RSA2
     * RSA2
     * sign String 是 256 商户请求参数的签名串，详见<a
     * href="https://doc.open.alipay.com/docs/doc.
     * htm?treeId=291&articleId=105974&docType=
     * 1">签名</a>
     * 详见示例
     * timestamp String 是 19 发送请求的时间，格式"yyyy-MM-dd
     * HH:mm:ss"
     * 2014-07-24 03:07:50
     * version String 是 3 调用的接口版本，固定为：1.0 1.0
     * app_auth_token String 否 40 详见<a
     * href="https://doc.open.alipay.com/doc2/deta
     * il.htm?treeId=216&articleId=105193&docType
     * =1">应用授权概述</a>
     * biz_content String 是 - 请求参数的集合，最大长度不限，除公共参数外
     * 所有请求参数都必须放在这个参数中传递，具体
     * 参照各产品快速接入文档
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * user_id String 是 200 用户登录号/用户uid，非脱敏
     * 账号
     * 2088102164186692
     * camp_id String 是 64 活动id 32173827832
     * bind_mobile String 否 15 用户参与活动的手机号（如果
     * 是用户直接输入手机号的活动
     * 形式，该项必填，作为识别用
     * 户的依据）
     * 13812341234
     * camp_source Number 否 5 请求来源，目前支持：1微信
     * 2 微博 3虾米 4淘宝 5天猫
     * 1公共响应参数
     * 响应参数
     * 请求示例
     * JAVA版本
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * code String 是 ~ 网关返回码，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * 40004
     * msg String 是 ~ 网关返回码描述，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * Business Failed
     * sub_code String 否 ~ 网关明细返回码，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * isv.invalid-signature
     * sub_msg String 否 ~ 网关明细返回码描述，详见文
     * 档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * 交易已被支付
     * sign String 是 64 签名，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1
     * DZXh8eeTuAHoYE3w1
     * J+POiPhfDxOYBfUNn1
     * lkeT/V7P4zJdyojWEa6
     * IZs6Hz0yDW5Cp/viufU
     * b5I0/V5WENS3OYR8zR
     * edqo6D+fUTdLHdc+E
     * FyCkiQhBxIzgngPdPdf
     * p1PIS7BdhhzrsZHbRq
     * b7o4k3Dxc+AAnFauu4
     * V6Zdwczo=
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * trigger_result Boolean 是 5 是否中奖结果状态，如果为
     * true时返回的结果中的其他字
     * 段非空，否则返回的其他字段
     * 为空
     * true
     * prize_id String 否 64 奖品id 4324324
     * prize_name String 否 200 奖品名称 立减
     * camp_log_id String 否 64 活动流水id 2016050500012387382
     * 3
     * camp_id String 否 64 活动id 20183984378342
     * prize_flag String 是 64 奖品唯一标识 74189237488923892
     * extend_field String 否 1000 拓展字段 {"key1":"value1","key
     * 2":"value2"}
     * display_name String 否 100 logonId的脱敏显示 138****1234
     */
    public function alipayMarketingCampaignDrawcampTriggerRequest($user_id, $camp_id, $bind_mobile = '', $camp_source = 0)
    {
        // alipay.marketing.campaign.drawcamp.trigger(营销抽奖活动触发抽奖)
        $request = new AlipayMarketingCampaignDrawcampTriggerRequest();
        $bizContent = array();
        $bizContent['user_id'] = $user_id;
        $bizContent['camp_id'] = $camp_id;
        if (! empty($bind_mobile)) {
            $bizContent['bind_mobile'] = $bind_mobile;
        }
        if (! empty($camp_source)) {
            $bizContent['camp_source'] = $camp_source;
        }
        $request->setBizContent(json_encode($bizContent));
        $result = $this->aopclient_request_execute($request);
        
        return $result;
    }

    /**
     * alipay.marketing.campaign.prize.amount.query(奖品剩余数量查询)
     * 版本号 1.0
     * 不需要授权
     * API应用场景说明
     * 奖品剩余数量查询
     * 公共参数
     * 请求地址
     * 公共请求参数
     * 请求参数
     * 公共响应参数
     * 环境 HTTPS请求地址
     * 正式环境 https://openapi.alipay.com/gateway.do
     * API应用场景说明 类型 是否
     * 必填
     * 最大
     * 长度
     * 描述 示例值
     * app_id String 是 32 支付宝分配给开发者的应用ID 2014072300007148
     * method String 是 128 接口名称 alipay.marketing.cam
     * paign.prize.amount.q
     * uery
     * format String 否 40 仅支持JSON JSON
     * charset String 是 10 请求使用的编码格式，如utf-8,gbk,gb2312等 utf-8
     * sign_type String 是 10 商户生成签名字符串所使用的签名算法类型，目
     * 前支持RSA2和RSA，推荐使用RSA2
     * RSA2
     * sign String 是 256 商户请求参数的签名串，详见<a
     * href="https://doc.open.alipay.com/docs/doc.
     * htm?treeId=291&articleId=105974&docType=
     * 1">签名</a>
     * 详见示例
     * timestamp String 是 19 发送请求的时间，格式"yyyy-MM-dd
     * HH:mm:ss"
     * 2014-07-24 03:07:50
     * version String 是 3 调用的接口版本，固定为：1.0 1.0
     * app_auth_token String 否 40 详见<a
     * href="https://doc.open.alipay.com/doc2/deta
     * il.htm?treeId=216&articleId=105193&docType
     * =1">应用授权概述</a>
     * biz_content String 是 - 请求参数的集合，最大长度不限，除公共参数外
     * 所有请求参数都必须放在这个参数中传递，具体
     * 参照各产品快速接入文档
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * camp_id String 是 64 活动id 1303312
     * prize_id String 是 64 奖品id 1134921
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值响应参数
     * 请求示例
     * JAVA版本
     * PHP版本
     * code String 是 ~ 网关返回码，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * 40004
     * msg String 是 ~ 网关返回码描述，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * Business Failed
     * sub_code String 否 ~ 网关明细返回码，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * isv.invalid-signature
     * sub_msg String 否 ~ 网关明细返回码描述，详见文
     * 档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * 交易已被支付
     * sign String 是 64 签名，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1
     * DZXh8eeTuAHoYE3w1
     * J+POiPhfDxOYBfUNn1
     * lkeT/V7P4zJdyojWEa6
     * IZs6Hz0yDW5Cp/viufU
     * b5I0/V5WENS3OYR8zR
     * edqo6D+fUTdLHdc+E
     * FyCkiQhBxIzgngPdPdf
     * p1PIS7BdhhzrsZHbRq
     * b7o4k3Dxc+AAnFauu4
     * V6Zdwczo=
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * remain_amount String 是 20 奖品剩余数量，数值 120
     */
    public function alipayMarketingCampaignPrizeAmountQueryRequest($camp_id, $prize_id)
    {
        // alipay.marketing.campaign.prize.amount.query(奖品剩余数量查询)
        $request = new AlipayMarketingCampaignPrizeAmountQueryRequest();
        $bizContent = array();
        $bizContent['camp_id'] = $camp_id;
        $bizContent['prize_id'] = $prize_id;
        $request->setBizContent(json_encode($bizContent));
        $result = $this->aopclient_request_execute($request);
        
        return $result;
    }

    /**
     * alipay.marketing.campaign.drawcamp.query(
     * 营销抽奖活动查询)
     * 版本号 1.1
     * 不需要授权
     * API应用场景说明
     * 通过此接口可以查询营销抽奖活动，开发者可以通过alipay.marketing.campaign.drawcamp.create创建营销抽奖活动
     * 公共参数
     * 请求地址
     * 公共请求参数
     * 请求参数
     * 公共响应参数
     * 环境 HTTPS请求地址
     * 正式环境 https://openapi.alipay.com/gateway.do
     * API应用场景说明 类型 是否
     * 必填
     * 最大
     * 长度
     * 描述 示例值
     * app_id String 是 32 支付宝分配给开发者的应用ID 2014072300007148
     * method String 是 128 接口名称 alipay.marketing.cam
     * paign.drawcamp.quer
     * y
     * format String 否 40 仅支持JSON JSON
     * charset String 是 10 请求使用的编码格式，如utf-8,gbk,gb2312等 utf-8
     * sign_type String 是 10 商户生成签名字符串所使用的签名算法类型，目
     * 前支持RSA2和RSA，推荐使用RSA2
     * RSA2
     * sign String 是 256 商户请求参数的签名串，详见<a
     * href="https://doc.open.alipay.com/docs/doc.
     * htm?treeId=291&articleId=105974&docType=
     * 1">签名</a>
     * 详见示例
     * timestamp String 是 19 发送请求的时间，格式"yyyy-MM-dd
     * HH:mm:ss"
     * 2014-07-24 03:07:50
     * version String 是 3 调用的接口版本，固定为：1.0 1.0
     * app_auth_token String 否 40 详见<a
     * href="https://doc.open.alipay.com/doc2/deta
     * il.htm?treeId=216&articleId=105193&docType
     * =1">应用授权概述</a>
     * biz_content String 是 - 请求参数的集合，最大长度不限，除公共参数外
     * 所有请求参数都必须放在这个参数中传递，具体
     * 参照各产品快速接入文档
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * camp_id String 是 64 抽奖活动id，通过
     * alipay.marketing.campaign.
     * drawcamp.create接口返回
     * 2323443434
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值响应参数
     * code String 是 ~ 网关返回码，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * 40004
     * msg String 是 ~ 网关返回码描述，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * Business Failed
     * sub_code String 否 ~ 网关明细返回码，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * isv.invalid-signature
     * sub_msg String 否 ~ 网关明细返回码描述，详见文
     * 档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * 交易已被支付
     * sign String 是 64 签名，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1
     * DZXh8eeTuAHoYE3w1
     * J+POiPhfDxOYBfUNn1
     * lkeT/V7P4zJdyojWEa6
     * IZs6Hz0yDW5Cp/viufU
     * b5I0/V5WENS3OYR8zR
     * edqo6D+fUTdLHdc+E
     * FyCkiQhBxIzgngPdPdf
     * p1PIS7BdhhzrsZHbRq
     * b7o4k3Dxc+AAnFauu4
     * V6Zdwczo=
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * camp_id String 是 64 抽奖活动id 2016060578473843
     * camp_code String 是 32 活动唯一标识,不能包含除中文
     * 、英文、数字以外的字符，创
     * 建后不能修改，需要保证在商
     * 户端不重复。
     * 131323
     * camp_name String 是 200 活动名称，开发者自定义 抽奖
     * camp_start_time String 是 20 活动开始时间，yyyy-MM-dd
     * HH:00:00格式（到小时），时
     * 间不能早于当前日期的0点
     * 2016-05-05 00:00:00
     * camp_end_time String 是 20 活动结束时间，yyyy-MM-dd
     * HH:00:00格式(到小时)，需要
     * 大于活动开始时间
     * 2016-05-25 00:00:00
     * camp_status String 是 20 活动状态
     * ，CAMP_CREATED(已创建状
     * 态)，CAMP_PAUSED(暂停状
     * 态)，CAMP_ENDED(结束状态
     * )，CAMP_GOING(启动状态
     * )，可由
     * alipay.marketing.campaign.
     * drawcamp.status.update 接
     * 口将已创建状态改为启动状态
     * ，将启动状态改为暂停/结束
     * 状态，将暂停状态置为结束状
     * 态
     * CAMP_GOING
     * creator String 否 64 签约商户的pid，系统默认获
     * 取当前接口调用者的签约pid
     * 2088XXXXXXXXXXXX
     * account_count String 是 20 单用户以支付宝账号维度可参
     * 与当前营销活动的总次数，由
     * 开发者自定义此数值
     * 12
     * certification_count String 是 20 单用户以账户证件号（如身份
     * 证号、护照、军官证等）维度
     * 可参与当前营销活动的总次数
     * ，由开发者自定义此数值
     * 12
     * mobile_count String 是 20 以认证手机号（与支付宝账号
     * 绑定的手机号）维度的可参与
     * 当前营销活动的总次数，由开
     * 发者自定义此数值
     * 12appid_count String 是 20 以移动设备维度可参与当前营
     * 销活动的总次数，由开发者自
     * 定义此数值
     * 12
     * trigger_user_rule_id String 否 64 实时人群验证规则id，由支付
     * 宝配置
     * 123
     * promo_rule_id String 否 64 营销验证规则id，由支付宝配
     * 置
     * 12
     * user_rule_id String 否 64 人群验证规则id，由支付宝配
     * 置
     * hjsdu23323729d
     * cert_rule_id String 否 64 凭证验证规则id，通过
     * alipay.marketing.campaign.
     * cert.create 接口创建的凭证id
     * 12233
     * award_rate String 是 20 活动奖品总中奖几率，开发者
     * 需传入整数值，如：传入99支
     * 付宝默认为99%
     * 99
     * crowd_rule_id String 否 64 圈人规则id，通过
     * alipay.marketing.campaign.
     * rule.crowd.create 接口创建
     * 的规则id
     * 2345675432
     * award_count String 是 20 单个用户当前活动允许中奖的
     * 最大次数，最大值999999
     * 618
     * trigger_type String 是 20 活动触发类型，目前支持
     * CAMP_USER_TRIGGER：用
     * 户触发（开发者调用
     * alipay.marketing.campaign.
     * drawcamp.trigger 接口触发
     * ）；
     * CAMP_SYS_TRIGGER：系统
     * 触发，必须配置实时人群验证
     * 规则（如：配置了监听用户支
     * 付事件，支付宝会根据活动规
     * 则自动发奖，无需用户手动触
     * 发）。
     * CAMP_USER_TRIGGER
     * prize_list MpPrizeInfoMod
     * el[]
     * 是 - 奖品模型，至少有一个奖品模
     * 型
     * -
     * prize_id String 否 64 奖品id，值由支付宝生成；调
     * 用
     * alipay.marketing.campaign.
     * drawcamp.create创建活动时
     * 不需要传入； 调用
     * alipay.marketing.campaign.
     * drawcamp.query接口查询时
     * 会返回；调用
     * alipay.marketing.campaign.
     * drawcamp.update接口修改
     * 活动时，如果不填prize_id,则
     * 会用参数新增一个奖品，并覆
     * 盖之前的奖品，如开发者想保
     * 留或修改当前活动奖品信息
     * ，则在修改接口中此参数必传
     * 。
     * 2016346734834343
     * prize_type String 是 100 奖品类型，目前支持
     * CAMP_CERT_PRIZE，凭证奖
     * 品类型
     * CAMP_CERT_PRIZE
     * certlot_number String 是 64 凭证id，通过
     * alipay.marketing.campaign.
     * cert.create 接口创建的凭证
     * id，开发者可以根据此凭证处
     * 理自己的业务逻辑，如给用户
     * 发放自定义优惠券等；
     * 12345566
     * prize_name String 是 200 奖品名称，开发者自定义 天猫券
     * prize_total String 是 20 奖品总数量，数值，最大
     * 999999
     * 1234
     * prize_start_time String 是 20 奖品开始时间，yyyy-mm-dd
     * 00:00:00格式，需在活动有效
     * 期内，不能晚于奖品结束时间
     * 2016-05-05 00:00:00
     * prize_end_time String 是 20 奖品结束时间，yyyy-mm-dd
     * 00:00:00格式，大于奖品开始
     * 时间，必须在活动有效期内
     * 2016-05-25 00:00:00请求示例
     * JAVA版本
     * PHP版本
     * .NET版本
     * HTTP版本
     * prize_max_award_limit String 是 20 单个用户当前奖品允许领取的
     * 最大次数，最大999999，原
     * 则上活动领取次数与奖品领取
     * 次数保持一致，特殊情况如
     * ：中奖次数每人可中2次，但
     * 奖品只能每人领取一个，则中
     * 奖次数每人只能一次。该属性
     * 不支持修改，修改时透传处理
     * 618
     * frequency_type String 否 5 奖品中奖频率类型: D，每自然
     * 日；W，每自然周（从周一至
     * 周日）；M，每自然月
     * D
     * frequency_count String 否 20 奖品频率对应的次数，最大
     * 999999，如
     * frequency_type为‘D’，值
     * 为2，则表示每日的奖品最多
     * 可领取2次
     */
    public function alipayMarketingCampaignDrawcampQueryRequest($camp_id)
    {
        // alipay.marketing.campaign.drawcamp.query(营销抽奖活动查询)
        $request = new AlipayMarketingCampaignDrawcampQueryRequest();
        $bizContent = array();
        $bizContent['camp_id'] = $camp_id;
        $str = json_encode($bizContent);
        // $str = "{" . "\"camp_id\":\"{$camp_id}\"" . "}";
        // die($str);
        $request->setBizContent($str);
        $result = $this->aopclient_request_execute($request);
        
        return $result;
    }

    /**
     * alipay.finance.fund.fundquotation.query(查询基金产品行情数据)
     * 版本号 1.0
     * 不需要授权
     * API应用场景说明
     * 将统一的数据提供给平台上的财富号机构使用，为机构赋能，提供更加完整的服务。
     * 财富号机构通过基金编号查询产品行情信息。
     * 公共参数
     * 请求地址
     * 公共请求参数
     * 请求参数
     * 公共响应参数
     * 环境 HTTPS请求地址
     * 正式环境 https://openapi.alipay.com/gateway.do
     * API应用场景说明 类型 是否
     * 必填
     * 最大
     * 长度
     * 描述 示例值
     * app_id String 是 32 支付宝分配给开发者的应用ID 2014072300007148
     * method String 是 128 接口名称 alipay.finance.fund.fu
     * ndquotation.query
     * format String 否 40 仅支持JSON JSON
     * charset String 是 10 请求使用的编码格式，如utf-8,gbk,gb2312等 utf-8
     * sign_type String 是 10 商户生成签名字符串所使用的签名算法类型，目
     * 前支持RSA2和RSA，推荐使用RSA2
     * RSA2
     * sign String 是 256 商户请求参数的签名串，详见<a
     * href="https://doc.open.alipay.com/docs/doc.
     * htm?treeId=291&articleId=105974&docType=
     * 1">签名</a>
     * 详见示例
     * timestamp String 是 19 发送请求的时间，格式"yyyy-MM-dd
     * HH:mm:ss"
     * 2014-07-24 03:07:50
     * version String 是 3 调用的接口版本，固定为：1.0 1.0
     * app_auth_token String 否 40 详见<a
     * href="https://doc.open.alipay.com/doc2/deta
     * il.htm?treeId=216&articleId=105193&docType
     * =1">应用授权概述</a>
     * biz_content String 是 - 请求参数的集合，最大长度不限，除公共参数外
     * 所有请求参数都必须放在这个参数中传递，具体
     * 参照各产品快速接入文档
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * fund_code String 是 6 基金编号：基金产品编号 519690
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值响应参数
     * 请求示例
     * JAVA版本
     * code String 是 ~ 网关返回码，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * 40004
     * msg String 是 ~ 网关返回码描述，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * Business Failed
     * sub_code String 否 ~ 网关明细返回码，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * isv.invalid-signature
     * sub_msg String 否 ~ 网关明细返回码描述，详见文
     * 档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1#s2
     * 交易已被支付
     * sign String 是 64 签名，详见文档
     * :https://doc.open.alipay.co
     * m/doc2/detail.htm?treeId=2
     * 00&articleId=105351&docTy
     * pe=1
     * DZXh8eeTuAHoYE3w1
     * J+POiPhfDxOYBfUNn1
     * lkeT/V7P4zJdyojWEa6
     * IZs6Hz0yDW5Cp/viufU
     * b5I0/V5WENS3OYR8zR
     * edqo6D+fUTdLHdc+E
     * FyCkiQhBxIzgngPdPdf
     * p1PIS7BdhhzrsZHbRq
     * b7o4k3Dxc+AAnFauu4
     * V6Zdwczo=
     * 参数 类型 是否
     * 必须
     * 最大
     * 长度
     * 描述 示例值
     * report_date String 是 10 发布日期：产品收益率发布的
     * 时间
     * 20170814
     * last_week String 否 10 近一周收益率：基金产品近一
     * 周内收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     * last_month String 否 10 近一月收益率：基金产品近一
     * 月内收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     * last_quarter String 否 10 近三月收益：基金产品近三月
     * 内收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     * last_half_year String 否 10 近半年收益：基金产品近半年
     * 内收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     * last_year String 否 10 近一年收益率：基金产品近一
     * 年内收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     * yield_3_year String 否 10 三年内收益率：基金产品三年
     * 内收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     * yield_5_year String 否 10 近五年收益率：基金产品近五
     * 年内收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     * this_year String 否 10 今年以来收益率：基金产品今
     * 年以来收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     * since_establishment String 否 10 成立以来收益率：基金产品成
     * 立以来收益率
     * 四舍五入后保留小数点后4位
     * 0.1000
     */
    public function alipayFinanceFundFundquotationQueryRequest($fund_code)
    {
        // alipay.finance.fund.fundquotation.query(查询基金产品行情数据)
        $request = new AlipayFinanceFundFundquotationQueryRequest();
        $bizContent = array();
        $bizContent['fund_code'] = $fund_code;
        $request->setBizContent(json_encode($bizContent));
        $result = $this->aopclient_request_execute($request);
        
        return $result;
    }
    
    // 转换编码
    protected function characet($data)
    {
        if (! empty($data)) {
            $fileType = mb_detect_encoding($data, array(
                'UTF-8',
                'GBK',
                'GB2312',
                'LATIN1',
                'BIG5'
            ));
            if ($fileType != 'UTF-8') {
                $data = mb_convert_encoding($data, 'UTF-8', $fileType);
            }
        }
        return $data;
    }

    /**
     * 使用SDK执行接口请求
     *
     * @param unknown $request            
     * @param string $token            
     * @return array
     */
    protected function aopclient_request_execute($request, $token = NULL)
    {
        $config = $this->config;
        $aop = new \AopClient();
        $aop->gatewayUrl = $config['gatewayUrl'];
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKey = $config['merchant_private_key'];
        $aop->alipayrsaPublicKey = $config['alipay_public_key'];
        $aop->signType = $config['sign_type'];
        $aop->apiVersion = "1.0";
        $resultold = $result = $aop->execute($request, $token);
        if (empty($result)) {
            throw new \Exception('请求alipay请求');
        }
        // 转化成数组格式
        $result = $this->convertToArray($result);
        $paramsMsg = '';
        if (method_exists($request, 'getBizContent')) {
            $paramsMsg = ",request params:" . $request->getBizContent();
        }
        if (isset($result['error_response'])) {
            $result = $result['error_response'];
            // 返回了错误信息
            // 如：[code] => 40002
            // [msg] => Invalid Arguments
            // [sub_code] => isv.code-invalid
            // [sub_msg] => 授权码code无效
            throw new \Exception("msg:{$result['msg']} sub_code:{$result['sub_code']} sub_msg:{$result['sub_msg']} {$paramsMsg}", $result['code']);
        } else {
            // 获取响应信息
            $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
            $result = $result[$responseNode];
            
            // 检查是否有code
            // 如果没有，直接返回,有些接口没有code,例如获取accesstoken
            if (! isset($result['code'])) {
                return $result;
            }
            // 如果有就检查code的值
            if (! empty($result['code']) && $result['code'] == 10000) {
                return $result;
            } else {
                throw new \Exception("msg:{$result['msg']} sub_code:{$result['sub_code']} sub_msg:{$result['sub_msg']} {$paramsMsg}", $result['code']);
            }
        }
        
        return $result;
    }

    protected function convertToArray($json)
    {
        $json = json_encode($json);
        return json_decode($json, true);
    }
}