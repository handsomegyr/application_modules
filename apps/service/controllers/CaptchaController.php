<?php
namespace App\Service\Controllers;

class CaptchaController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 提供生成captcha的服务
     */
    public function createAction()
    {
        // http://webcms.didv.cn/service/captcha/create?image_width=100&image_height=100
        $image_width = $this->get('image_width', '0');
        $image_height = $this->get('image_height', '0');
        $options = array(
            'code_length' => 4
        // 'captcha_type' => \Securimage::SI_CAPTCHA_MATHEMATIC
                );
        $img = new \Securimage($options);
        if (! empty($image_width)) {
            $img->image_width = $image_width;
        }
        if (! empty($image_height)) {
            $img->image_height = $image_height;
        }
        $img->show();
        exit();
    }
}

