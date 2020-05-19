<?php

namespace App\Service\Controllers;

class EvcardController extends ControllerBase
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
        // http://www.jizigou.com/service/evcard/sendcoupon
        try {
            $currentTime = date('Y-m-d H:i:s');
            // 渠道KEY:cole_test
            // 渠道密钥:6964952c-de93-4b30-a87b-5ecc32426ebb
            // 渠道KEY:cocacolatest
            // 渠道密钥:90670134-634c-49de-8a43-5a0f44758792

            // 活动加密ID：hwfd3baj
            // 券ID：2098
            // 券ID：2099
            // 券ID：2100
            $objEvcardMmp = new \EvcardMmp("cocacolatest", "dc283494-b999-47b1-af4d-316cebb56d66", false);
            $ret = $objEvcardMmp->offerSingleThirdCoupon("h36bedar", "13564100096", 184);

            //$objEvcardMmp = new \EvcardMmp("cole_test","6964952c-de93-4b30-a87b-5ecc32426ebb",false);						
            //$ret = $objEvcardMmp->offerSingleThirdCoupon("hwfd3baj", "13564100096", 2098);

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
