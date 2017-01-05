<?php
namespace App\Order\Models;

class OrderCommon extends \App\Common\Models\Order\OrderCommon
{

    /**
     * 生成订单扩展表信息
     *
     * @param array $orderInfo            
     * @param array $storeInfo            
     * @param array $receiverInfo            
     * @param array $invoiceInfo            
     * @param array $promotionInfo            
     * @param array $voucherInfo            
     * @param string $order_message            
     * @throws \Exception
     * @return array
     */
    public function create(array $orderInfo, array $storeInfo, array $receiverInfo, array $invoiceInfo, array $promotionInfo, array $voucherInfo, $order_message = "")
    {
        $data = array();
        // 订单信息
        $data['order_id'] = $orderInfo['order_id'];
        $data['order_message'] = $order_message;
        
        // 收货人信息
        if (! empty($receiverInfo)) {
            $data['receiver_info'] = serialize($receiverInfo);
            $data['receiver_name'] = $receiverInfo['receiver_name'];
            $data['receiver_province_id'] = $receiverInfo['receiver_province_id'];
            $data['receiver_city_id'] = $receiverInfo['receiver_city_id'];
        }
        
        // 发票信息
        if (! empty($invoiceInfo)) {
            $data['invoice_info'] = serialize($invoiceInfo);
        }
        
        // 保存促销信息
        if (! empty($promotionInfo)) {
            $data['promotion_info'] = serialize($promotionInfo);
        }
        
        // 代金券信息
        if (! empty($voucherInfo)) {
            $data['voucher_price'] = $voucherInfo['voucher_price'];
            $data['voucher_code'] = $voucherInfo['voucher_code'];
        }
        
        $orderCommonInfo = $this->insert($data);
        return $orderCommonInfo;
    }

}