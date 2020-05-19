<?php

namespace App\Site\Controllers;

/**
 * 网站的服务
 *
 * @author Kan
 *        
 */
class ServiceController extends ControllerBase
{

    protected $modelSuggestion = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelSuggestion = new \App\Site\Models\Suggestion();
    }

    /**
     * 投诉与建议的接口
     * 帮助-投诉建议
     */
    public function suggestAction()
    {
        // http://www.jizigou.com/site/service/suggest?theme=投诉与建议&name=中国&telephone=&email=115454322@qq.com&content=反馈内容反馈内容反馈内容反馈内容反馈内容反馈内容&captcha=xx
        try {
            $theme = urldecode($this->get('theme', ''));
            $name = urldecode($this->get('name', ''));
            $telephone = urldecode($this->get('telephone', ''));
            $email = urldecode($this->get('email', ''));
            $content = urldecode($this->get('content', ''));
            $captcha = trim($this->get('captcha', '')); // captcha

            if (empty($theme)) {
                echo ($this->error(-1, '请选择一个主题'));
                return false;
            }

            if (empty($email)) {
                echo ($this->error(-2, 'Email为空'));
                return false;
            }

            if (empty($content)) {
                echo ($this->error(-3, '内容为空'));
                return false;
            }

            $image = new \Securimage();
            if ($image->check($captcha) == true) {
                $isOk = true;
            } else {
                echo ($this->error(-4, '验证码不正确'));
                return false;
            }

            $this->modelSuggestion->log($theme, $name, $telephone, $email, $content);

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}
