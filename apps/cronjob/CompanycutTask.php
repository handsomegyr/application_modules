<?php
class CompanycutTask extends \Phalcon\CLI\Task
{
    /**
     * 公司CUT相关的处理
     * cd /learn-php/phalcon/application_modules/apps/cronjob
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php companycut handle svntest
     */
    public function handleAction(array $params)
    {
        if (empty($params)) {
            $project_code = "svntest";
        } else {
            $project_code = $params[0];
        }

        /*
        * CUT
        */
        // $SVNSERVER = 'https://192.168.81.129/';
        $SVNSERVER = 'svn://192.168.81.129/repository/';
        $WWWROOT_R = '/home/webs/';
        $WWWROOT = $WWWROOT_R . 'dev/';
        $SVNCMD = '/usr/bin/svn ';
        $SVNOPTIONS = ' --config-dir /var/svn --no-auth-cache --non-interactive --username handsomegyr --password guotingyu0324 ';    //这个'/var/.subversion’目录别删了哦.

        $RSYNCCMD = "/bin/env USER='cutu5er' RSYNC_PASSWORD='1ccOper5' /usr/bin/rsync -vzrpt --blocking-io --exclude='.svn' --exclude='.git' --exclude='.buildpath' --exclude='.project' --exclude='.gitignore' --exclude='*.log' --exclude='/logs/*' --exclude='/cache/*' --exclude=node_modules ";
        $RSYNCSERVER = '192.168.81.129';

        // $modelTask = new \App\Cronjob\Models\Task();
        // // 从task表中取出cut相关的任务进行处理  
        // $query = array('type' => 1);
        // $sort = array();
        // $cutTaskList = $modelTask->find($query, $sort, 1, 100);
        // if (empty($cutTaskList['datas'])) {
        //     return;
        // }

        // 测试用
        $taskInfo = array();
        $taskInfo['content'] = array('process_name' => 'svn_import');
        $cutTaskList['datas'][] = $taskInfo;
        // 循环处理
        foreach ($cutTaskList['datas'] as $key => $taskInfo) {
            $taskContent = $taskInfo['content'];
            // svn import
            if ($taskContent['process_name'] == 'svn_import') {
                //
                $workingcopy_path = $WWWROOT . $project_code;

                //将工程交由版本库控制：svn import 项目目录 SVN Repository URL -m "提交日志信息"
                //svn import E:\SVN\workProejcts\projectA https://wangdaye-PC/svn/myRepository/ -m "initial import"
                $subcommand = ' import ';
                $svn_url = $SVNSERVER . $project_code;
                $cmdline = $SVNCMD . $subcommand . $workingcopy_path . ' ' . $svn_url . ' -m "' . $project_code . '" ' . $SVNOPTIONS . ' 2>&1 ';
                $tip = exec("$cmdline", $output, $ret);
                $result_ary = array();
                $result_ary['project_code'] = $project_code;
                $result_ary['cmdline'] = $cmdline;
                $result_ary['tip'] = $tip;
                $result_ary['ret'] = $ret;
                $result_ary['output'] = $output;
                print_r($result_ary);
            }
        }
    }
}
