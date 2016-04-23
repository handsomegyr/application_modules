<?php
use Pheanstalk\Pheanstalk;

class MsgTask extends \Phalcon\CLI\Task
{

    /**
     * 找满员商品揭晓结果通知的处理
     * 每个一分钟运行一次
     *
     * @param array $params            
     */
    public function processAction(array $params)
    {
        $modelOrderGoods = new \App\Order\Models\Goods();
        
        $pheanstalk = $this->getDI()->get('pheanstalk');
        
        // 不断插数据到队列中获取数据
        try {
            // 获取已揭晓的商品列表
            $orderGoodsList = $modelOrderGoods->getList4MsgSend(1, 1000);
            
            if (! empty($orderGoodsList['datas'])) {
                foreach ($orderGoodsList['datas'] as $orderGoodsInfo) {
                    echo "put {$orderGoodsInfo['_id']} in sendmsg4lotteryresult queue\n";
                    // 入新期的商品处理队列
                    $pheanstalk->useTube('sendmsg4lotteryresult');
                    $pheanstalk->put($orderGoodsInfo['_id']);
                    // 已发送
                    $modelOrderGoods->isMsgSent($orderGoodsInfo['_id']);
                }
            }
            die("OK\n");
        } catch (\Exception $e) {
            die($e->getMessage() . "\n");
        }
    }

    /**
     * 发送消息
     *
     * @param array $params            
     */
    public function sendmsg4lotteryresultAction(array $params)
    {
        $modelTemplate = new \App\Message\Models\Template();
        $modelMailSettings = new \App\Mail\Models\Settings();
        $modelSmsSettings = new \App\Sms\Models\Settings();
        $modelSysMsg = new \App\Message\Models\SysMsg();
        $modelMember = new \App\Member\Models\Member();
        $modelOrderGoods = new \App\Order\Models\Goods();
        $pheanstalk = $this->getDI()->get('pheanstalk');
        $pheanstalk->watch('sendmsg4lotteryresult')->ignore('default');
        
        // 不断从队列中获取数据
        while (true) {
            try {
                if (($job = $pheanstalk->reserve()) !== false) {
                    $order_goods_id = $job->getData();
                    // echo $order_goods_id . "\n";
                    $pheanstalk->delete($job);
                    
                    $orderGoodsInfo = $modelOrderGoods->getInfoById($order_goods_id);
                    $memberInfo = $modelMember->getInfoById($orderGoodsInfo['buyer_id']);
                    $buyer_name = $modelMember->getRegisterName($memberInfo);
                    
                    // 中奖
                    if (in_array($orderGoodsInfo['prize_code'], explode(',', $orderGoodsInfo['lottery_code']))) {
                        
                        if (! empty($memberInfo['mobile'])) {
                            // 短信发送
                            $mobile = $memberInfo['mobile'];
                            $tpl_info = $modelTemplate->getLotteryOkMobileTemplate();
                            $modelSmsSettings->sendSms($mobile, $tpl_info['subject'], $tpl_info['content']);
                        }
                        if (! empty($memberInfo['email'])) {
                            // 邮件发送
                            $email = $memberInfo['email'];
                            $tpl_info = $modelTemplate->getLotteryOkEmailTemplate($buyer_name, $orderGoodsInfo['goods_id'], $orderGoodsInfo['goods_name'], $orderGoodsInfo['goods_period']);
                            $modelMailSettings->sendEmail($email, $tpl_info['subject'], $tpl_info['content']);
                        }
                    } else {
                        // 未中奖
                        $tpl_info = $modelTemplate->getLotteryNoTemplate($buyer_name, $orderGoodsInfo['goods_id'], $orderGoodsInfo['goods_name'], $orderGoodsInfo['goods_period']);
                        if (! empty($memberInfo['noticesettings']['sysMsgSet'])) {
                            // 发送系统消息
                            $modelSysMsg->log($memberInfo['_id'], $tpl_info['content']);
                            $this->modelMsgCount->incSysMsgCount($memberInfo['_id']);
                        }
                        if (! empty($memberInfo['noticesettings']['wxMailSet'])) {
                            if (! empty($memberInfo['email'])) {
                                // 邮件发送
                                $email = $memberInfo['email'];
                                $modelMailSettings->sendEmail($email, $tpl_info['subject'], $tpl_info['content']);
                            }
                            if (! empty($memberInfo['weixinopenid'])) {
                                // 发送微信短信
                            }
                        }
                    }
                } else {
                    usleep(200000);
                }
            } catch (\Exception $e) {}
        }
    }
    
    
    
}