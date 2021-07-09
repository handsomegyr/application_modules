<?php

namespace App\Campaign\Controllers;

/**
 * 微信卡券事例
 *
 * @author Administrator
 *        
 */
class WeixincardController extends ControllerBase
{

    private $secretKey = 'weixin_card';

    private $modelCard = null;

    private $modelCardBag = null;

    private $modelWeixinApplication = null;

    protected function doCampaignInitialize()
    {
        $this->modelCard = new \App\Weixincard\Models\Card();
        $this->modelCardBag = new \App\Weixincard\Models\CardBag();
        $this->modelWeixinApplication = new \App\Weixin\Models\Application();
    }

    /**
     * 用户获取微信卡券的页面
     *
     * @name 获取微信卡券
     */
    public function indexAction()
    {
        // http://www.myapplicationmodule.com/campaign/weixincard/index?wechat_card_js=1&openid=xx&card_id=556d2bb249961986398b45db&card_code=code1&balance=0&outer_id=0&cb_url=http%3A%2F%2Fwww.baidu.com%2F&FromUserName=ogW8rtyob18sg_MBKH-V2qYe2tt4&istest=1&sign=xxx
        try {
            // 回调地址
            $cb_url = $this->get('cb_url', '');
            $cb_url = urldecode($cb_url);

            $openid = $this->get('openid', '');
            $card_id = $this->get('card_id', '');
            $card_code = $this->get('card_code', '');
            $balance = intval($this->get('balance', '0'));
            $outer_id = intval($this->get('outer_id', '0'));
            // 签名
            $sign = trim($this->get('sign', ''));

            // 计算签名
            $calcSign = $this->getSignKey4WeixinCard($openid, $card_code, $card_id);
            if ($sign != $calcSign) {
                $this->assign("error", array(
                    'error_code' => -999,
                    'error_msg' => "签名不正确，非法访问"
                ));
                return false;
            }

            // 检查该卡券是否存在
            $cardInfo = $this->modelCard->getInfoById($card_id);
            if (empty($cardInfo)) {
                $this->assign("error", array(
                    'error_code' => -1,
                    'error_msg' => "对应的卡券ID不存在"
                ));
                return false;
            }
            $wx_card_id = $weixinCardInfo['card_id']; // 微信卡券的卡券ID

            // 获取签名
            $signature = $this->getSignature($wx_card_id, $card_code, $openid, $outer_id, $balance);

            $result = array();
            $result['signatureInfo'] = $signature;
            $result['wx_card_id'] = $wx_card_id;
            $result['card_code'] = $card_code;
            $result['callbackUrl'] = $cb_url;
            $this->assign("ret", $result);
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取签名
     *
     * @param string $card_id            
     * @param string $code            
     * @param string $openid            
     * @param number $outer_id            
     * @param number $balance            
     * @return array
     */
    private function getSignature($card_id, $code, $openid, $outer_id = 0, $balance = 0)
    {
        // api_ticket、timestamp、card_id、code、openid、balance
        $config = $this->getDI()->get('config');
        $token = $this->modelWeixinApplication->getTokenByAppid($config['weixin']['appid']);

        $api_ticket = $token['wx_card_api_ticket'];
        $timestamp = (string) time();
        $outer_id = (string) $outer_id;
        $balance = (string) $balance;

        $objSignature = new \Weixin\Model\Signature();
        $objSignature->add_data($api_ticket);
        $objSignature->add_data($timestamp);
        $objSignature->add_data($card_id);
        $objSignature->add_data($code);
        $objSignature->add_data($openid);
        if (!empty($balance)) {
            $objSignature->add_data($balance);
        }
        if (!empty($outer_id)) {
            $objSignature->add_data($outer_id);
        }

        $signature = $objSignature->get_signature();
        $card_ext = array(
            "code" => $code,
            "openid" => $openid,
            "timestamp" => $timestamp,
            "signature" => $signature
        );
        if (!empty($outer_id)) {
            $card_ext["outer_id"] = $outer_id;
        }
        if (!empty($balance)) {
            $card_ext["balance"] = $balance;
        }
        return array(
            'signature' => $signature,
            'timestamp' => $timestamp,
            'card_ext' => json_encode($card_ext)
        );
    }

    /**
     * 领取消息测试接口
     */
    public function getAction()
    {
        // http://www.myapplicationmodule.com/weixincard/index/get
        try {
            // 领取卡券事件推送
            /**
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
            $postStr = '<xml><ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
<FromUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FromUserName>
<CreateTime>1486087470</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[user_get_card]]></Event>
<CardId><![CDATA[p4ELSvyOp16PTtrkxzWw_QybcorA]]></CardId>
<IsGiveByFriend>0</IsGiveByFriend>
<UserCardCode><![CDATA[985522410649]]></UserCardCode>
<FriendUserName><![CDATA[]]></FriendUserName>
<OuterId>1</OuterId>
<OldUserCardCode><![CDATA[]]></OldUserCardCode>
<IsRestoreMemberCard>0</IsRestoreMemberCard>
<IsRecommendByFriend>0</IsRecommendByFriend>
<SourceScene><![CDATA[SOURCE_SCENE_QRCODE]]></SourceScene>
</xml>';

            $datas = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $datas = $this->object2array($datas);

            $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
            $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
            $CreateTime = isset($datas['CreateTime']) ? intval($datas['CreateTime']) : time();
            $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
            $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
            $CardId = isset($datas['CardId']) ? trim($datas['CardId']) : '';
            $IsGiveByFriend = isset($datas['IsGiveByFriend']) ? intval($datas['IsGiveByFriend']) : 0;
            $UserCardCode = isset($datas['UserCardCode']) ? trim($datas['UserCardCode']) : '';
            $FriendUserName = isset($datas['FriendUserName']) ? trim($datas['FriendUserName']) : '';
            $OuterId = isset($datas['OuterId']) ? trim($datas['OuterId']) : '';
            $OldUserCardCode = isset($datas['OldUserCardCode']) ? trim($datas['OldUserCardCode']) : '';
            $IsRestoreMemberCard = isset($datas['IsRestoreMemberCard']) ? intval($datas['IsRestoreMemberCard']) : 0;
            $IsRecommendByFriend = isset($datas['IsRecommendByFriend']) ? intval($datas['IsRecommendByFriend']) : 0;
            $SourceScene = isset($datas['SourceScene']) ? trim($datas['SourceScene']) : '';

            $encrypt_code = isset($datas['encrypt_code']) ? trim($datas['encrypt_code']) : '';
            $new_code = isset($datas['new_code']) ? trim($datas['new_code']) : '';

            // 领取卡券处理
            $this->modelCardBag->userGetCard($CardId, $UserCardCode, $FromUserName, $CreateTime, $IsGiveByFriend, $FriendUserName, $OuterId, $OldUserCardCode, $IsRestoreMemberCard, $IsRecommendByFriend, $SourceScene, $encrypt_code, $new_code, $datas);

            // 增加该卡券的领取数量
            $this->modelCard->incReceivedNum($CardId, 1);

            echo $this->result("OK");
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 领取朋友赠送消息测试接口的卡券
     */
    public function getbyfriendAction()
    {
        // http://www.myapplicationmodule.com/weixincard/index/getbyfriend
        try {
            // 领取卡券事件推送
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
            $postStr = '<xml><ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
<FromUserName><![CDATA[o4ELSv7CChC3YKmM8WKXX4kXSr8c]]></FromUserName>
<CreateTime>1486107437</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[user_get_card]]></Event>
<CardId><![CDATA[p4ELSv5DoUT4SxCgIJ7_tUVRTfx8]]></CardId>
<IsGiveByFriend>1</IsGiveByFriend>
<UserCardCode><![CDATA[072975907291]]></UserCardCode>
<FriendUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FriendUserName>
<OuterId>0</OuterId>
<OldUserCardCode><![CDATA[395079429012]]></OldUserCardCode>
<OutetStr><![CDATA[]]></OutetStr>
</xml>';

            $datas = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $datas = $this->object2array($datas);

            $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
            $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
            $CreateTime = isset($datas['CreateTime']) ? intval($datas['CreateTime']) : time();
            $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
            $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
            $CardId = isset($datas['CardId']) ? trim($datas['CardId']) : '';
            $IsGiveByFriend = isset($datas['IsGiveByFriend']) ? intval($datas['IsGiveByFriend']) : 0;
            $UserCardCode = isset($datas['UserCardCode']) ? trim($datas['UserCardCode']) : '';
            $FriendUserName = isset($datas['FriendUserName']) ? trim($datas['FriendUserName']) : '';
            $OuterId = isset($datas['OuterId']) ? trim($datas['OuterId']) : '';
            $OldUserCardCode = isset($datas['OldUserCardCode']) ? trim($datas['OldUserCardCode']) : '';
            $IsRestoreMemberCard = isset($datas['IsRestoreMemberCard']) ? intval($datas['IsRestoreMemberCard']) : 0;
            $IsRecommendByFriend = isset($datas['IsRecommendByFriend']) ? intval($datas['IsRecommendByFriend']) : 0;
            $SourceScene = isset($datas['SourceScene']) ? trim($datas['SourceScene']) : '';

            $encrypt_code = isset($datas['encrypt_code']) ? trim($datas['encrypt_code']) : '';
            $new_code = isset($datas['new_code']) ? trim($datas['new_code']) : '';

            // 领取卡券处理
            $newCardBag = $this->modelCardBag->userGetCard($CardId, $UserCardCode, $FromUserName, $CreateTime, $IsGiveByFriend, $FriendUserName, $OuterId, $OldUserCardCode, $IsRestoreMemberCard, $IsRecommendByFriend, $SourceScene, $encrypt_code, $new_code, $datas);
            // 增加该卡券的领取数量
            $this->modelCard->incReceivedNum($CardId, 1);

            // 如果是转赠朋友的话
            if ($IsGiveByFriend) {
                // 更新原来的卡包的信息
                $friend_card_bag_id = $newCardBag['_id'];
                $this->modelCardBag->giveCardToFriend($CardId, $OldUserCardCode, $FriendUserName, $CreateTime, $friend_card_bag_id, $datas);
                // 增加转赠朋友数
                $this->modelCard->incGiveByFriendNum($CardId, 1);
            }

            echo $this->result("OK");
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 核销消息测试接口
     */
    public function consumeAction()
    {
        // http://www.myapplicationmodule.com/weixincard/index/consume
        try {
            // 核销卡券事件推送
            /**
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
            $postStr = '<xml><ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
<FromUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FromUserName>
<CreateTime>1486099185</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[user_consume_card]]></Event>
<CardId><![CDATA[p4ELSvyOp16PTtrkxzWw_QybcorA]]></CardId>
<UserCardCode><![CDATA[985522410649]]></UserCardCode>
<ConsumeSource><![CDATA[FROM_API]]></ConsumeSource>
<LocationName><![CDATA[]]></LocationName>
<StaffOpenId><![CDATA[o4ELSv5pqWRhNXqdL3Mp_93-g80s]]></StaffOpenId>
<VerifyCode><![CDATA[]]></VerifyCode>
<RemarkAmount><![CDATA[]]></RemarkAmount>
<OuterStr><![CDATA[]]></OuterStr>
<LocationId>0</LocationId>
</xml>';

            $datas = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $datas = $this->object2array($datas);

            $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
            $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
            $CreateTime = isset($datas['CreateTime']) ? intval($datas['CreateTime']) : time();
            $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
            $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
            $CardId = isset($datas['CardId']) ? trim($datas['CardId']) : '';
            $IsGiveByFriend = isset($datas['IsGiveByFriend']) ? intval($datas['IsGiveByFriend']) : 0;
            $UserCardCode = isset($datas['UserCardCode']) ? trim($datas['UserCardCode']) : '';
            $FriendUserName = isset($datas['FriendUserName']) ? trim($datas['FriendUserName']) : '';
            $OuterId = isset($datas['OuterId']) ? trim($datas['OuterId']) : '';
            $OldUserCardCode = isset($datas['OldUserCardCode']) ? trim($datas['OldUserCardCode']) : '';
            $IsRestoreMemberCard = isset($datas['IsRestoreMemberCard']) ? intval($datas['IsRestoreMemberCard']) : 0;
            $IsRecommendByFriend = isset($datas['IsRecommendByFriend']) ? intval($datas['IsRecommendByFriend']) : 0;
            $SourceScene = isset($datas['SourceScene']) ? trim($datas['SourceScene']) : '';

            $encrypt_code = isset($datas['encrypt_code']) ? trim($datas['encrypt_code']) : '';
            $new_code = isset($datas['new_code']) ? trim($datas['new_code']) : '';

            $ConsumeSource = isset($datas['ConsumeSource']) ? trim($datas['ConsumeSource']) : '';
            $StaffOpenId = isset($datas['StaffOpenId']) ? trim($datas['StaffOpenId']) : '';
            $LocationId = isset($datas['LocationId']) ? trim($datas['LocationId']) : '';
            $LocationName = isset($datas['LocationName']) ? trim($datas['LocationName']) : '';

            // 核销卡券处理
            $this->modelCardBag->userConsumeCard($CardId, $UserCardCode, $FromUserName, $CreateTime, $ConsumeSource, $StaffOpenId, $LocationId, $LocationName, $datas);

            // 增加该卡券的核销数量
            $this->modelCard->incConsumedNum($CardId, 1);

            echo $this->result("OK");
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 删除消息测试接口
     */
    public function deleteAction()
    {
        // http://www.myapplicationmodule.com/weixincard/index/delete
        try {
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
            $postStr = '<xml><ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
<FromUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FromUserName>
<CreateTime>1486105477</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[user_del_card]]></Event>
<CardId><![CDATA[p4ELSv5DoUT4SxCgIJ7_tUVRTfx8]]></CardId>
<UserCardCode><![CDATA[247249135439]]></UserCardCode>
</xml>';

            $datas = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $datas = $this->object2array($datas);

            $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
            $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
            $CreateTime = isset($datas['CreateTime']) ? intval($datas['CreateTime']) : time();
            $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
            $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
            $CardId = isset($datas['CardId']) ? trim($datas['CardId']) : '';
            $IsGiveByFriend = isset($datas['IsGiveByFriend']) ? intval($datas['IsGiveByFriend']) : 0;
            $UserCardCode = isset($datas['UserCardCode']) ? trim($datas['UserCardCode']) : '';
            $FriendUserName = isset($datas['FriendUserName']) ? trim($datas['FriendUserName']) : '';
            $OuterId = isset($datas['OuterId']) ? trim($datas['OuterId']) : '';
            $OldUserCardCode = isset($datas['OldUserCardCode']) ? trim($datas['OldUserCardCode']) : '';
            $IsRestoreMemberCard = isset($datas['IsRestoreMemberCard']) ? intval($datas['IsRestoreMemberCard']) : 0;
            $IsRecommendByFriend = isset($datas['IsRecommendByFriend']) ? intval($datas['IsRecommendByFriend']) : 0;
            $SourceScene = isset($datas['SourceScene']) ? trim($datas['SourceScene']) : '';

            $encrypt_code = isset($datas['encrypt_code']) ? trim($datas['encrypt_code']) : '';
            $new_code = isset($datas['new_code']) ? trim($datas['new_code']) : '';

            $ConsumeSource = isset($datas['ConsumeSource']) ? trim($datas['ConsumeSource']) : '';
            $StaffOpenId = isset($datas['StaffOpenId']) ? trim($datas['StaffOpenId']) : '';
            $LocationId = isset($datas['LocationId']) ? trim($datas['LocationId']) : '';
            $LocationName = isset($datas['LocationName']) ? trim($datas['LocationName']) : '';

            // 删除卡券处理
            $this->modelCardBag->userDelCard($CardId, $UserCardCode, $FromUserName, $CreateTime, $datas);

            // 增加该卡券的删除数量
            $this->modelCard->incDeletedNum($CardId, 1);

            echo $this->result("OK");
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 转化方法 很重要
     *
     * @param object $object            
     */
    public function object2array($object)
    {
        return @json_decode(preg_replace('/{}/', '""', @json_encode($object)), 1);
    }
}
