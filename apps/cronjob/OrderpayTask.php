<?php
use Pheanstalk\Pheanstalk;

class OrderpayTask extends \Phalcon\CLI\Task
{

    /**
     * 找已经支付完成的支付单进行支付完成后续的处理
     * 每个一分钟运行一次
     *
     * @param array $params
     */
    public function processAction()
    {
        $params = $this->dispatcher->getParams();

        $modelOrderPay = new \App\Order\Models\Pay();
        // 不断插数据到队列中获取数据
        try {
            // 获取揭晓中的商品列表
            $orderPayList = $modelOrderPay->getProcessingList(1, 1000);
            
            if (! empty($orderPayList['datas'])) {
                foreach ($orderPayList['datas'] as $orderPayInfo) {
                    // 入完成支付处理队列
                    //\iQueue::enqueue4OrderPay(array(
                    //    'out_trade_no' => $orderPayInfo['_id']
                    //));
                }
            }
            die("OK\n");
        } catch (\Exception $e) {
            die($e->getMessage() . "\n");
        }
    }

    /**
     * 完成支付处理
     *
     * @param array $params
     */
    public function finishpayAction()
    {
        $params = $this->dispatcher->getParams();

        $servicePay = new \App\Order\Services\Pay();
        
        // 不断从队列中获取数据
        while (true) {
            try {
                $jobData = \iQueue::dequeue4OrderPay();
                
                if (! empty($jobData)) {
                    // 完成支付处理
                    $out_trade_no = $jobData['out_trade_no'];
                    echo "get {$out_trade_no} from finishpayorder queue \n";
                    $ret = $servicePay->finishPay($out_trade_no);
                    
                    if (! empty($ret['error_code'])) {
                        // print_r($ret);
                        // die('error');
                        // 如果出错的话,就放入重试队列中
                        $data = array(
                            'op' => 'finishpayorder',
                            'out_trade_no' => $out_trade_no
                        );
                        // $pheanstalk2->put(\App\Common\Utils\Helper::myJsonEncode($data));
                    }
                } else {
                    echo ("empty finishpayorder queue \n");
                    usleep(200000);
                }
            } catch (\Exception $e) {}
        }
    }
}