<?php
namespace Webcms\Lottery\Controllers;

class IndexController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    public function indexAction()
    {
        die('lottery');
    }

    /**
     * 抽奖
     */
    public function getAction()
    {
        // http://magic.gtrgogogo.com/lottery/index/get?activity_id=565d5aaa7f50ea081300002d&FromUserName=guoyongrong
        try {
            $activity_id = isset($_GET['activity_id']) ? trim($_GET['activity_id']) : '';
            $identity_id = trim($_GET['user_id']);
            $source = \Webcms\Lottery\Models\Identity::SOURCE_WEIXIN;
            
            $modelApi = new \Webcms\Lottery\Services\Api();
            $identityInfo = array(
                'nickname' => '郭永荣'
            );
            $demo = array(
                'activity_id' => $activity_id
            );
            $lotteryInfo = $modelApi->doLottery($activity_id, $identity_id, array(), array(), $source, $identityInfo, $demo);
            if (empty($lotteryInfo['error_code']) && ! empty($lotteryInfo['result'])) {
                $exchangeInfo = $lotteryInfo['result'];
                echo $this->result("OK", $exchangeInfo);
                return true;
            } else {
                // 失败的话
                echo ($this->error($lotteryInfo['error_code'], $lotteryInfo['error_msg']));
                return false;
            }
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}

