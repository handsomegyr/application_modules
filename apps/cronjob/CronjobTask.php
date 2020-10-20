<?php

/**
 * 
 * 
 * @author Administrator
 *
 */
class CronjobTask extends \Phalcon\CLI\Task
{

    /**
     * 批量运营计划任务
     * /usr/bin/php /usr/share/nginx/learn-php/phalcon/application_modules/public/cli.php cronjob run a b &
     */
    public function runAction(array $params)
    {
        echo "====================";
        echo "start time:" . date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";
        resetTimeMemLimit();
        $cronjobString = APP_PATH . 'public/cli.php';

        try {
            $nowTime = time();
            $startTime = microtime(true);
            $modelJob = new \App\Cronjob\Models\Job();
            $modelJobLog = new \App\Cronjob\Models\Log();
            $cmds = $modelJob->getAll();
            if (empty($cmds)) {
                echo "no cmd need run\n";
                return false;
            }
            $rst = array();
            foreach ($cmds as $cmd) {
                if (isset($cmd['last_execute_time']) && $cmd['last_execute_time'] instanceof \MongoDate) {
                    // 如果设定了秒
                    if (!empty($cmd['sec_cycle'])) {
                        $sec_cycle = intval($cmd['sec_cycle']);
                        if ($cmd['last_execute_time']->sec + $sec_cycle > $nowTime) {
                            continue;
                        }
                    } else {
                        if (!empty($cmd['cron'])) {
                            try {
                                $cron = \Cron\CronExpression::factory($cmd['cron']);
                                if (!$cron->isDue()) {
                                    continue;
                                }
                            } catch (\Exception $e) {
                                continue;
                            }
                        } else {
                            $cycle = isset($cmd['cycle']) ? $cmd['cycle'] : 0;
                            if ($cmd['last_execute_time']->sec + $cycle * 60 > $nowTime) {
                                continue;
                            }
                        }
                    }
                }
                $rst[] = $cmd;
            }
            $cmds = $rst;
            if (empty($cmds)) {
                echo "no cmd need run\n";
                return false;
            }

            $i = 0;
            $resources = array();
            foreach ($cmds as $cmd) {
                // /usr/bin/php /home/wwwroot/webcms/public/cli.php main test a b &
                echo $cmdString = "/usr/bin/php {$cronjobString} {$cmd['cmd']}";
                echo "\n";
                $fp = popen($cmdString, 'r');
                if (is_resource($fp)) {
                    $modelJob->startJob($cmd['_id'], $nowTime);
                    $tmp = array(
                        'resource' => $fp,
                        'cmd' => $cmd,
                        'result' => ''
                    );
                    $resources[] = $tmp;
                    $i++;
                } else {
                    echo "popen cmd error:\n";
                    echo $cmdString;
                    echo "\n";
                }
            }
            if (empty($resources)) {
                echo "no resource need deal\n";
                return false;
            }
            while (true) {
                if ($i == 0) {
                    break;
                }
                foreach ($resources as $k => $r) {
                    if (!feof($r['resource'])) {
                        $r['result'] .= fread($r['resource'], 1024);
                        $resources[$k]['result'] = $r['result'];
                    } else {
                        pclose($r['resource']);
                        $i--;
                        $scriptExecuteTime = sprintf("%08.2f", microtime(true) - $startTime);
                        $modelJob->recordResult($r['cmd']['_id'], $r['result'], $scriptExecuteTime);
                        $modelJobLog->recordResult($r['cmd']['_id'], $r['result'], $scriptExecuteTime);
                    }
                }
            }

            echo "all cmd is done\n";
            echo "====================";
            echo "end time:" . date("Y-m-d H:i:s");
            echo "====================";
            echo "\n";
            return true;
        } catch (\Exception $e) {
            echo "====================";
            echo "Exception:" . date("Y-m-d H:i:s");
            echo "====================";
            echo "\n";
            var_dump($e->getTraceAsString());
            echo "\n";
        }
    }

    /**
     * 批量运营计划任务
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php cronjob runbyjobby a b &
     */
    public function runbyjobbyAction(array $params)
    {
        echo "====================";
        echo "start time:" . date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";
        resetTimeMemLimit();
        $cronjobString = APP_PATH . 'public/cli.php';

        try {
            $nowTime = time();
            $startTime = microtime(true);
            $modelJob = new \App\Cronjob\Models\Job();
            $modelJobLog = new \App\Cronjob\Models\Log();
            $cmds = $modelJob->getAll();
            if (empty($cmds)) {
                echo "no cmd need run\n";
                return false;
            }

            $jobby = new \Jobby\Jobby();

            // $jobby->add('CommandExample', array(
            // 'command' => 'ls',
            // 'schedule' => '* * * * *',
            // 'output' => '/logs/command.log',
            // 'enabled' => true
            // ));

            // $jobby->add('ClosureExample', array(
            // 'command' => function () {
            // echo "I'm a function!\n";
            // return true;
            // },
            // 'schedule' => '* * * * *',
            // 'output' => 'logs/closure.log',
            // 'enabled' => true
            // ));

            foreach ($cmds as $cmd) {
                // /usr/bin/php /home/wwwroot/webcms/public/cli.php main test a b &
                $cmdString = "/usr/bin/php {$cronjobString} {$cmd['cmd']}";
                echo $cmdString . "\n";
                $jobby->add($cmd['_id'], array(
                    'command' => $cmdString,
                    'schedule' => $cmd['cron'],
                    'output' => "/logs/command_{$cmd['_id']}.log",
                    'enabled' => true
                ));
            }

            $jobby->run();

            echo "all cmd is done\n";
            echo "====================";
            echo "end time:" . date("Y-m-d H:i:s");
            echo "====================";
            echo "\n";
            return true;
        } catch (\Exception $e) {
            echo "====================";
            echo "Exception:" . date("Y-m-d H:i:s");
            echo "====================";
            echo "\n";
            var_dump($e->getTraceAsString());
            echo "\n";
        }
    }

    /**
     * 批量运营计划任务
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php cronjob runbyjobby2 &
     */
    public function runbyjobby2Action(array $params)
    {
        echo "====================";
        echo "start time:" . date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";
        resetTimeMemLimit();
        $cronjobString = APP_PATH . 'public/cli.php';

        try {
            $nowTime = time();
            $startTime = microtime(true);
            $modelJob = new \App\Cronjob\Models\Job();
            $modelJobLog = new \App\Cronjob\Models\Log();
            $cmds = $modelJob->getAll();
            if (empty($cmds)) {
                echo "no cmd need run\n";
                return false;
            }

            $rst = array();
            foreach ($cmds as $cmd) {
                if (isset($cmd['last_execute_time']) && $cmd['last_execute_time'] instanceof \MongoDate) {
                    // 如果设定了秒
                    if (!empty($cmd['sec_cycle'])) {
                        $sec_cycle = intval($cmd['sec_cycle']);
                        if ($cmd['last_execute_time']->sec + $sec_cycle > $nowTime) {
                            continue;
                        }
                    } else {
                        if (!empty($cmd['cron'])) {
                            try {
                                $cron = \Cron\CronExpression::factory($cmd['cron']);
                                if (!$cron->isDue()) {
                                    continue;
                                }
                            } catch (\Exception $e) {
                                continue;
                            }
                        } else {
                            $cycle = isset($cmd['cycle']) ? $cmd['cycle'] : 0;
                            if ($cmd['last_execute_time']->sec + $cycle * 60 > $nowTime) {
                                continue;
                            }
                        }
                    }
                }
                $rst[] = $cmd;
            }
            $cmds = $rst;
            if (empty($cmds)) {
                echo "no cmd need run\n";
                return false;
            }

            $helper = new \Jobby\Helper();
            $host = $helper->getHost();

            foreach ($cmds as $cmd) {
                // /usr/bin/php /home/wwwroot/webcms/public/cli.php main test a b &
                $cmdString = "/usr/bin/php {$cronjobString} {$cmd['cmd']}";
                echo $cmdString . "\n";
                $cmd_id = $cmd['_id'];
                $modelJob->startJob($cmd_id, $nowTime);
                $stdout = "/logs/command_{$cmd['_id']}_{$nowTime}.log";
                $config = array(
                    'command' => $cmdString,
                    'output' => $stdout,
                    'runOnHost' => $host,
                    'enabled' => true
                );
                // 这里可以找一些异步库来进行性能优化
                $job = new \Jobby\BackgroundJob($cmd['name'] . '_' . $cmd['_id'], $config);
                try {
                    $job->run();
                    if (file_exists($stdout)) {
                        $result = file_get_contents($stdout);
                    } else {
                        $result =  '';
                    }
                    $scriptExecuteTime = sprintf("%08.2f", microtime(true) - $startTime);
                    $modelJob->recordResult($cmd_id, $result, $scriptExecuteTime);
                    $modelJobLog->recordResult($cmd_id, $result, $scriptExecuteTime);
                } catch (\Exception $th) {
                    echo $th->getMessage();
                }
            }

            echo "all cmd is done\n";
            echo "====================";
            echo "end time:" . date("Y-m-d H:i:s");
            echo "====================";
            echo "\n";
            return true;
        } catch (\Exception $e) {
            echo "====================";
            echo "Exception:" . date("Y-m-d H:i:s");
            echo "====================";
            echo "\n";
            var_dump($e->getTraceAsString());
            echo "\n";
        }
    }
}
