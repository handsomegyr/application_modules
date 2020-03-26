<?php
namespace App\Backend\Submodules\Order\Controllers;

use App\Backend\Submodules\Order\Models\Goods;

/**
 * @title({name="订单商品管理"})
 *
 * @name 订单商品管理
 */
class GoodsController extends \App\Backend\Controllers\FormController
{

    private $saleStateDatas = \App\Order\Models\Goods::SALESTATEDATAS;

    private $orderStateDatas = \App\Order\Models\Goods::ORDERSTATEDATAS;

    private $modelOrderGoods = NULL;

    public function initialize()
    {
        $this->modelOrderGoods = new Goods();
        $this->modelGoods = new \App\Common\Models\Goods\Goods();
        $this->modelMember = new \App\Common\Models\Member\Member();
        parent::initialize();
    }

    /**
     * @title({name="发货"})
     *
     * @name 发货
     */
    public function deliveryorderAction()
    {
        try {
            $this->view->disable();
            
            $input = $this->getFilterInput();
            
            if ($input->isValid("id")) {} else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            $express_no = $this->get('express_no', '');
            if (empty($express_no)) {
                throw new \Exception('快递公司未选择');
            }
            $delivery_sn = $this->get('delivery_sn', '');
            if (empty($delivery_sn)) {
                throw new \Exception('发货单号未填写');
            }
            $orderGoodsInfo = $this->modelOrderGoods->getInfoById($input->id);
            if (empty($orderGoodsInfo)) {
                throw new \Exception('id不正确');
            }
            if ($orderGoodsInfo['order_state'] != \App\Common\Models\Order\Goods::ORDER_STATE2) { // 待发货
                throw new \Exception('该订单不是待发货的订单');
            }
            $this->doDeliveryOrder($orderGoodsInfo['order_no'], $express_no, $delivery_sn);
            $this->makeJsonResult();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {        $schemas['_id']['list']['is_show'] = false;
        $schemas['goods_id'] = array(
            'name' => '商品ID',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true,
                'name' => '云购商品信息'
            ),
            'search' => array(
                'is_show' => true
            )
        );
        $schemas['goods_name'] = array(
            'name' => '商品名',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
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
        $schemas['goods_price'] = array(
            'name' => '单价',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_num'] = array(
            'name' => 'goods_num',
            'data' => array(
                'type' => 'integer',
                'length' => 5
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_image'] = array(
            'name' => '商品主图',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelGoods->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_pay_price'] = array(
            'name' => 'goods_pay_price',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['buyer_id'] = array(
            'name' => '买家ID',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true,
                'name' => '买家信息'
            ),
            'search' => array(
                'is_show' => true
            )
        );
        $schemas['buyer_name'] = array(
            'name' => 'buyer_name',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        $schemas['buyer_mobile'] = array(
            'name' => 'buyer_mobile',
            'data' => array(
                'type' => 'string',
                'length' => 20
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
        $schemas['buyer_email'] = array(
            'name' => 'buyer_email',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        $schemas['buyer_avatar'] = array(
            'name' => '用户头像',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelMember->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['buyer_register_by'] = array(
            'name' => 'buyer_register_by',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['buyer_ip'] = array(
            'name' => '买家IP',
            'data' => array(
                'type' => 'string',
                'length' => 15
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
        $schemas['goods_type'] = array(
            'name' => 'goods_type',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['promotions_id'] = array(
            'name' => 'promotions_id',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        $schemas['commis_rate'] = array(
            'name' => 'commis_rate',
            'data' => array(
                'type' => 'integer',
                'length' => 5
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id'] = array(
            'name' => 'gc_id',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        $schemas['goods_commonid'] = array(
            'name' => '商品ID',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        $schemas['goods_period'] = array(
            'name' => '云',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_total_person_time'] = array(
            'name' => '总参与次数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_remain_person_time'] = array(
            'name' => 'goods_remain_person_time',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['is_success'] = array(
            'name' => 'is_success',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['failure_num'] = array(
            'name' => '云购失败次数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['lottery_prize_id'] = array(
            'name' => 'lottery_prize_id',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        $schemas['lottery_code'] = array(
            'name' => '云购码',
            'data' => array(
                'type' => 'string',
                'length' => 0
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true,
                'name' => '云购信息'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['purchase_num'] = array(
            'name' => '云购次数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['purchase_time'] = array(
            'name' => '云购时间',
            'data' => array(
                'type' => 'decimal',
                'length' => 13
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'currency',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => true
            )
        );
        $schemas['prize_code'] = array(
            'name' => '幸运云购码',
            'data' => array(
                'type' => 'integer',
                'length' => 11
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => true
            )
        );
        $schemas['prize_time'] = array(
            'name' => '揭晓时间',
            'data' => array(
                'type' => 'decimal',
                'length' => 13
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'currency',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                // 'input_type' => 'datetimepicker',
                'is_show' => true
            )
        );
        $schemas['prize_buyer_id'] = array(
            'name' => 'prize_buyer_id',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        $schemas['prize_buyer_name'] = array(
            'name' => 'prize_buyer_name',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        $schemas['prize_buyer_register_by'] = array(
            'name' => 'prize_buyer_register_by',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['prize_order_goods_id'] = array(
            'name' => 'prize_order_goods_id',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        $schemas['refund_num'] = array(
            'name' => '退回次数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['state'] = array(
            'name' => '销售状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->saleStateDatas
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['order_no'] = array(
            'name' => '订单NO',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => true
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
        $schemas['order_state'] = array(
            'name' => '订单状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => false,
                'items' => $this->orderStateDatas
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'defaultValues' => array(
                    \App\Order\Models\Goods::ORDER_STATE1
                ),
                'items' => function ()
                {
                    return array_column($this->orderStateDatas, 'name', 'value');
                }
            )
        );
        $schemas['is_post_single'] = array(
            'name' => '是否晒单',
            'data' => array(
                'type' => 'boolean',
                'length' => 1
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
                'is_show' => false,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['orderActDesc'] = array(
            'name' => 'orderActDesc',
            'data' => array(
                'type' => 'string',
                'length' => 1000
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '订单商品';
    }

    protected function getPartials4List()
    {
        return array(
            '../submodules/order/views/partials/delivery'
        );
    }

    protected function getModel()
    {
        return $this->modelOrderGoods;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        // $goodsList = $this->modelGoodsCommon->getAll();
        foreach ($list['data'] as &$item) {
            // $item['goods_commonid'] = isset($goodsList[$item['goods_commonid']]) ? $goodsList[$item['goods_commonid']] : "--";
            $item['goods_id'] = "云购ID:{$item['goods_id']}<br/>商品名称:(第{$item['goods_period']}云){$item['goods_name']}<br/>单价:{$item['goods_price']}<br/>商品ID:{$item['goods_commonid']}";
            $item['buyer_id'] = "买家ID:{$item['buyer_id']}<br/>买家名称:{$item['buyer_name']}<br/>邮箱:{$item['buyer_email']}<br/>手机:{$item['buyer_mobile']}<br/>IP:{$item['buyer_ip']}";
            $item['purchase_time'] = getMilliTime4Show($item['purchase_time']);
            $item['prize_time'] = getMilliTime4Show($item['prize_time']);
            $item['lottery_code'] = "云购码:{$item['lottery_code']}<br/>幸运码:{$item['prize_code']}<br/>揭晓时间:{$item['prize_time']}<br/>云购次数:{$item['purchase_num']}<br/>购买时间:{$item['purchase_time']}<br/>退回次数:{$item['refund_num']}";
            $item['state'] = $this->saleStateDatas[strval($item['state'])]['name'];
            if ($item['order_state'] == \App\Common\Models\Order\Goods::ORDER_STATE2) { // 待发货
                $item['order_state'] = $this->orderStateDatas[strval($item['order_state'])]['name'];
                // $item['order_state'] = $item['order_state'] . '<br/><a href="javascript:;" class="btn blue icn-only" onclick="List.call(\'' . $item['_id'] . '\', \'你确定要进行发货吗？\', \'deliveryorder\')" class="halflings-icon user white"><i></i> 发货</a>';
                // $item['order_state'] = $item['order_state'] . '<br/><a href="javascript:;" class="btn blue delivery_btn" order_id="'. $item['_id'] .'" >发货</a>';//data-toggle="modal" href="#responsive"
                $item['order_state'] = $item['order_state'] . '<br/><button type="button" onclick="Delivery.show(\'' . $item['_id'] . '\')" class="btn blue">发货</button>';
            } else {
                if (! empty($item['order_state'])) {
                    $item['order_state'] = $this->orderStateDatas[strval($item['order_state'])]['name'];
                } else {
                    $item['order_state'] = "";
                }
            }
        }
        return $list;
    }

    function doDeliveryOrder($order_no, $express_no, $delivery_sn)
    {
        $param = array(
            'order_no' => $order_no,
            'express_no' => $express_no,
            'delivery_sn' => $delivery_sn,
            'user_id' => $_SESSION['admin_id'],
            'user_name' => $_SESSION['admin_name']
        );
        $ret = doPost("http://www.jizigou.com/order/service/deliveryorder", $param);
        $ret = (string) $ret;
        if (! empty($ret)) {
            if (isJson($ret)) {
                $ret = json_decode($ret, true);
                if ($ret["success"]) {
                    $info = $ret['result'];
                    return $info;
                } else {
                    throw new \Exception("发货信息为空");
                }
            } else {
                throw new \Exception("发货信息不是有效的json格式");
            }
        } else {
            throw new \Exception("可能网络繁忙,发货的请求失败");
        }
    }
}