<?php
namespace Webcms\Weixinredpack\Controllers;

class IndexController extends ControllerBase
{

    private $servicesApi;

    public function initialize()
    {
        $this->servicesApi = new \Webcms\Weixinredpack\Services\Api('nojson');
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 首页
     */
    public function indexAction()
    {}

    /**
     * 领取红包的接口
     */
    public function getAction()
    {
        // http://magic.gtrgogogo.com/weixinredpack/index/get?re_openid=ooCkDj-Knv4XWlRa2jjOwtspd7pk&customer_id=56ee23f2ae8715942d000030&redpack_id=56ee254dae8715942d000031
        try {
            $activity_id = YUNGOU_ACTIVITY_ID;
            $customer_id = trim($_GET['customer_id']);
            $redpack_id = trim($_GET['redpack_id']);
            $re_openid = trim($_GET['re_openid']);
            $amount = 0;
            $info = array(
                'openid' => $re_openid,
                'nickname' => '郭永荣',
                'headimgurl' => '',
                're_nickname' => '郭永荣',
                're_headimgurl' => ''
            );
            
            $gotInfo = $this->servicesApi->sendRedpack($activity_id, $customer_id, $redpack_id, $re_openid, $amount, $info);
            if (empty($gotInfo['error_code']) && ! empty($gotInfo['result'])) {
                $exchangeInfo = $gotInfo['result'];
                echo $this->result("OK", $exchangeInfo);
                return true;
            } else {
                // 失败的话
                echo ($this->error($gotInfo['error_code'], $gotInfo['error_msg']));
                return false;
            }
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}

