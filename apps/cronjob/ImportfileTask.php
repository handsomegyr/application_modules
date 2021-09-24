<?php
class ImportfileTask extends \Phalcon\CLI\Task
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:import_file {csvfilename?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '文件导入';

    /**
     * @var \App\Cronjob\Models\DataImport\File
     */
    private $modelFile = null;

    /**
     * @var \App\Cronjob\Models\DataImport\Log
     */
    private $modelLog = null;

    private $time = 0;

    /**
     * 处理
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php importfile handle 5e46128f69dc0a0f8415fe8f.csv
     * @param array $params            
     */
    public function handleAction(array $params)
    {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

        $this->time = time();
        $day = date("Ymd", $this->time);

        // 指定文件
        $csvfilename = empty($params[0]) ? '' : $params[0];
        if (empty($csvfilename)) {
            $csvfilename = "";
        }

        $config = $this->getDI()->get('config');
        $cronjobdataimportSettings = $config->cronjobdataimport->toArray();
        $env = "production";
        $params  = array(
            'day' => $day,
            'csvfilename' => $csvfilename,
            'time' => date("Y-m-d H:i:s", $this->time),
            'cronjobdataimportSettings' => $cronjobdataimportSettings,
            'name' => 'cronjob:import_file'
        );
        print_r($params);

        $this->modelLog = new \App\Cronjob\Models\DataImport\Log();
        $this->modelFile = new \App\Cronjob\Models\DataImport\File();

        // set_time_limit(0);
        foreach ($cronjobdataimportSettings as $settings) {
            $ftpDirectory = $settings[$env]['ftpDirectory'];
            $backupDirectory = $settings[$env]['backupDirectory'];

            // success文件
            $fileFlag = $ftpDirectory . sprintf($settings['successFlagFile'], $day);
            // csv文件
            if (empty($csvfilename)) {
                $fileCsv = $ftpDirectory . sprintf($settings['csvFile'], $day);
            } else {
                $fileCsv = $ftpDirectory . $csvfilename;
            }
            // lock文件
            $fileLock = $ftpDirectory . "lock_" . basename($fileCsv) . ".lock";
            // 文件内容类型
            $content_type = $settings['content_type'];

            if (!file_exists($fileCsv)) {
                echo "{$fileCsv} is not exist\n";
                continue;
            }

            $mode = "a+";

            if (!$handle = fopen($fileLock, $mode)) {
                echo "{$fileLock} cannot open\n";
                continue;
            }

            //$isFileLock = false;
            $isCanDeleteLockFile = false;

            if (flock($handle, LOCK_EX)) { // 进行排它型锁定
                //$isFileLock = true;
                try {
                    // 获取文件的内容
                    $id = fread($handle, 1024);
                    $id = trim($id);
                    // die('file content:' . $id);

                    // 文件内容为空的话
                    if (strlen($id) === 0) {
                        // // 检测文件的success flag文件
                        // if (!file_exists($fileFlag)) {
                        //     throw new \Exception("{$fileFlag} is not exist\n");
                        // }
                        // $content = file_get_contents($fileFlag);
                        // $content = trim($content);

                        // if (empty($content)) {
                        //     throw new \Exception("{$fileFlag}'s {$content} is empty\n");
                        // }

                        // 检查csv文件
                        if (!file_exists($fileCsv)) {
                            throw new \Exception("{$fileCsv} is not exist\n");
                        }
                        $csv = file_get_contents($fileCsv);
                        if (empty($csv)) {
                            throw new \Exception("{$fileCsv} is empty\n");
                        }

                        // 生成日志
                        $cronjobInfo = $this->modelFile->log($this->time, $fileCsv, $fileFlag, $fileLock, time(), 0, 0, \App\Cronjob\Models\DataImport\File::STATUS_INIT, 'uploading', '');

                        // 如果计划任务不存在
                        if (empty($cronjobInfo)) {
                            throw new \Exception("failure to create cronjob\n");
                        }

                        // 将cronjob_id写入lock文件中
                        $cronjob_id = ($cronjobInfo['_id']);
                        if (fwrite($handle, $cronjob_id) === FALSE) {
                            throw new \Exception("{$fileLock} cannot write\n");
                        }
                    } else {
                        // 获取日志
                        $cronjobInfo = $this->modelFile->getInfoById($id);
                    }
                    // 如果计划任务不存在
                    if (empty($cronjobInfo)) {
                        throw new \Exception("id:{$id}对应的cronjob is not exist\n");
                    }

                    // 如果计划任务已经处理完成
                    if ($cronjobInfo['status'] == \App\Cronjob\Models\DataImport\File::STATUS_FINISH) {
                        throw new \Exception("id:{$id}对应的cronjob is success to do\n");
                    }

                    $cronjob_id = ($cronjobInfo['_id']);
                    $fileCsv = $cronjobInfo['data_file'];

                    // 是否需要csv文件上传处理
                    if ($cronjobInfo['status'] < \App\Cronjob\Models\DataImport\File::STATUS_UPLOAD) {
                        $csv = file_get_contents($fileCsv);
                        // csv to array
                        $arr = csv2arr($csv);
                        unset($csv); // 释放内存
                        //print_r($arr);
                        if (empty($arr)) {
                            throw new \Exception("{$fileCsv}'s content is not correct\n");
                        }

                        // 处理ftp上传来的文件
                        $this->dataUpload($content_type, $arr, $cronjobInfo, $settings);
                    }

                    // 再次获取日志
                    $cronjobInfo = $this->modelFile->getInfoById($cronjobInfo['_id']);
                    // 如果计划任务已经上传或完成
                    if ($cronjobInfo['status'] >= \App\Cronjob\Models\DataImport\File::STATUS_UPLOAD) {
                        $isCanDeleteLockFile = true;
                        $fileLock = $cronjobInfo['lock_file'];
                        if (file_exists($fileCsv)) {
                            // 移动文件到备份目录
                            $file1 = basename($fileCsv);
                            copy($fileCsv, $backupDirectory . $file1); //拷贝到新目录
                            unlink($fileCsv); //删除旧目录下的文件
                        }
                    }
                } catch (\Exception $e) {
                    // $this->sendMail($e->getMessage());
                    echo ($e->getMessage());
                }
                flock($handle, LOCK_UN); // 释放锁定
            } else {
                echo "{$fileLock} cannot lock\n";
            }
            fclose($handle);

            if ($isCanDeleteLockFile) {
                if (file_exists($fileLock)) {
                    $file2 = basename($fileLock);
                    copy($fileLock, $backupDirectory . $file2); //拷贝到新目录
                    unlink($fileLock); //删除旧目录下的文件
                }
            }
        }
        echo "OK";
    }

    private function sendMail($err_message)
    {
        try {
            \Mail::raw($err_message, function ($message) {
                $to = array();
                $to[] = 'handsomegyr@126.com';
                $to[] = '30865400@qq.com';
                $to[] = 'sunhui@qiaohu.com';
                $to[] = 'nidandan@qiaohu.com';
                $to[] = 'houshuyuan@qiaohu.com';
                $to[] = 'marutani@qiaohu.com';
                $message->to($to)->subject('积分商城导入订单出错');
            });

            // // 返回的一个错误数组，利用此可以判断是否发送成功
            // dd(\Mail::failures());

            if (count(\Mail::failures()) < 1) {
                echo '发送邮件成功，请查收！';
                return true;
            } else {
                echo '发送邮件失败，错误原因：' . \App\Common\Utils\Helper::myJsonEncode(\Mail::failures());
                return false;
            }
        } catch (\Exception $th) {
            echo '发送邮件失败，错误原因：' . $th->getMessage();
            return false;
        }
    }

    private function dataUpload($content_type, $arr, $cronjobInfo, $settings)
    {
        $modelFileContent = new \App\Cronjob\Models\DataImport\FileContent();
        $cronjob_id = ($cronjobInfo['_id']);
        $this->modelLog->log($cronjob_id, '', 'csv数据导入开始', $cronjob_id, time(), "");

        // 数据上传导入处理 上传导入到订单导入表中
        try {
            $data_num = 0;
            $line_no = 0;

            // DB::beginTransaction();
            $modelFileContent->begin();

            foreach ($arr as $idx => $row) {
                $line_no++;
                // 如果第一行是头
                if ($idx === 0 && !empty($settings['firstrow_is_header'])) {
                    $line_no--;
                    continue;
                }

                //print_r($row);

                // 文件内容
                $content = \App\Common\Utils\Helper::myJsonEncode($row);
                //echo 'content:' . $content . "<br>";

                $rowString = \implode(",", $row);
                $content_sign = \sha1($rowString . "|" . $content_type);
                $cron_time = strtotime($cronjobInfo['cron_time']);

                // 检查是否已经导入了
                $info = $modelFileContent->getInfoByContentSign($content_sign, $cronjob_id, $content_type);

                // 如果没有就记录
                if (empty($info)) {
                    $modelFileContent->log($cron_time, $cronjob_id, $line_no, $content_type, $content, $content_sign, time());
                    $data_num++;
                }
            }

            // 记录需要处理的条数
            $otherIncData = array('data_count' => $data_num);
            $otherUpdateData = array('desc' => 'csv数据导入');
            $this->modelFile->updateStatus($cronjob_id, \App\Cronjob\Models\DataImport\File::STATUS_UPLOAD, time(), $otherIncData, $otherUpdateData);
            // DB::commit();
            $modelFileContent->commit();
            // 记录日志
            $this->modelLog->log($cronjob_id, '', "csv数据导入成功", $cronjob_id, time(), "本次导入数据条数:{$data_num}");
        } catch (\Exception $e) {
            // DB::rollback();
            $modelFileContent->rollback();
            // 记录日志
            $errorMsg = $e->getCode() . ":" . $e->getMessage();
            $this->modelLog->log($cronjob_id, '', "csv数据导入失败", $cronjob_id, time(), "失败原因:[{$errorMsg}]");
        }
    }
}
