<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CardBag;
use App\Backend\Submodules\Weixincard\Models\Card;

/**
 * @title({name="卡包"})
 *
 * @name 卡包
 */
class CardbagController extends \App\Backend\Controllers\FormController
{

    private $modelCardBag;

    private $modelCard;

    public function initialize()
    {
        $this->modelCardBag = new CardBag();
        $this->modelCard = new Card();
        $this->cardList = $this->modelCard->getAllWithCardId();
        parent::initialize();
    }

    private $cardList = null;

    protected function getFormTools2($tools)
    {
        $tools['getcode'] = array(
            'title' => '查询code',
            'action' => 'getcode',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['UserCardCode'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['consumecode'] = array(
            'title' => '消耗code',
            'action' => 'consumecode',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['UserCardCode']) && empty($item['is_consumed']) && empty($item['is_deleted'])) {
                        return true;
                    }
                    return false;
                }
            },
            'icon' => 'fa-pencil-square-o',
        );
        $tools['unavailablecode'] = array(
            'title' => '设置卡券失效',
            'action' => 'unavailablecode',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (!empty($item['UserCardCode']) && empty($item['is_unavailable']) && empty($item['is_deleted'])) {
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
        $schemas['card_id'] = array(
            'name' => '微信卡券ID',
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
                'items' => $this->cardList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->cardList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->cardList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['UserCardCode'] = array(
            'name' => 'code序列号',
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
        $schemas['FromUserName'] = array(
            'name' => '领券方帐号',
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
        $schemas['is_got'] = array(
            'name' => '用户是否领券',
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
        $schemas['got_time'] = array(
            'name' => '领券时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
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
        $schemas['IsGiveByFriend'] = array(
            'name' => '是否为转赠',
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
                'help' => '是否为转赠，1 代表是，0 代表否。',
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
        $schemas['FriendUserName'] = array(
            'name' => '赠送方账号',
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
        $schemas['OldUserCardCode'] = array(
            'name' => '转赠前的code序列号',
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
        $schemas['IsRestoreMemberCard'] = array(
            'name' => '是否是会员卡',
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
        $schemas['IsRecommendByFriend'] = array(
            'name' => '是否为朋友推荐',
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
        $schemas['SourceScene'] = array(
            'name' => '来源场景',
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
        $schemas['encrypt_code'] = array(
            'name' => '编码code',
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
        $schemas['new_code'] = array(
            'name' => '新的卡券code编码',
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
        $schemas['is_consumed'] = array(
            'name' => '是否已核销',
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
        $schemas['StaffOpenId'] = array(
            'name' => '核销该卡券核销员的openid',
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
                'help' => '核销该卡券核销员的openid（只有通过卡券商户助手核销时才会出现）',
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
        $schemas['consume_time'] = array(
            'name' => '核销时间',
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
        $schemas['ConsumeSource'] = array(
            'name' => '核销来源',
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
                'help' => '核销来源。支持开发者统计API核销（FROM_API）、公众平台核销（FROM_MP）、卡券商户助手核销（FROM_MOBILE_HELPER）（核销员微信号）',
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
        $schemas['LocationId'] = array(
            'name' => '门店ID',
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
                'help' => '门店ID，当前卡券核销的门店名称（只有通过卡券商户助手和买单核销时才会出现）',
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
        $schemas['LocationName'] = array(
            'name' => '门店名称',
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
                'items' => '',
                'help' => '门店名称，当前卡券核销的门店名称（只有通过卡券商户助手和买单核销时才会出现）',
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
        $schemas['is_deleted'] = array(
            'name' => '是否删除',
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
        $schemas['delete_time'] = array(
            'name' => '删除时间',
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
        $schemas['is_unavailable'] = array(
            'name' => '是否已失效',
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
        $schemas['unavailable_time'] = array(
            'name' => '失效时间',
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
        $schemas['is_give_to_friend'] = array(
            'name' => '是否已赠予朋友',
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
        $schemas['give_to_friend_time'] = array(
            'name' => '赠予朋友时间',
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
        $schemas['friend_card_bag_id'] = array(
            'name' => '朋友卡包ID',
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
        $schemas['OuterId'] = array(
            'name' => '场景参数值',
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
        return '卡包';
    }

    protected function getModel()
    {
        return $this->modelCardBag;
    }

    /**
     * @title({name="查询code"})
     *
     * @name 查询code
     */
    public function getcodeAction()
    {
        // http://www.myapplicationmodule.com.com/admin/weixincard/cardbag/getcode?id=58940256887c2223368b456e
        try {
            

            $weixin = $this->getWeixin();

            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", -1);
            }

            $key = "getcode_{$id}";
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                throw new \Exception("上次操作还未完成,请等待", -99);
            }

            $cardbagInfo = $this->modelCardBag->getInfoById($id);
            if (empty($cardbagInfo)) {
                throw new \Exception("该券不存在", -2);
            }

            // 调用微信查询code接口
            $info = $weixin->getCardManager()->codeGet(trim($cardbagInfo['UserCardCode']), trim($cardbagInfo['card_id']));
            // Array ( [errcode] => 0 [errmsg] => ok [card] => Array ( [card_id] => p4ELSvyOp16PTtrkxzWw_QybcorA [begin_time] => 1486051200 [end_time] => 1488643199 [code] => 985522410649 ) [openid] => o4ELSvz-B4_DThF0Vpfrverk3IpY [can_consume] => 1 [user_card_status] => NORMAL )
            if (!empty($info['errcode'])) {
                throw new \Exception($info['errmsg'], $info['errcode']);
            }
            // $this->makeJsonResult(json_encode($info), 'OK');
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功查询code:' . \json_encode($info));
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
        // http://www.myapplicationmodule.com.com/admin/weixincard/cardbag/consumecode?id=58940256887c2223368b456e
        try {
            

            $weixin = $this->getWeixin();

            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", -1);
            }

            $key = "consumecode_{$id}";
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                throw new \Exception("上次操作还未完成,请等待", -99);
            }

            $cardbagInfo = $this->modelCardBag->getInfoById($id);
            if (empty($cardbagInfo)) {
                throw new \Exception("该券不存在", -2);
            }

            if ($cardbagInfo['is_consumed']) {
                throw new \Exception("该券已经核销过了", -3);
            }

            // 调用微信核销接口
            $info = $weixin->getCardManager()->codeConsume(trim($cardbagInfo['UserCardCode']), trim($cardbagInfo['card_id']));
            // Array ( [errcode] => 0 [errmsg] => ok [card] => Array ( [card_id] => p4ELSvyOp16PTtrkxzWw_QybcorA ) [openid] => o4ELSvz-B4_DThF0Vpfrverk3IpY )
            if (!empty($info['errcode'])) {
                throw new \Exception($info['errmsg'], $info['errcode']);
            }
            // $this->makeJsonResult(json_encode($info), 'OK');
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功核销code:' . \json_encode($info));
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
        // http://www.myapplicationmodule.com.com/admin/weixincard/cardbag/unavailablecode?id=58940256887c2223368b456e
        try {
            

            $weixin = $this->getWeixin();

            $id = $this->get('id', '');
            if (empty($id)) {
                throw new \Exception("id未指定", -1);
            }

            $key = "unavailablecode_{$id}";
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                throw new \Exception("上次操作还未完成,请等待", -99);
            }

            $cardbagInfo = $this->modelCardBag->getInfoById($id);
            if (empty($cardbagInfo)) {
                throw new \Exception("该券不存在", -2);
            }

            if ($cardbagInfo['is_unavailable']) {
                throw new \Exception("该券已经失效了", -3);
            }

            // 调用微信设置卡券失效接口
            $info = $weixin->getCardManager()->codeUnavailable(trim($cardbagInfo['UserCardCode']), trim($cardbagInfo['card_id']));
            // Array( [errcode] => 0 [errmsg] => ok [card] => Array ( [card_id] => pgW8rt5vzjJ7nFLYxskYGBtxZP3k ) [openid] => ogW8rt97N2ojFzIc_1N3pknIKnoU)
            if (!empty($info['errcode'])) {
                throw new \Exception($info['errmsg'], $info['errcode']);
            }

            // print_r($info);
            // die('');
            // $this->makeJsonResult(json_encode($info), 'OK');
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功设置卡券失效:' . \json_encode($info));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}
