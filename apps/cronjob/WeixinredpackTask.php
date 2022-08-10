<?php

class WeixinredpackTask extends \Phalcon\CLI\Task
{

    /**
     * 补发红包
     *
     * @param array $params
     */
    public function createnewperiodgoodsAction(array $params)
    {
        try {
            set_time_limit(0);
            // 补发红包
            $modelService = new \App\Weixinredpack\Services\Api();
            $modelService->isNeedSendRedpack = true;
            $ret = $modelService->cron();
            $errorList = $ret['errorList'];
            $successList = $ret['successList'];
            if (! empty($errorList)) {
                foreach ($errorList as $error) {
                    $e = new \Exception($error['error_msg'], $error['error_code']);
                    $this->modelErrorLog->log($e);
                }
            }
            if (! empty($successList)) {
                $modelRedpacklog = new Service_Model_Redpacklog();
                foreach ($successList as $repacklog) {
                    $amount = $repacklog['total_amount'];
                    $logid = myMongoId($repacklog['_id']);
                    $p4 = $repacklog['memo']['openid'];
                    $custno = $repacklog['memo']['custno'];
                    $FromUserName4Guotai = $repacklog['re_openid'];
                    $redpackInfo = $modelRedpacklog->log($p4, $FromUserName4Guotai, $logid, $amount, $custno);
                }
            }
            
            // 处理
            die('OK');
        } catch (Exception $e) {
            $this->modelErrorLog->log($e);
            die($e->getMessage());
        }
    }
}