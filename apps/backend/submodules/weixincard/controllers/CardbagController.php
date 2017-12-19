<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CardBag;
use App\Backend\Submodules\Weixincard\Models\Card;
use function GuzzleHttp\json_encode;

/**
 * @title({name="卡包管理"})
 *
 * @name 卡包管理
 */
class CardbagController extends \App\Backend\Controllers\FormController
{

    private $modelCardBag;

    private $modelCard;

    public function initialize()
    {
        $this->modelCardBag = new CardBag();
        $this->modelCard = new Card();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['card_id'] = array(
            'name' => '微信卡券',
            'data' => array(
                'type' => 'string',
                'length' => '32'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelCard->getAllWithCardId();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'card_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['UserCardCode'] = array(
            'name' => '自定义码',
            'data' => array(
                'type' => 'string',
                'length' => '20'
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
        
        $schemas['FromUserName'] = array(
            'name' => '领券方帐号',
            'data' => array(
                'type' => 'string',
                'length' => '32'
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
        
        $schemas['IsGiveByFriend'] = array(
            'name' => '是否转赠',
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
        
        $schemas['FriendUserName'] = array(
            'name' => '赠送方账号',
            'data' => array(
                'type' => 'string',
                'length' => '32'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['OldUserCardCode'] = array(
            'name' => '转赠前的code序列号',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['IsRestoreMemberCard'] = array(
            'name' => '是否是会员卡',
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
        
        $schemas['IsRecommendByFriend'] = array(
            'name' => '是否为朋友推荐',
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
        
        $schemas['SourceScene'] = array(
            'name' => '来源场景',
            'data' => array(
                'type' => 'string',
                'length' => '30'
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
        
        $schemas['encrypt_code'] = array(
            'name' => '编码code',
            'data' => array(
                'type' => 'string',
                'length' => '128'
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
        
        $schemas['new_code'] = array(
            'name' => '新的卡券code编码',
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
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['is_got'] = array(
            'name' => '是否已领券',
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
        
        $schemas['got_time'] = array(
            'name' => '领券时间',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_consumed'] = array(
            'name' => '是否已核销',
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
        
        $schemas['consume_time'] = array(
            'name' => '核销时间',
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
        
        $schemas['ConsumeSource'] = array(
            'name' => '核销来源',
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
        
        $schemas['StaffOpenId'] = array(
            'name' => '核销员的openid',
            'data' => array(
                'type' => 'string',
                'length' => '32'
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
        
        $schemas['LocationId'] = array(
            'name' => '核销的门店ID',
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
        
        $schemas['LocationName'] = array(
            'name' => '核销的门店名称',
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
        
        $schemas['is_deleted'] = array(
            'name' => '是否删除',
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
        
        $schemas['delete_time'] = array(
            'name' => '删除时间',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_unavailable'] = array(
            'name' => '是否已失效',
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
        
        $schemas['unavailable_time'] = array(
            'name' => '失效时间',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_give_to_friend'] = array(
            'name' => '是否已赠予朋友',
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
        
        $schemas['give_to_friend_time'] = array(
            'name' => '赠予朋友时间',
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
        
        $schemas['friend_card_bag_id'] = array(
            'name' => '朋友卡包ID',
            'data' => array(
                'type' => 'string',
                'length' => '24'
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
        
        $schemas['OuterId'] = array(
            'name' => '场景值',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
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
        return '卡包';
    }

    protected function getModel()
    {
        return $this->modelCardBag;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $cardList = $this->modelCard->getAllWithCardId();
        foreach ($list['data'] as &$item) {
            $item['card_name'] = isset($cardList[$item['card_id']]) ? $cardList[$item['card_id']] : "--";
            $item['got_time'] = date("Y-m-d H:i:s", $item['got_time']->sec);
            $item['consume_time'] = date("Y-m-d H:i:s", $item['consume_time']->sec);
            $item['delete_time'] = date("Y-m-d H:i:s", $item['delete_time']->sec);
            $item['unavailable_time'] = date("Y-m-d H:i:s", $item['unavailable_time']->sec);
            if (! empty($item['UserCardCode'])) {
                if (empty($item['is_consumed']) && empty($item['is_deleted'])) {
                    $item['UserCardCode'] = $item['UserCardCode'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要从微信公众平台上查询卡券code信息吗？\', \'getcode\')" class="halflings-icon user white"><i></i> 查询</a>';
                    $item['UserCardCode'] = $item['UserCardCode'] . '&nbsp&nbsp<br/><a href="javascript:;" class="btn yellow icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要进行卡券核销吗？\', \'consumecode\')" class="halflings-icon user white"><i></i> 核销</a>';
                    // $item['UserCardCode'] = $item['UserCardCode'] . '&nbsp&nbsp<a href="javascript:;" class="btn red icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要进行设置卡券失效吗？\', \'unavailablecode\')" class="halflings-icon user white"><i></i> 设置失效code</a>';
                }
            }
        }
        
        return $list;
    }

    /**
     * @title({name="查询code"})
     *
     * @name 查询code
     */
    public function getcodeAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/cardbag/getcode?id=58940256887c2223368b456e
        try {
            $this->view->disable();
            
            $weixin = $this->getWeixin();
            
            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", - 1);
            }
            
            $key = "getcode_{$id}";
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                throw new \Exception("上次操作还未完成,请等待", - 99);
            }
            
            $cardbagInfo = $this->modelCardBag->getInfoById($id);
            if (empty($cardbagInfo)) {
                throw new \Exception("该券不存在", - 2);
            }
            
            // 调用微信查询code接口
            $info = $weixin->getCardManager()->codeGet(trim($cardbagInfo['UserCardCode']), trim($cardbagInfo['card_id']));
            // Array ( [errcode] => 0 [errmsg] => ok [card] => Array ( [card_id] => p4ELSvyOp16PTtrkxzWw_QybcorA [begin_time] => 1486051200 [end_time] => 1488643199 [code] => 985522410649 ) [openid] => o4ELSvz-B4_DThF0Vpfrverk3IpY [can_consume] => 1 [user_card_status] => NORMAL )
            if (! empty($info['errcode'])) {
                throw new \Exception($info['errmsg'], $info['errcode']);
            }
            $this->makeJsonResult(json_encode($info), 'OK');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="消耗code"})
     *
     * 卡券核销部分
     *
     * @name 消耗code
     */
    public function consumecodeAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/cardbag/consumecode?id=58940256887c2223368b456e
        try {
            $this->view->disable();
            
            $weixin = $this->getWeixin();
            
            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", - 1);
            }
            
            $key = "consumecode_{$id}";
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                throw new \Exception("上次操作还未完成,请等待", - 99);
            }
            
            $cardbagInfo = $this->modelCardBag->getInfoById($id);
            if (empty($cardbagInfo)) {
                throw new \Exception("该券不存在", - 2);
            }
            
            if ($cardbagInfo['is_consumed']) {
                throw new \Exception("该券已经核销过了", - 3);
            }
            
            // 调用微信核销接口
            $info = $weixin->getCardManager()->codeConsume(trim($cardbagInfo['UserCardCode']), trim($cardbagInfo['card_id']));
            // Array ( [errcode] => 0 [errmsg] => ok [card] => Array ( [card_id] => p4ELSvyOp16PTtrkxzWw_QybcorA ) [openid] => o4ELSvz-B4_DThF0Vpfrverk3IpY )
            if (! empty($info['errcode'])) {
                throw new \Exception($info['errmsg'], $info['errcode']);
            }
            $this->makeJsonResult(json_encode($info), 'OK');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="设置卡券失效"})
     *
     * @name 设置卡券失效
     *      
     *       设置卡券失效
     *       为满足改票、退款等异常情况，可调用卡券失效接口将用户的卡券设置为失效状态。
     *       注：设置卡券失效的操作不可逆，即无法将设置为失效的卡券调回有效状态，商家须慎重调用该接口
     */
    public function unavailablecodeAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/cardbag/unavailablecode?id=58940256887c2223368b456e
        try {
            $this->view->disable();
            
            $weixin = $this->getWeixin();
            
            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", - 1);
            }
            
            $key = "unavailablecode_{$id}";
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                throw new \Exception("上次操作还未完成,请等待", - 99);
            }
            
            $cardbagInfo = $this->modelCardBag->getInfoById($id);
            if (empty($cardbagInfo)) {
                throw new \Exception("该券不存在", - 2);
            }
            
            if ($cardbagInfo['is_unavailable']) {
                throw new \Exception("该券已经失效了", - 3);
            }
            
            // 调用微信设置卡券失效接口
            $info = $weixin->getCardManager()->codeUnavailable(trim($cardbagInfo['UserCardCode']), trim($cardbagInfo['card_id']));
            // Array( [errcode] => 0 [errmsg] => ok [card] => Array ( [card_id] => pgW8rt5vzjJ7nFLYxskYGBtxZP3k ) [openid] => ogW8rt97N2ojFzIc_1N3pknIKnoU)
            if (! empty($info['errcode'])) {
                throw new \Exception($info['errmsg'], $info['errcode']);
            }
            
            print_r($info);
            die('');
            
            $this->makeJsonResult(json_encode($info), 'OK');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}