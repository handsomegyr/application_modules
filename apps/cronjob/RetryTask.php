<?php
use Pheanstalk\Pheanstalk;

class RetryTask extends \Phalcon\CLI\Task
{

    /**
     * 重试的处理
     *
     * @param array $params            
     */
    public function processAction(array $params)
    {
        $serviceGoods = new \App\Goods\Services\Goods();
        $serviceGoods = new \App\Order\Services\Pay();
        $pheanstalk = $this->getDI()->get('pheanstalk');
        $pheanstalk->watch('retry')->ignore('default');
        
        // 不断从队列中获取数据
        while (true) {
            try {
                if (($job = $pheanstalk->reserve()) !== false) {
                    $msg = json_decode($job->getData(), true);
                    // echo $goods_commonid . "\n";
                    $pheanstalk->delete($job);
                    if ($msg['op'] == 'createnewperiodgoods') {
                        // 新一期商品生成
                        $ret = $serviceGoods->createNewPeriodGoods($msg['goods_commonid']);
                    } elseif ($msg['op'] == 'lotterygoods') {
                        // 商品抽奖
                        $ret = $serviceGoods->lottery($msg['goods_id']);
                    } elseif ($msg['op'] == 'finishpayorder') {
                        // 完成支付处理
                        $ret = $serviceGoods->finishPay($msg['out_trade_no']);
                    }
                    
                    if (! empty($ret['error_code'])) {
                        // // 如果出错的话,就放入重试队列中
                        // $data = array(
                        // 'op' => 'createnewperiodgoods',
                        // 'goods_commonid' => $goods_commonid
                        // );
                        // $pheanstalk2->put(json_encode($data));
                    }
                } else {
                    usleep(200000);
                }
            } catch (\Exception $e) {}
        }
    }
}