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
                    // $content = strval($sence_id) . '_scene';
                    // $to_groupid = $v;
                    // //回调微信用户加入分组
                    // $this->_weixin->getGroupManager()->membersUpdate($FromUserName, $to_groupid);
                    // }
                    // } else {
                    // $content = "扫描二维码{$sence_id}";
                    // }
                    
                    // 不同项目特定的业务逻辑结束
                }
            } elseif ($Event == 'location_select') { // 自定义菜单事件推送 -location_select：弹出地理位置选择器的事件推送
                                                     
                // 相对点击事件做特别处理，请在这里，并删除$content = $EventKey;
                $content = $EventKey;
                
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
                $this->processCard();
                $response = 'success';
            }
        }
        
        // 不同项目特定的业务逻辑开始
        if ($MsgType == 'text') { // 接收普通消息----文本消息
            if ($content == "客服测试") {
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
        
        $datas['content_process'] = $content;
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
     * @param string $content            
     * @return boolean
     */
    protected function processCard()
    {
        // $datas = $this->_sourceDatas;
        // $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
        // $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
        // $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
        // $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
        // $CreateTime = isset($datas['CreateTime']) ? intval($datas['CreateTime']) : 0;
        // $CardId = isset($datas['CardId']) ? trim($datas['CardId']) : '';
        // $IsGiveByFriend = isset($datas['IsGiveByFriend']) ? intval($datas['IsGiveByFriend']) : 0;
        // $UserCardCode = isset($datas['UserCardCode']) ? trim($datas['UserCardCode']) : '';
        // $FriendUserName = isset($datas['FriendUserName']) ? trim($datas['FriendUserName']) : '';
        
        // switch ($Event) {
        // case 'card_pass_check':
        // case 'card_not_pass_check':
        // // 审核通过或不通过事件推送
        // /**
        // * 生成的卡券通过审核时，微信会把这个事件推送到开发者填写的URL。
        // * 推送XML数据包示例：
        // * <xml> <ToUserName><![CDATA[toUser]]></ToUserName>
        // * <FromUserName><![CDATA[FromUser]]></FromUserName>
        // * <CreateTime>123456789</CreateTime>
        // * <MsgType><![CDATA[event]]></MsgType>
        // * <Event><![CDATA[card_pass_check]]></Event> //不通过为card_not_pass_check
        // * <CardId><![CDATA[cardid]]></CardId>
        // * </xml>
        // */
        // $modelCardEvent = new Weixincard_Model_Event();
        // $modelCardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode);
        
        // // 根据cardid更新审核是否通过信息
        
        // break;
        // case 'user_get_card':
        // // 领取卡券事件推送
        // /**
        // * 用户在领取卡券时，微信会把这个事件推送到开发者填写的URL。
        // * 推送XML数据包示例：
        // * <xml> <ToUserName><![CDATA[toUser]]></ToUserName>
        // * <FromUserName><![CDATA[FromUser]]></FromUserName>
        // * <FriendUserName><![CDATA[FriendUser]]></FriendUserName>
        // * <CreateTime>123456789</CreateTime>
        // * <MsgType><![CDATA[event]]></MsgType>
        // * <Event><![CDATA[user_get_card]]></Event>
        // * <CardId><![CDATA[cardid]]></CardId>
        // * <IsGiveByFriend>1</IsGiveByFriend>
        // * <UserCardCode><![CDATA[12312312]]></UserCardCode>
        // * </xml>
        // */
        // $modelCardEvent = new Weixincard_Model_Event();
        // $modelCardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode);
        
        // // 在卡包里面记录信息
        // $modelCardBag = new Weixincard_Model_CardBag();
        // $modelCardBag->getCard($FromUserName, $CardId, $UserCardCode, $CreateTime, $IsGiveByFriend, $FriendUserName, false, false, "");
        
        // break;
        // case 'user_del_card':
        // // 删除卡券事件推送
        // /**
        // * 用户在删除卡券时，微信会把这个事件推送到开发者填写的URL。
        // * 推送XML数据包示例：
        // * <xml> <ToUserName><![CDATA[toUser]]></ToUserName>
        // * <FromUserName><![CDATA[FromUser]]></FromUserName>
        // * <CreateTime>123456789</CreateTime>
        // * <MsgType><![CDATA[event]]></MsgType>
        // * <Event><![CDATA[user_del_card]]></Event>
        // * <CardId><![CDATA[cardid]]></CardId>
        // * <UserCardCode><![CDATA[12312312]]></UserCardCode>
        // * </xml>
        // */
        // $modelCardEvent = new Weixincard_Model_Event();
        // $modelCardEvent->record($ToUserName, $FromUserName, $CreateTime, $MsgType, $Event, $CardId, $FriendUserName, $IsGiveByFriend, $UserCardCode);
        
        // // 删除卡包数据
        // $modelCardBag = new Weixincard_Model_CardBag();
        // $modelCardBag->deleteCard($CardId, $UserCardCode);
        
        // break;
        // default:
        // break;
        // }
        return true;
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