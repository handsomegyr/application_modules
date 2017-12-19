<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Qrcard;
use App\Backend\Submodules\Weixincard\Models\Card;

/**
 * @title({name="卡券二维码管理"})
 *
 * @name 卡券二维码管理
 */
class QrcardController extends \App\Backend\Controllers\FormController
{

    private $modelQrcard;

    private $modelCard;

    public function initialize()
    {
        $this->modelQrcard = new Qrcard();
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
        
        $schemas['code'] = array(
            'name' => '自定义码',
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
        
        $schemas['openid'] = array(
            'name' => '领取者openid',
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
        
        $schemas['expire_seconds'] = array(
            'name' => '有效时间',
            'data' => array(
                'type' => 'integer',
                'length' => '10',
                'defaultValue' => 1800
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['is_unique_code'] = array(
            'name' => '是否指定下发二维码',
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
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['balance'] = array(
            'name' => '红包余额',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['outer_id'] = array(
            'name' => '场景值',
            'data' => array(
                'type' => 'integer',
                'length' => '10',
                'defaultValue' => 1
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['ticket'] = array(
            'name' => '二维码ticket',
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
        
        $schemas['url'] = array(
            'name' => '二维码图片解析地址',
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
        
        $schemas['qrcodeUrl'] = array(
            'name' => '通过ticket换取二维码',
            'data' => array(
                'type' => 'string',
                'length' => '128'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['show_qrcode_url'] = array(
            'name' => '二维码显示地址',
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
        
        $schemas['is_created'] = array(
            'name' => '是否已生成',
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
        
        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);
        
        $schemas['ticket_time'] = array(
            'name' => '生成时间',
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
        return '卡券二维码';
    }

    protected function getModel()
    {
        return $this->modelQrcard;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $cardList = $this->modelCard->getAllWithCardId();
        foreach ($list['data'] as &$item) {
            $item['card_name'] = isset($cardList[$item['card_id']]) ? $cardList[$item['card_id']] : "--";
            if (isset($cardList[$item['card_id']])) {
                $isCanCreate = true;
                if ($isCanCreate && empty($item['expire_seconds']) && ! empty($item['is_created'])) { // 如果是永久并且已生成的话
                    $isCanCreate = false;
                }
                if ($isCanCreate && ! empty($item['expire_seconds']) && ! empty($item['is_created']) && ($item['ticket_time']->sec + $item['expire_seconds']) > (time())) { // 如果是临时并且已生成并且没有过期
                    $isCanCreate = false;
                }
                if ($isCanCreate) {
                    $item['card_name'] = $item['card_name'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要在微信公众平台上生成卡券二维码吗？\', \'create\')" class="halflings-icon user white"><i></i> 创建</a>';
                }
            }
            $item['ticket_time'] = date("Y-m-d H:i:s", $item['ticket_time']->sec);
        }
        
        return $list;
    }

    /**
     * @title({name="生成卡券二维码"})
     * 生成卡券二维码的Hook
     *
     * @name 生成卡券二维码
     */
    public function createAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/qrcard/create?id=xx
        try {
            $this->view->disable();
            $weixin = $this->getWeixin();
            $this->modelQrcard->setWeixin($weixin);
            $id = $this->get('id', '');
            if (empty($id)) {
                $cards = $this->modelQrcard->getAll();
            } else {
                $cardInfo = $this->modelQrcard->getInfoById($id);
                $cards = array(
                    $cardInfo
                );
            }
            
            foreach ($cards as $card) {
                if (empty($card['expire_seconds']) && ! empty($card['is_created'])) { // 如果是永久并且已生成的话
                    continue;
                }
                if (! empty($card['expire_seconds']) && ! empty($card['is_created']) && ($card['ticket_time']->sec + $card['expire_seconds']) > (time())) { // 如果是临时并且已生成并且没有过期
                    continue;
                }
                
                $card_id = $card['card_id'];
                $cardInfo = $this->modelCard->getInfoByCardId($card_id);
                if (empty($cardInfo)) {
                    throw new \Exception("卡券ID为{$card_id}的数据不存在", - 1);
                }
                
                // 指定二维码的有效时间，范围是60 ~ 1800秒。不填默认为永久有效
                if (! empty($card['expire_seconds']) && ($card['expire_seconds'] < 60 || $card['expire_seconds'] > 1800)) {
                    throw new \Exception("指定二维码的有效时间，范围是60 ~ 1800秒", - 4);
                }
                
                // 卡券Code码,use_custom_code字段为true的卡券必须填写，非自定义code不必填写。
                if (! empty($cardInfo['use_custom_code']) && empty($card['code'])) {
                    throw new \Exception("use_custom_code字段为true的卡券必须填写卡券Code码", - 2);
                }
                
                // 指定领取者的openid，只有该用户能领取。bind_openid字段为true的卡券必须填写，非指定openid不必填写。
                if (! empty($cardInfo['bind_openid']) && empty($card['openid'])) {
                    throw new \Exception("bind_openid字段为true的卡券必须填写必须指定领取者的openid", - 3);
                }
                
                // 注意填写该字段时，卡券须通过审核且库存不为0。
                if (! empty($card['is_unique_code']) && ! ($cardInfo['sku_quantity'] > 0 && $cardInfo['status'] == 'CARD_STATUS_VERIFY_OK')) {
                    throw new \Exception("卡券须通过审核且库存不为0", - 5);
                }
                
                // 在微信公众平台生成相应的二维码
                $this->modelQrcard->create4Weixin($card);
                
                // // 在卡包里面记录信息
                // $memo = array(
                // 'card_type' => $cardInfo['card_type'],
                // 'iscreatedByQrcard' => 1,
                // 'balance' => $balance,
                // 'card_record_id' => $cardInfo['_id']
                // );
                // $cardbagInfo = $this->_cardBag->addCard($card_id, $code, $openid, $outer_id, $memo);
            }
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="生成多张卡券二维码"})
     * 生成多张卡券二维码的Hook
     *
     * @name 生成多张卡券二维码
     */
    public function createmultipleAction()
    {
        // http://www.applicationmodule.com/admin/weixincard/qrcard/createmultiple?id=xx
        try {
            $this->view->disable();
            $weixin = $this->getWeixin();
            $this->modelQrcard->setWeixin($weixin);
            $id = $this->get('id', '');
            if (empty($id)) {
                $cards = $this->modelQrcard->getAll();
            } else {
                $cardInfo = $this->modelQrcard->getInfoById($id);
                $cards = array(
                    $cardInfo
                );
            }
            
            foreach ($cards as $card) {
                if (empty($card['expire_seconds']) && ! empty($card['is_created'])) { // 如果是永久并且已生成的话
                    continue;
                }
                if (! empty($card['expire_seconds']) && ! empty($card['is_created']) && ($card['ticket_time']->sec + $card['expire_seconds']) > (time())) { // 如果是临时并且已生成并且没有过期
                    continue;
                }
                
                $card_id = $card['card_id'];
                $cardInfo = $this->modelCard->getInfoByCardId($card_id);
                if (empty($cardInfo)) {
                    throw new \Exception("卡券ID为{$card_id}的数据不存在", - 1);
                }
                
                // 指定二维码的有效时间，范围是60 ~ 1800秒。不填默认为永久有效
                if (! empty($card['expire_seconds']) && ($card['expire_seconds'] < 60 || $card['expire_seconds'] > 1800)) {
                    throw new \Exception("指定二维码的有效时间，范围是60 ~ 1800秒", - 4);
                }
                
                // 卡券Code码,use_custom_code字段为true的卡券必须填写，非自定义code不必填写。
                if (! empty($cardInfo['use_custom_code']) && empty($card['code'])) {
                    throw new \Exception("use_custom_code字段为true的卡券必须填写卡券Code码", - 2);
                }
                
                // 指定领取者的openid，只有该用户能领取。bind_openid字段为true的卡券必须填写，非指定openid不必填写。
                if (! empty($cardInfo['bind_openid']) && empty($card['openid'])) {
                    throw new \Exception("bind_openid字段为true的卡券必须填写必须指定领取者的openid", - 3);
                }
                
                // 注意填写该字段时，卡券须通过审核且库存不为0。
                if (! empty($card['is_unique_code']) && ! ($cardInfo['sku_quantity'] > 0 && $cardInfo['status'] == 'CARD_STATUS_VERIFY_OK')) {
                    throw new \Exception("卡券须通过审核且库存不为0", - 5);
                }
                
                // 在微信公众平台生成相应的二维码
                $this->modelQrcard->create4Weixin($card);
                
                // // 在卡包里面记录信息
                // $memo = array(
                // 'card_type' => $cardInfo['card_type'],
                // 'iscreatedByQrcard' => 1,
                // 'balance' => $balance,
                // 'card_record_id' => $cardInfo['_id']
                // );
                // $cardbagInfo = $this->_cardBag->addCard($card_id, $code, $openid, $outer_id, $memo);
            }
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }
}