<?php
use Pheanstalk\Pheanstalk;

class GoodsTask extends \Phalcon\CLI\Task
{

    /**
     * 找满员的商品进行生成新一期商品和抽奖的处理
     * 每个一分钟运行一次
     *
     * @param array $params
     */
    public function processAction()
    {
        $params = $this->dispatcher->getParams();

        $modelGoods = new \App\Goods\Models\Goods();
        
        // 不断插数据到队列中获取数据
        try {
            // 获取揭晓中的商品列表
            $goodsList = $modelGoods->getProcessingList(1, 1000);
            
            if (! empty($goodsList['datas'])) {
                foreach ($goodsList['datas'] as $goodsInfo) {
                    
                    // // 入新期的商品处理队列
                    // \iQueue::enqueue4NewPeriodGoods(array(
                    // 'goods_id' => $goodsInfo['_id']
                    // ));
                    
                    // // 入抽奖处理队列
                    // \iQueue::enqueue4LotteryGoods(array(
                    // 'goods_id' => $goodsInfo['_id']
                    // ));
                }
            }
            die("OK\n");
        } catch (\Exception $e) {
            die($e->getMessage() . "\n");
        }
    }

    /**
     * 生成新一期的商品
     *
     * @param array $params
     */
    public function createnewperiodgoodsAction()
    {
        $params = $this->dispatcher->getParams();

        $serviceGoods = new \App\Goods\Services\Goods();
        $modelGoods = new \App\Goods\Models\Goods();
        
        // 不断从队列中获取数据
        while (true) {
            try {
                $jobData = \iQueue::dequeue4NewPeriodGoods();
                
                if (! empty($jobData)) {
                    $goods_id = $jobData['goods_id'];
                    $goodsInfo = $modelGoods->getInfoById($goods_id);
                    if (empty($goodsInfo)) {
                        continue;
                    }
                    if ($goodsInfo['sale_state'] != \App\Goods\Models\Goods::SALE_STATE2) {
                        continue;
                    }
                    $goods_commonid = $goodsInfo['goods_commonid'];
                    
                    // 新一期商品生成
                    echo "get {$goods_commonid} from newperiodgoods queue \n";
                    $ret = $serviceGoods->createNewPeriodGoods($goods_commonid);
                    
                    if (! empty($ret['error_code'])) {
                        // 如果出错的话,就放入重试队列中
                        $data = array(
                            'op' => 'createnewperiodgoods',
                            'goods_commonid' => $goods_commonid
                        );
                        // $pheanstalk2->put(\App\Common\Utils\Helper::myJsonEncode($data));
                    }
                } else {
                    echo ("empty newperiodgoods queue \n");
                    usleep(200000);
                }
            } catch (\Exception $e) {}
        }
    }

    /**
     * 商品抽奖处理
     *
     * @param array $params
     */
    public function lotterygoodsAction()
    {
        $params = $this->dispatcher->getParams();

        $serviceGoods = new \App\Goods\Services\Goods();
        
        // 不断从队列中获取数据
        while (true) {
            try {
                $jobData = \iQueue::dequeue4lotterygoods();
                
                if (! empty($jobData)) {
                    $goods_id = $jobData['goods_id'];
                    
                    // 商品抽奖处理
                    echo "get {$goods_id} from lotterygoods queue \n";
                    $ret = $serviceGoods->lottery($goods_id);
                    
                    if (! empty($ret['error_code'])) {
                        // 如果出错的话,就放入重试队列中
                        $data = array(
                            'op' => 'lotterygoods',
                            'goods_id' => $goods_id
                        );
                        // $pheanstalk2->put(\App\Common\Utils\Helper::myJsonEncode($data));
                    }
                } else {
                    echo ("empty lotterygoods queue \n");
                    usleep(200000);
                }
            } catch (\Exception $e) {}
        }
    }
}