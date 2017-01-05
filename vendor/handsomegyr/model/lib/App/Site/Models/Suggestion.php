<?php
namespace App\Site\Models;

class Suggestion extends \App\Common\Models\Site\Suggestion
{

    public function log($theme, $name, $telephone, $email, $content)
    {
        
        // theme:投诉与建议
        // name:中国
        // tell:
        // email:115454322@qq.com
        // message:反馈内容反馈内容反馈内容反馈内容反馈内容反馈内容
        $data = array();
        $data['theme'] = $theme;
        $data['name'] = $name;
        $data['telephone'] = $telephone;
        $data['email'] = $email;
        $data['content'] = $content;
        $data['log_time'] = getCurrentTime();
        
        $this->insert($data);
    }
}