<?php

use Pheanstalk\Pheanstalk;

class MainTask extends \Phalcon\CLI\Task
{

    public function mainAction()
    {
        echo "\nThis is the default task and the default action \n";
        $count = uniqid();
        die('uniqid:' . $count);
    }

    /**
     *
     * @param array $params
     */
    public function testAction()
    {
        $params = $this->dispatcher->getParams();

        echo sprintf('hello %s', $params[0] . uniqid()) . PHP_EOL;
        echo sprintf('best regards, %s', $params[1] . uniqid()) . PHP_EOL;
    }

    public function testputsamejobtoqueueAction()
    {
        $params = $this->dispatcher->getParams();

        $pheanstalk = $this->getDI()->get('pheanstalk');
        $tube = $params[0];
        $pheanstalk->useTube($tube);

        // 不断插数据到队列中获取数据
        try {
            $id = uniqid();
            $job = "";
            while (true) {
                try {
                    if ($job) {
                        $info = $pheanstalk->statsJob($job);
                    }
                } catch (\Exception $e) {
                    $info = array();
                }
                if (empty($info)) {
                    echo "put " . $id . "\n";
                    $job = $pheanstalk->put($id);
                    echo print_r($job) . "\n";
                } else {
                    echo print_r($info) . "\n";
                    echo "has put " . $id . "in queue\n";
                }
                usleep(200000);
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function testputjobtoqueueAction()
    {
        $params = $this->dispatcher->getParams();

        $pheanstalk = $this->getDI()->get('pheanstalk');
        $tube = $params[0];
        $pheanstalk->useTube($tube);

        // 不断插数据到队列中获取数据
        try {
            while (true) {
                $id = uniqid();
                $message = "jobid {$id} payload goes here";
                echo "put " . $message . "\n";
                $pheanstalk->put($message);
                usleep(200000);
            }
        } catch (\Exception $e) {
        }
    }

    public function testgetjobfromqueueAction()
    {
        $params = $this->dispatcher->getParams();

        $pheanstalk = $this->getDI()->get('pheanstalk');
        $tube = $params[0];
        $pheanstalk->watch($tube)->ignore('default');

        // 不断从队列中获取数据
        try {
            while (true) {
                if (($job = $pheanstalk->reserve()) !== false) {
                    $message = $job->getData();
                    echo "get " . $message . "\n";
                    $pheanstalk->delete($job);
                } else {
                    usleep(200000);
                    break;
                }
            }
        } catch (\Exception $e) {
        }
    }
}
