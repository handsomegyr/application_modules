<?php
namespace App\Weixin\Services;

class Base
{

    /**
     *
     * @return \App\Weixin\Services\Base
     */
    public static function getServiceObject()
    {
        return new self();
    }

    /**
     *
     * @var \Weixin\Client
     */
    public $_weixin = null;

    /**
     * 处理特殊的逻辑
     *
     * @param array $datas            
     * @return array
     */
    public function doSpecialLogic(array $datas)
    {
        $content = isset($datas['Content']) ? strtolower(trim($datas['Content'])) : '';
		$content_process = isset($datas['content_process']) ? strtolower(trim($datas['content_process'])) : '';
		$FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
        $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
        $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
        $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
        $EventKey = isset($datas['EventKey']) ? trim($datas['EventKey']) : '';
        $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';
        $Ticket = isset($datas['Ticket']) ? trim($datas['Ticket']) : '';
        $response = isset($datas['response']) ? strtolower(trim($datas['response'])) : '';
        
        // 转化为关键词方式，表示关注
        if ($MsgType == 'event') { // 接收事件推送
            if ($Event == 'subscribe') { // 关注事件
                /**
                 */
                // EventKey 事件KEY值，qrscene_为前缀，后面为二维码的参数值
                
                // Ticket 二维码的ticket，可用来换取二维码图片
                if (! empty($Ticket) && ! empty($EventKey)) { // 扫描带参数二维码事件 用户未关注时，进行关注后的事件推送
                /**
                 */
                    // $sence_info = $this->_scene->getSceneById($sence_id);
                    // if (isset($sence_info['groups_id'])) {
                    // //二维码场景管理微信组ID
                    // foreach ($sence_info['groups_id'] as $k => $v) {
                    // $content_process = strval($sence_id) . '_scene';
                    // $to_groupid = $v;
                    // //回调微信用户加入分组
                    // $this->_weixin->getGroupManager()->membersUpdate($FromUserName, $to_groupid);
                    // }
                    // } else {
                    // $content_process = "扫描二维码{$sence_id}";
                    // }
                    
                    // 不同项目特定的业务逻辑结束
                }
            } elseif ($Event == 'location_select') { // 自定义菜单事件推送 -location_select：弹出地理位置选择器的事件推送
                
                /**
                 * <SendLocationInfo>
                 * <Location_X><![CDATA[23]]></Location_X>
                 * <Location_Y><![CDATA[113]]></Location_Y>
                 * <Scale><![CDATA[15]]></Scale>
                 * <Label><![CDATA[ 广州市海珠区客村艺苑路 106号]]></Label>
                 * <Poiname><![CDATA[]]></Poiname>
                 * </SendLocationInfo>
                 */
                
                // SendLocationInfo 发送的位置信息
                // Location_X X坐标信息
                // Location_Y Y坐标信息
                // Scale 精度，可理解为精度或者比例尺、越精细的话 scale越高
                // Label 地理位置的字符串信息
                // Poiname 朋友圈POI的名字，可能为空
                $Location_X = isset($datas['SendLocationInfo']['Location_X']) ? trim($datas['SendLocationInfo']['Location_X']) : 0;
                $Location_Y = isset($datas['SendLocationInfo']['Location_Y']) ? trim($datas['SendLocationInfo']['Location_Y']) : 0;
                $Scale = isset($datas['SendLocationInfo']['Scale']) ? trim($datas['SendLocationInfo']['Scale']) : 0;
                $Label = isset($datas['SendLocationInfo']['Label']) ? trim($datas['SendLocationInfo']['Label']) : "";
                $Poiname = isset($datas['SendLocationInfo']['Poiname']) ? trim($datas['SendLocationInfo']['Poiname']) : "";
                
                $articles = $this->shopLocation($Location_X, $Location_Y);
                if (! empty($articles)) {
                    $response = $this->_weixin->getMsgManager()
                        ->getReplySender()
                        ->replyGraphText($articles);
                }
            } else {
                // 卡券处理
                $response = $this->processCard($datas);
                //$response = 'success';
            }
        }
        
        // 不同项目特定的业务逻辑开始
        if ($MsgType == 'text') { // 接收普通消息----文本消息
            if ($content_process == "客服测试") {
                $response = $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyCustomerService();
            }
        }
        // 不同项目特定的业务逻辑结束
        
        // 处理地理位置信息开始
        if ($MsgType == 'location') { // 接收普通消息----地理位置消息
            /**
             */
            // Location_X 地理位置维度
            // Location_Y 地理位置经度
            // Scale 地图缩放大小
            $Location_X = isset($datas['Location_X']) ? trim($datas['Location_X']) : 0;
            $Location_Y = isset($datas['Location_Y']) ? trim($datas['Location_Y']) : 0;
            $Scale = isset($datas['Scale']) ? trim($datas['Scale']) : 0;
            
            $articles = $this->shopLocation($Location_X, $Location_Y);
            if (! empty($articles)) {
                $response = $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyGraphText($articles);
            }
        }
        
        $datas['content_process'] = $content_process;
        $datas['response'] = $response;
        return $datas;
    }

    /**
     * 处理后的处理
     *
     * @param array $data            
     * @return boolean
     */
    public function processAfter(array $data)
    {}

    /**
     * 处理授权后特殊的逻辑
     *
     * @param array $datas            
     * @return array
     */
    public function doSnsCallback(array $datas)
    {}

    /**
     * 卡券的处理
     *
     * @param array $datas            
     * @return boolean
     */
    protected function processCard(array $datas)
    {
		$response = 'success';
        $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
        $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
        $CreateTime = isset($datas['CreateTime']) ? intval($datas['CreateTime']) : time();
        $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
        $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
        $CardId = isset($datas['CardId']) ? trim($datas['CardId']) : '';
        $UserCardCode = isset($datas['UserCardCode']) ? trim($datas['UserCardCode']) : '';
        $OuterId = isset($datas['OuterId']) ? trim($datas['OuterId']) : '';
        
        $encrypt_code = isset($datas['encrypt_code']) ? trim($datas['encrypt_code']) : '';
        $new_code = isset($datas['new_code']) ? trim($datas['new_code']) : '';
        
        $content = file_get_contents('php://input');
        
        $modelCard = new \App\Weixincard\Models\Card();
        $modelCard->setWeixin($this->_weixin);
        $modelWeixincardEvent = new \App\Weixincard\Models\Event();
        $modelWeixincardCardBag = new \App\Weixincard\Models\CardBag();
        
        switch ($Event) {
            case 'card_pass_check':
            case 'card_not_pass_check':
                // 审核事件推送
                /**
                 * 生成的卡券通过审核时，微信会把这个事件推送到开发者填写的URL。
                 * <xml>
                 * <ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
                 * <FromUserName><![CDATA[o4ELSv0kyOMGtHtBnnsED3HAE8sM]]></FromUserName>
                 * <CreateTime>1486103360</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[card_pass_check]]></Event>
                 * <CardId><![CDATA[p4ELSv5DoUT4SxCgIJ7_tUVRTfx8]]></CardId>
                 * <RefuseReason><![CDATA[]]></RefuseReason>
                 * </xml>
                 */
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                // 获取最新的卡券信息并且更新本地的信息
                $modelCard->getAndUpdateCardInfo($CardId);
                
                break;
            case 'user_get_card':
                // 领取事件推送
                /**
                 * 用户在领取卡券时，微信会把这个事件推送到开发者填写的URL。推送XML数据包示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
                 * <FromUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FromUserName>
                 * <CreateTime>1486087470</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[user_get_card]]></Event>
                 * <CardId><![CDATA[p4ELSvyOp16PTtrkxzWw_QybcorA]]></CardId>
                 * <IsGiveByFriend>0</IsGiveByFriend>
                 * <UserCardCode><![CDATA[985522410649]]></UserCardCode>
                 * <FriendUserName><![CDATA[]]></FriendUserName>
                 * <OuterId>1</OuterId>
                 * <OldUserCardCode><![CDATA[]]></OldUserCardCode>
                 * <IsRestoreMemberCard>0</IsRestoreMemberCard>
                 * <IsRecommendByFriend>0</IsRecommendByFriend>
                 * <SourceScene><![CDATA[SOURCE_SCENE_QRCODE]]></SourceScene>
                 * </xml>
                 */
                
                /**
                 * 用户的朋友在领取卡券时，微信会把这个事件推送到开发者填写的URL。推送XML数据包示例：
                 * <xml><ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
                 * <FromUserName><![CDATA[o4ELSv7CChC3YKmM8WKXX4kXSr8c]]></FromUserName>
                 * <CreateTime>1486107437</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[user_get_card]]></Event>
                 * <CardId><![CDATA[p4ELSv5DoUT4SxCgIJ7_tUVRTfx8]]></CardId>
                 * <IsGiveByFriend>1</IsGiveByFriend>
                 * <UserCardCode><![CDATA[072975907291]]></UserCardCode>
                 * <FriendUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FriendUserName>
                 * <OuterId>0</OuterId>
                 * <OldUserCardCode><![CDATA[395079429012]]></OldUserCardCode>
                 * <OutetStr><![CDATA[]]></OutetStr>
                 * </xml>
                 */
                $IsGiveByFriend = isset($datas['IsGiveByFriend']) ? intval($datas['IsGiveByFriend']) : 0;
                $FriendUserName = isset($datas['FriendUserName']) ? trim($datas['FriendUserName']) : '';
                
                $OldUserCardCode = isset($datas['OldUserCardCode']) ? trim($datas['OldUserCardCode']) : '';
                $IsRestoreMemberCard = isset($datas['IsRestoreMemberCard']) ? intval($datas['IsRestoreMemberCard']) : 0;
                $IsRecommendByFriend = isset($datas['IsRecommendByFriend']) ? intval($datas['IsRecommendByFriend']) : 0;
                $SourceScene = isset($datas['SourceScene']) ? trim($datas['SourceScene']) : '';
                
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                // 领取卡券处理
                $newCardBag = $modelWeixincardCardBag->userGetCard($CardId, $UserCardCode, $FromUserName, $CreateTime, $IsGiveByFriend, $FriendUserName, $OuterId, $OldUserCardCode, $IsRestoreMemberCard, $IsRecommendByFriend, $SourceScene, $encrypt_code, $new_code, $datas);
                // 增加该卡券的领取数量
                $modelCard->incReceivedNum($CardId, 1);
                // 如果是转赠朋友的话
                if ($IsGiveByFriend) {
                    // 更新原来的卡包的信息
                    $friend_card_bag_id = $newCardBag['_id'];
                    $modelWeixincardCardBag->giveCardToFriend($CardId, $OldUserCardCode, $FriendUserName, $CreateTime, $friend_card_bag_id, $datas);
                    // 增加转赠朋友数
                    $modelCard->incGiveByFriendNum($CardId, 1);
                }
                
                break;
            
            case 'user_del_card':
                // 删除事件推送
                /**
                 * 用户在删除卡券时，微信会把这个事件推送到开发者填写的URL。 推送XML数据包示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
                 * <FromUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FromUserName>
                 * <CreateTime>1486105477</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[user_del_card]]></Event>
                 * <CardId><![CDATA[p4ELSv5DoUT4SxCgIJ7_tUVRTfx8]]></CardId>
                 * <UserCardCode><![CDATA[247249135439]]></UserCardCode>
                 * </xml>
                 */
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                // 删除卡包数据
                $modelWeixincardCardBag->userDelCard($CardId, $UserCardCode, $FromUserName, $CreateTime, $datas);
                
                // 增加该卡券的删除数量
                $modelCard->incDeletedNum($CardId, 1);
                
                // // 删除会员卡卡包数据
                // $this->_weixinMemberCardBag->userDelCard($CardId, $UserCardCode);
                break;
            
            case 'user_consume_card':
                // 核销事件推送
                /**
                 * 卡券被核销时，微信会把这个事件推送到开发者填写的URL。 推送XML数据包示例：
                 * <xml>
                 * <ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
                 * <FromUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FromUserName>
                 * <CreateTime>1486099185</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[user_consume_card]]></Event>
                 * <CardId><![CDATA[p4ELSvyOp16PTtrkxzWw_QybcorA]]></CardId>
                 * <UserCardCode><![CDATA[985522410649]]></UserCardCode>
                 * <ConsumeSource><![CDATA[FROM_API]]></ConsumeSource>
                 * <LocationName><![CDATA[]]></LocationName>
                 * <StaffOpenId><![CDATA[o4ELSv5pqWRhNXqdL3Mp_93-g80s]]></StaffOpenId>
                 * <VerifyCode><![CDATA[]]></VerifyCode>
                 * <RemarkAmount><![CDATA[]]></RemarkAmount>
                 * <OuterStr><![CDATA[]]></OuterStr>
                 * <LocationId>0</LocationId>
                 * </xml>
                 */
                
                $ConsumeSource = isset($datas['ConsumeSource']) ? trim($datas['ConsumeSource']) : '';
                $StaffOpenId = isset($datas['StaffOpenId']) ? trim($datas['StaffOpenId']) : '';
                $LocationId = isset($datas['LocationId']) ? trim($datas['LocationId']) : '';
                $LocationName = isset($datas['LocationName']) ? trim($datas['LocationName']) : '';
                
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                // 核销卡券处理
                $newCardBag = $modelWeixincardCardBag->userConsumeCard($CardId, $UserCardCode, $FromUserName, $CreateTime, $ConsumeSource, $StaffOpenId, $LocationId, $LocationName, $datas);
                
                // 增加该卡券的核销数量
                $modelCard->incConsumedNum($CardId, 1);
                
                break;
            
            case 'user_pay_from_pay_cell':
                // 买单事件推送
                /**
                 * 微信买单完成时，微信会把这个事件推送到开发者填写的URL。 推送XML数据包示例：
                 * <xml>
                 * <ToUserName><![CDATA[gh_e2243xxxxxxx]]></ToUserName>
                 * <FromUserName><![CDATA[oo2VNuOUuZGMxxxxxxxx]]></FromUserName>
                 * <CreateTime>1442390947</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[user_pay_from_pay_cell]]></Event>
                 * <CardId><![CDATA[po2VNuCuRo-8sxxxxxxxxxxx]]></CardId>
                 * <UserCardCode><![CDATA[38050000000]]></UserCardCode>
                 * <TransId><![CDATA[10022403432015000000000]]></TransId>
                 * <LocationId>291710000</LocationId>
                 * <Fee><![CDATA[10000]]></Fee>
                 * <OriginalFee><![CDATA[10000]]> </OriginalFee>
                 * </xml>
                 */
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                break;
            case 'user_view_card':
                // 进入会员卡事件推送
                /**
                 * 用户在进入会员卡时，微信会把这个事件推送到开发者填写的URL。
                 *
                 * 需要开发者在创建会员卡时填入need_push_on_view 字段并设置为true。开发者须综合考虑领卡人数和服务器压力，决定是否接收该事件。
                 *
                 * 推送XML数据包示例：
                 * <xml> <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[FromUser]]></FromUserName>
                 * <CreateTime>123456789</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[user_view_card]]></Event>
                 * <CardId><![CDATA[cardid]]></CardId>
                 * <UserCardCode><![CDATA[12312312]]></UserCardCode>
                 * </xml>
                 */
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                break;
            case 'user_enter_session_from_card':
                // 从卡券进入公众号会话事件推送
                /**
                 * 用户在卡券里点击查看公众号进入会话时（需要用户已经关注公众号），微信会把这个事件推送到开发者填写的URL。开发者可识别从卡券进入公众号的用户身份。 推送XML数据包示例：
                 *
                 * <xml> <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[FromUser]]></FromUserName>
                 * <CreateTime>123456789</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[user_enter_session_from_card]]></Event>
                 * <CardId><![CDATA[cardid]]></CardId>
                 * <UserCardCode><![CDATA[12312312]]></UserCardCode>
                 * </xml>
                 */
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                break;
            
            case 'update_member_card':
                // 会员卡内容更新事件
                /**
                 * 当用户的会员卡积分余额发生变动时，微信会推送事件告知开发者。 推送XML数据包示例：
                 * <xml><ToUserName><![CDATA[gh_9e1765b5568e]]></ToUserName>
                 * <FromUserName><![CDATA[ojZ8YtyVyr30HheH3CM73y7h4jJE]]></FromUserName>
                 * <CreateTime>1445507140</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[update_member_card]]></Event>
                 * <CardId><![CDATA[pjZ8Ytx-nwvpCRyQneH3Ncmh6N94]]></CardId>
                 * <UserCardCode><![CDATA[485027611252]]></UserCardCode>
                 * <ModifyBonus>3</ModifyBonus>
                 * <ModifyBalance>0</ModifyBalance>
                 * </xml>
                 */
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                break;
            case 'card_sku_remind':
                // 库存报警事件
                /**
                 * 用户领券时，若此时库存数小于预警值（默认为100），会发送事件给商户，事件每隔12小时发送一次。
                 *
                 * <xml>
                 * <ToUserName><![CDATA[gh_2d62d*****0]]></ToUserName>
                 * <FromUserName><![CDATA[oa3LFuBvWb7*********]]></FromUserName>
                 * <CreateTime>1443838506</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[card_sku_remind]]></Event>
                 * <CardId><![CDATA[pa3LFuAh2P65**********]]></CardId>
                 * <Detail><![CDATA[the card's quantity is equal to 0]]></Detail>
                 * // </xml>
                 */
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                break;
            
            case 'card_pay_order':
                // 券点流水详情事件
                /**
                 * 当商户朋友的券券点发生变动时，微信服务器会推送消息给商户服务器。
                 *
                 * <xml>
                 * <ToUserName><![CDATA[gh_7223c83d4be5]]></ToUserName>
                 * <FromUserName><![CDATA[ob5E7s-HoN9tslQY3-0I4qmgluHk]]></FromUserName>
                 * <CreateTime>1453295737</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[card_pay_order]]></Event>
                 * <OrderId><![CDATA[404091456]]></OrderId>
                 * <Status><![CDATA[ORDER_STATUS_FINANCE_SUCC]]></Status>
                 * <CreateTime>1453295737</CreateTime>
                 * <PayFinishTime>0</PayFinishTime>
                 * <Desc><![CDATA[]]></Desc>
                 * <FreeCoinCount><![CDATA[200]]></FreeCoinCount>
                 * <PayCoinCount><![CDATA[0]]></PayCoinCount>
                 * <RefundFreeCoinCount><![CDATA[0]]></RefundFreeCoinCount>
                 * <RefundPayCoinCount><![CDATA[0]]></RefundPayCoinCount>
                 * <OrderType><![CDATA[ORDER_TYPE_SYS_ADD]]></OrderType>
                 * <Memo><![CDATA[开通账户奖励]]></Memo>
                 * <ReceiptInfo><![CDATA[]]></ReceiptInfo>
                 * </xml>
                 */
                $modelWeixincardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode, $OuterId, $content);
                
                break;
            default:
				$response ='';
                break;
        }
		
        return $response;
    }

    /**
     * 百度地图API URI标示服务
     *
     * @param float $lat
     *            lat<纬度>,lng<经度>
     * @param float $lng
     *            lat<纬度>,lng<经度>
     * @param string $title
     *            标注点显示标题
     * @param string $content
     *            标注点显示内容
     * @param int $zoom
     *            展现地图的级别，默认为视觉最优级别。
     * @param string $output
     *            表示输出类型，web上必须指定为html才能展现地图产品结果
     * @return string
     */
    protected function mapUrl($lat, $lng, $title = '', $content = '', $zoom = '', $output = 'html')
    {
        $title = rawurlencode($title);
        $content = rawurlencode($content);
        return "http://api.map.baidu.com/marker?location={$lat},{$lng}&title={$title}&content={$content}&zoom={$zoom}&output={$output}&referer=catholic";
    }

    /**
     * 生成某个坐标的静态定位图片
     *
     * @param float $lat
     *            lat<纬度>,lng<经度>
     * @param float $lng
     *            lat<纬度>,lng<经度>
     * @param int $width
     *            图片宽度。取值范围：(0, 1024]。默认400
     * @param int $height
     *            图片高度。取值范围：(0, 1024]。 默认300
     * @param int $zoom
     *            地图级别。取值范围：[1, 18]。 默认11
     * @return string
     */
    protected function mapImage($lat, $lng, $width = 400, $height = 300, $zoom = 11)
    {
        return "http://api.map.baidu.com/staticimage?center={$lng},{$lat}&markers={$lng},{$lat}&width={$width}&height={$height}&zoom={$zoom}";
    }

    protected function shopLocation($Location_X, $Location_Y)
    {
        return array();
        $modelShop = new Cronjob_Model_Shop();
        $shopList = $modelShop->getNearby($Location_Y, $Location_X, 2000, 1, 10);
        $shopList = $shopList['list'];
        
        $articles = array();
        if (count($shopList) > 0) {
            $count = 0;
            foreach ($shopList as $item) {
                $name = (string) $item['name'];
                $address = (string) $item['address'];
                $longitude = (string) $item['location'][0];
                $latitude = (string) $item['location'][1];
                
                $article = array();
                $article['title'] = $name;
                $article['description'] = $address;
                if ($count == 0) {
                    $article['picurl'] = $this->mapImage($latitude, $longitude, 640, 320);
                } else {
                    // $article['picurl'] = 'http://scrm.umaman.com/soa/image/get/id/52e096624896191904cee0bd/size/80x80';
                    $article['picurl'] = '';
                }
                
                $article['url'] = $this->mapUrl($latitude, $longitude, $name, $address);
                
                array_push($articles, $article);
                $count ++;
                // 只要推送5条地理位置信息
                if ($count >= 5) {
                    break;
                }
            }
        }
        
        return $articles;
    }
}