<?php

/**
 * 
 * /usr/bin/php /usr/share/nginx/learn-php/phalcon/application_modules/public/cli.php cronjob run a b &
 * @author Administrator
 *
 */
class CronjobTask extends \Phalcon\CLI\Task
{

    /**
     * 批量运营计划任务
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
            $startTime = microtime(true);
            $model = new \App\Cronjob\Models\Job();
            $cmds = $model->getAll();
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
                    $model->startJob($cmd['_id']);
                    $tmp = array(
                        'resource' => $fp,
                        'cmd' => $cmd,
                        'result' => ''
                    );
                    $resources[] = $tmp;
                    $i ++;
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
                    if (! feof($r['resource'])) {
                        $r['result'] .= fread($r['resource'], 1024);
                        $resources[$k]['result'] = $r['result'];
                    } else {
                        pclose($r['resource']);
                        $i --;
                        $model->recordResult($r['cmd']['_id'], $r['result'], $startTime);
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
}