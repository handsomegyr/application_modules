<?php

class iQueue
{

    public static function enqueue($queueName, array $data)
    {
        $di = \Phalcon\DI::getDefault();
        $cache = $di->get("cache");
        
        $data = serialize($data);
        $key = cacheKey(__CLASS__, $data);
        $exist = $cache->get($key);
        if (empty($exist)) {
            $pheanstalk = $di->get('pheanstalk');
            $pheanstalk->useTube($queueName);
            $pheanstalk->put($data);
            $cache->save($key, $exist, 60 * 3); // 3分钟
        }
    }

    public static function dequeue($queueName)
    {
        $di = \Phalcon\DI::getDefault();
        $pheanstalk = $di->get('pheanstalk');
        $pheanstalk->watch($queueName)->ignore('default');
        $job = $pheanstalk->reserve();
        if (($job) !== false) {
            $jobData = $job->getData();
            $jobData = unserialize($jobData);
            $pheanstalk->delete($job);
        } else {
            $jobData = false;
        }
        return $jobData;
    }

    public static function enqueue4OrderPay(array $data)
    {
        self::enqueue('finishpayorder', $data);
    }

    public static function dequeue4OrderPay()
    {
        return self::dequeue('finishpayorder');
    }

    public static function enqueue4NewPeriodGoods(array $data)
    {
        self::enqueue('newperiodgoods', $data);
    }

    public static function dequeue4NewPeriodGoods()
    {
        return self::dequeue('newperiodgoods');
    }

    public static function enqueue4LotteryGoods(array $data)
    {
        self::enqueue('lotterygoods', $data);
    }

    public static function dequeue4LotteryGoods()
    {
        return self::dequeue('lotterygoods');
    }
}