<?php
namespace App\Mail\Models;

class Settings extends \App\Common\Models\Mail\Settings
{

    /**
     * 获取邮件设置
     *
     * @return array
     */
    public function getSettings()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $settings = false; // $cache->get($key);
        if (empty($settings)) {
            $settings = array();
            $list = $this->find(array(), array(
                '_id' => - 1
            ), 0, 1);
            
            if (! empty($list['datas'])) {
                $settings = $list['datas'][0];
                $cache->save($key, $settings, 60 * 60 * 24); // 24小时
            }
        }
        return $settings;
    }
    
    public function sendEmail($email, $subject, $body)
    {
        $mailSettings = $this->getSettings();
        $mail = new \PHPMailer(true);
        $mail->CharSet = "UTF8"; // 这里指定字符集！
        $mail->Encoding = "base64";
        // $mail->SMTPDebug = 3; // Enable verbose debug output
        if (! empty($mailSettings['is_smtp'])) {
            $mail->isSMTP(); // Set mailer to use SMTP
        }
        $mail->Host = $mailSettings['host']; // 'smtp1.example.com;smtp2.example.com'; // Specify main and backup SMTP servers
        if (! empty($mailSettings['is_auth'])) {
            $mail->SMTPAuth = true; // Enable SMTP authentication
        }
    
        $mail->Username = $mailSettings['username']; // 'user@example.com'; // SMTP username
        $mail->Password = $mailSettings['password']; // 'secret'; // SMTP password
        if (! empty($mailSettings['secure'])) {
            $mail->SMTPSecure = $mailSettings['secure']; // 'tls'; // Enable TLS encryption, `ssl` also accepted
        }
        $mail->Port = $mailSettings['port']; // TCP port to connect to
    
        $mail->setFrom($mailSettings['username'], $mailSettings['name_from']); // $mail->setFrom('from@example.com', 'Mailer');
         
        // $mail->addAddress('joe@example.net', 'Joe User'); // Add a recipient
        $mail->addAddress($email); // Name is optional
         
        // $mail->addReplyTo('info@example.com', 'Information');
         
        // $mail->addCC('cc@example.com');
         
        // $mail->addBCC('bcc@example.com');
         
        // $mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments
         
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name
    
        $mail->isHTML(true); // Set email format to HTML
    
        $mail->Subject = $subject;
        $mail->Body = $body;
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->send();
    }
}