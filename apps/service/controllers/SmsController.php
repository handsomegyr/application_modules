<?php

namespace App\Service\Controllers;

use App\Message\Models\Template;
use App\Sms\Models\Settings;

class SmsController extends ControllerBase
{

    private $smsSettings = null;

    private $messageTemplate = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->messageTemplate = new Template();
        $this->smsSettings = new Settings();
    }

    /**
     * 提供根据模板发送短信的服务
     */
    public function sendbytemplateAction()
    {
        // http://www.myapplicationmodule.com/service/sms/sendbytemplate?mobile=13564100096&code=validate_mobile&contents=eyJzaXRlIjoiXHU1YmI2XHU1YjlkXHU3ZjUxXHU3YWQ5IiwidmNvZGUiOiI0NTY3In0=
        try {
            $mobile = $this->get('mobile', '');
            $code = $this->get('code', '');
            $contents = $this->get('contents', '');
            $contents = base64_decode($contents);
            // $contents = \App\Common\Utils\Helper::myJsonEncode(array(
            // 'site' => '家宝网站',
            // 'vcode' => '4567'
            // ));
            // die(base64_encode($contents));
            $tpl_info = $this->messageTemplate->getInfoByCode($code);
            if (!empty($tpl_info)) {
                $param = json_decode($contents, true);
                $subject = ncReplaceText($tpl_info['title'], $param);
                $message = ncReplaceText($tpl_info['content'], $param);
                // $message = "【家宝网站】您的验证码是4567";
                $ret = $this->smsSettings->sendSms($mobile, $subject, $message);
                echo $this->result('OK');
            } else {
                // 记录错误
                echo $this->error(-1, "code:{$code}对应的模版消息未设置");
            }
            return;
        } catch (\Exception $e) {
            // 记录错误
            echo $this->error($e->getCode(), $e->getMessage());
        }
    }
}
