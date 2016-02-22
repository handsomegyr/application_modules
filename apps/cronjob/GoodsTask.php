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
    public function processAction(array $params)
    {
        $modelGoods = new \Webcms\Goods\Models\Goods();
        
        $pheanstalk = $this->getDI()->get('pheanstalk');
        $cache = $this->getDI()->get("cache");
        // 不断插数据到队列中获取数据
        try {
            // 获取揭晓中的商品列表
            $goodsList = $modelGoods->getProcessingList(1, 1000);
            
            if (! empty($goodsList['datas'])) {
                foreach ($goodsList['datas'] as $goodsInfo) {
                    
                    // 入新期的商品处理队列
                    $key = cacheKey(__CLASS__, 'newperiodgoods', $goodsInfo['goods_commonid']);
                    $exist = $cache->get($key);
                    if (empty($exist)) {
                        echo "put {$goodsInfo['goods_commonid']} in newperiodgoods queue\n";
                        $pheanstalk->useTube('newperiodgoods');
                        $pheanstalk->put($goodsInfo['goods_commonid']);
                        $cache->save($key, $exist, 60 * 3); // 3分钟
                    } else {
                        echo "{$goodsInfo['goods_commonid']} is already in newperiodgoods queue\n";
                    }
                    
                    // 入抽奖处理队列
                    $key = cacheKey(__CLASS__, 'lotterygoods', $goodsInfo['_id']);
                    $exist = $cache->get($key);
                    if (empty($exist)) {
                        echo "put {$goodsInfo['_id']} in lotterygoods queue\n";
                        $pheanstalk->useTube('lotterygoods');
                        $pheanstalk->put($goodsInfo['_id']);
                        $cache->save($key, $exist, 60 * 3); // 3分钟
                    } else {
                        echo "{$goodsInfo['_id']} is already in lotterygoods queue\n";
                    }
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
    public function createnewperiodgoodsAction(array $params)
    {
        $serviceGoods = new \Webcms\Goods\Services\Goods();
        $pheanstalk = $this->getDI()->get('pheanstalk');
        $pheanstalk->watch('newperiodgoods')->ignore('default');
        $cache = $this->getDI()->get("cache");
        // 重试队列
        // $pheanstalk2 = $this->getDI()->get('pheanstalk');
        // $pheanstalk2->useTube('retry');
        
        // 不断从队列中获取数据
        while (true) {
            try {
                if (($job = $pheanstalk->reserve()) !== false) {
                    $goods_commonid = $job->getData();
                    $pheanstalk->delete($job);
                    
                    $key = cacheKey(__CLASS__, 'newperiodgoods', $goods_commonid);
                    $exist = $cache->get($key);
                    if (! empty($exist)) {
                        continue;
                    }
                    
                    // 新一期商品生成
                    echo "get {$goods_commonid} from newperiodgoods queue \n";
                    $ret = $serviceGoods->createNewPeriodGoods($goods_commonid);
                    
                    if (! empty($ret['error_code'])) {
                        // 如果出错的话,就放入重试队列中
                        $data = array(
                            'op' => 'createnewperiodgoods',
                            'goods_commonid' => $goods_commonid
                        );
                        // $pheanstalk2->put(json_encode($data));
                    }
                } else {
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
    public function lotterygoodsAction(array $params)
    {
        $serviceGoods = new \Webcms\Goods\Services\Goods();
        $pheanstalk = $this->getDI()->get('pheanstalk');
        $pheanstalk->watch('lotterygoods')->ignore('default');
        $cache = $this->getDI()->get("cache");
        // 重试队列
        // $pheanstalk2 = $this->getDI()->get('pheanstalk');
        // $pheanstalk2->useTube('retry');
        
        // 不断从队列中获取数据
        while (true) {
            try {
                if (($job = $pheanstalk->reserve()) !== false) {
                    $goods_id = $job->getData();
                    $pheanstalk->delete($job);
                    
                    $key = cacheKey(__CLASS__, 'lotterygoods', $goods_id);
                    $exist = $cache->get($key);
                    if (! empty($exist)) {
                        continue;
                    }
                    
                    // 商品抽奖处理
                    echo "get {$goods_id} from lotterygoods queue \n";
                    $ret = $serviceGoods->lottery($goods_id);
                    
                    if (! empty($ret['error_code'])) {
                        // 如果出错的话,就放入重试队列中
                        $data = array(
                            'op' => 'lotterygoods',
                            'goods_id' => $goods_id
                        );
                        // $pheanstalk2->put(json_encode($data));
                    }
                } else {
                    usleep(200000);
                }
            } catch (\Exception $e) {}
        }
    }
}