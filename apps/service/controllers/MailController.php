<?php
namespace Webcms\Service\Controllers;

use Webcms\Message\Models\Template;
use Webcms\Mail\Models\Settings;

class MailController extends ControllerBase
{

    private $messageTemplate = null;

    private $mailSettings = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->messageTemplate = new Template();
        $this->mailSettings = new Settings();
    }

    /**
     * 提供根据邮件模板发送邮件的服务
     */
    public function sendbytemplateAction()
    {
        // http://webcms.didv.cn/service/mail/sendbytemplate?email=handsomegyr@126.com&code=validate_mobile&contents=eyJzaXRlIjoiXHU1YmI2XHU1YjlkXHU3ZjUxXHU3YWQ5IiwidmNvZGUiOiI0NTY3In0=
        try {
            $email = $this->get('email', '');
            $code = $this->get('code', '');
            $contents = $this->get('contents', '');
            $contents = base64_decode($contents);
            
            $tpl_info = $this->messageTemplate->getInfoByCode($code);
            if (! empty($tpl_info)) {
                $param = json_decode($contents, true);
                $subject = ncReplaceText($tpl_info['title'], $param);
                $message = ncReplaceText($tpl_info['content'], $param);
                $ret = $this->mailSettings->sendEmail($email, $subject, $message);
                echo $this->result('OK');
            } else {
                // 记录错误
                echo $this->error(- 1, "code:{$code}对应的模版消息未设置");
            }
            return;
        } catch (\Exception $e) {
            // 记录错误
            echo $this->error($e->getCode(), $e->getMessage());
        }
    }
}

