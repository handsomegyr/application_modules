<?php

namespace App\Service\Controllers;

class SaicmobilityController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 提供发优惠券的服务
     */
    public function sendcouponAction()
    {
        // http://www.jizigou.com/service/saicmobility/sendcoupon
        try {
            $currentTime = date('Y-m-d H:i:s');
            $objSaicmobility = new \Saicmobility(1040001, "sqjt0212", false);
            $ret = $objSaicmobility->gtwtrustSendCoupon("13032124712", "CarExhibition201902", "saicgroup", "aabb");
            $ret['currentTime'] = $currentTime;
            echo $this->result('OK', $ret);
            return true;
        } catch (\Exception $e) {
            // 记录错误
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }
}
