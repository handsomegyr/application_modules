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
    public function processAction(array $params)
    {
        $modelOrderPay = new \App\Order\Models\Pay();
        $cache = $this->getDI()->get("cache");
        $pheanstalk = $this->getDI()->get('pheanstalk');
        $pheanstalk->useTube('finishpayorder');
        
        // 不断插数据到队列中获取数据
        try {
            // 获取揭晓中的商品列表
            $orderPayList = $modelOrderPay->getProcessingList(1, 1000);
            
            if (! empty($orderPayList['datas'])) {
                foreach ($orderPayList['datas'] as $orderPayInfo) {
                    // 入完成支付处理队列
                    
                    $key = cacheKey(__CLASS__, $orderPayInfo['_id']);
                    $exist = $cache->get($key);
                    if (empty($exist)) {
                        echo "put {$orderPayInfo['_id']} in finishpayorder queue \n";
                        $pheanstalk->put($orderPayInfo['_id']);
                        $cache->save($key, $exist, 60 * 3); // 3分钟
                    } else {
                        echo "{$orderPayInfo['_id']} is already in finishpayorder queue\n";
                    }
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
    public function finishpayAction(array $params)
    {
        $servicePay = new \App\Order\Services\Pay();
        $pheanstalk = $this->getDI()->get('pheanstalk');
        $pheanstalk->watch('finishpayorder')->ignore('default');
        $cache = $this->getDI()->get("cache");
        // 重试队列
        // $pheanstalk2 = $this->getDI()->get('pheanstalk');
        // $pheanstalk2->useTube('retry');
        
        // 不断从队列中获取数据
        while (true) {
            try {
                if (($job = $pheanstalk->reserve()) !== false) {
                    $out_trade_no = $job->getData();
                    $pheanstalk->delete($job);
                    
                    $key = cacheKey(__CLASS__, $out_trade_no);
                    $exist = $cache->get($key);
                    if (! empty($exist)) {
                        continue;
                    }
                    
                    // 完成支付处理
                    echo "get {$out_trade_no} from finishpayorder queue \n";
                    $ret = $servicePay->finishPay($out_trade_no);
                    
                    if (! empty($ret['error_code'])) {
                        print_r($ret);
                        die('error');
                        // 如果出错的话,就放入重试队列中
                        $data = array(
                            'op' => 'finishpayorder',
                            'out_trade_no' => $out_trade_no
                        );
                        // $pheanstalk2->put(json_encode($data));
                    }
                } else {
                    echo ('empty queue');
                    usleep(200000);
                }
            } catch (\Exception $e) {}
        }
    }
}