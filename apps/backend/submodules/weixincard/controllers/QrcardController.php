<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\Qrcard;
use App\Backend\Submodules\Weixincard\Models\Card;

/**
 * @title({name="卡券二维码"})
 *
 * @name 卡券二维码
 */
class QrcardController extends \App\Backend\Controllers\FormController
{

    private $modelQrcard;

    private $modelCard;

    public function initialize()
    {
        $this->modelQrcard = new Qrcard();
        $this->modelCard = new Card();
        $this->cardList = $this->modelCard->getAllWithCardId();
        parent::initialize();
    }

    private $cardList = null;

    protected function getFormTools2($tools)
    {
        $tools['create'] = array(
            'title' => '生成卡券二维码',
            'action' => 'create',
            'is_show' => function ($item) {
                // 没有数据
                if (empty($item)) {
                    return false;
                } else {
                    if (empty($item['expire_seconds']) && !empty($item['is_created'])) { // 如果是永久并且已生成的话
                        return false;
                    }
                    if (!empty($item['expire_seconds']) && !empty($item['is_created']) && ($item['ticket_time']->sec + $item['expire_seconds']) > (time())) { // 如果是临时并且已生成并且没有过期
                        return false;
                    }
                }
                return true;
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
        $schemas['code'] = array(
            'name' => '卡券Code码',
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
                'help' => '卡券Code码,use_custom_code字段为true的卡券必须填写，非自定义code不必填写。',
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
        $schemas['openid'] = array(
            'name' => '领取者的openid',
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
                'items' => '',
                'help' => '指定领取者的openid，只有该用户能领取。bind_openid字段为true的卡券必须填写，非指定openid不必填写。',
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
        $schemas['expire_seconds'] = array(
            'name' => '二维码的有效时间',
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
                'help' => '指定二维码的有效时间，范围是60 ~ 1800秒。不填默认为永久有效。',
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
        $schemas['is_unique_code'] = array(
            'name' => '是否随机分配一个code',
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
                'help' => '指定下发二维码，生成的二维码随机分配一个code，领取后不可再次扫描。填写true或false。默认false，注意填写该字段时，卡券须通过审核且库存不为0。',
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
        $schemas['balance'] = array(
            'name' => '红包余额(以分为单位)',
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
        $schemas['outer_id'] = array(
            'name' => '领取场景值',
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
                'help' => '领取场景值，用于领取渠道的数据统计，默认值为0，字段类型为整型，长度限制为60位数字。用户领取卡券后触发的事件推送中会带上此自定义场景值。',
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
        $schemas['ticket'] = array(
            'name' => '获取的二维码ticket',
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
                'help' => '获取的二维码ticket，凭借此ticket调用通过ticket换取二维码接口可以在有效时间内换取二维码。',
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
        $schemas['url'] = array(
            'name' => '二维码图片解析后的地址',
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
                'help' => '二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片',
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
        $schemas['qrcodeUrl'] = array(
            'name' => '通过ticket换取二维码',
            'data' => array(
                'type' => 'string',
                'length' => 200,
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
        $schemas['show_qrcode_url'] = array(
            'name' => '二维码显示地址',
            'data' => array(
                'type' => 'string',
                'length' => 200,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '二维码显示地址，点击后跳转二维码页面',
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
        $schemas['is_created'] = array(
            'name' => '是否已生成',
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
        $schemas['ticket_time'] = array(
            'name' => '生成时间',
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
        return '卡券二维码';
    }

    protected function getModel()
    {
        return $this->modelQrcard;
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
                if (empty($card['expire_seconds']) && !empty($card['is_created'])) { // 如果是永久并且已生成的话
                    continue;
                }
                if (!empty($card['expire_seconds']) && !empty($card['is_created']) && ($card['ticket_time']->sec + $card['expire_seconds']) > (time())) { // 如果是临时并且已生成并且没有过期
                    continue;
                }

                $card_id = $card['card_id'];
                $cardInfo = $this->modelCard->getInfoByCardId($card_id);
                if (empty($cardInfo)) {
                    throw new \Exception("卡券ID为{$card_id}的数据不存在", -1);
                }

                // 指定二维码的有效时间，范围是60 ~ 1800秒。不填默认为永久有效
                if (!empty($card['expire_seconds']) && ($card['expire_seconds'] < 60 || $card['expire_seconds'] > 1800)) {
                    throw new \Exception("指定二维码的有效时间，范围是60 ~ 1800秒", -4);
                }

                // 卡券Code码,use_custom_code字段为true的卡券必须填写，非自定义code不必填写。
                if (!empty($cardInfo['use_custom_code']) && empty($card['code'])) {
                    throw new \Exception("use_custom_code字段为true的卡券必须填写卡券Code码", -2);
                }

                // 指定领取者的openid，只有该用户能领取。bind_openid字段为true的卡券必须填写，非指定openid不必填写。
                if (!empty($cardInfo['bind_openid']) && empty($card['openid'])) {
                    throw new \Exception("bind_openid字段为true的卡券必须填写必须指定领取者的openid", -3);
                }

                // 注意填写该字段时，卡券须通过审核且库存不为0。
                if (!empty($card['is_unique_code']) && !($cardInfo['sku_quantity'] > 0 && $cardInfo['status'] == 'CARD_STATUS_VERIFY_OK')) {
                    throw new \Exception("卡券须通过审核且库存不为0", -5);
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
            return $this->makeJsonResult(array('then' => array('action' => 'refresh')), '已成功生成卡券二维码');
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
                if (empty($card['expire_seconds']) && !empty($card['is_created'])) { // 如果是永久并且已生成的话
                    continue;
                }
                if (!empty($card['expire_seconds']) && !empty($card['is_created']) && ($card['ticket_time']->sec + $card['expire_seconds']) > (time())) { // 如果是临时并且已生成并且没有过期
                    continue;
                }

                $card_id = $card['card_id'];
                $cardInfo = $this->modelCard->getInfoByCardId($card_id);
                if (empty($cardInfo)) {
                    throw new \Exception("卡券ID为{$card_id}的数据不存在", -1);
                }

                // 指定二维码的有效时间，范围是60 ~ 1800秒。不填默认为永久有效
                if (!empty($card['expire_seconds']) && ($card['expire_seconds'] < 60 || $card['expire_seconds'] > 1800)) {
                    throw new \Exception("指定二维码的有效时间，范围是60 ~ 1800秒", -4);
                }

                // 卡券Code码,use_custom_code字段为true的卡券必须填写，非自定义code不必填写。
                if (!empty($cardInfo['use_custom_code']) && empty($card['code'])) {
                    throw new \Exception("use_custom_code字段为true的卡券必须填写卡券Code码", -2);
                }

                // 指定领取者的openid，只有该用户能领取。bind_openid字段为true的卡券必须填写，非指定openid不必填写。
                if (!empty($cardInfo['bind_openid']) && empty($card['openid'])) {
                    throw new \Exception("bind_openid字段为true的卡券必须填写必须指定领取者的openid", -3);
                }

                // 注意填写该字段时，卡券须通过审核且库存不为0。
                if (!empty($card['is_unique_code']) && !($cardInfo['sku_quantity'] > 0 && $cardInfo['status'] == 'CARD_STATUS_VERIFY_OK')) {
                    throw new \Exception("卡券须通过审核且库存不为0", -5);
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
