<?php
namespace App\Message\Models;

class Template extends \App\Common\Models\Message\Template
{

    public function getInfoByCode($code)
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $info = false; // $cache->get($key);
        if (empty($info)) {
            $query = array(
                'code' => $code
            );
            $info = $this->findOne($query);
            if (! empty($info)) {
                $cache->save($key, $info, 60 * 60 * 24); // 24小时
            }
        }
        return $info;
    }

    public function getValidateEmailTemplate($userEmail, $vcode)
    {
        $tpl_info = $this->getInfoByCode('validate_email');
        $param = array(
            'userEmail' => $userEmail,
            'vcode' => $vcode
        );
        $subject = ncReplaceText($tpl_info['title'], $param);
        $content = ncReplaceText($tpl_info['content'], $param);
        
        return array(
            'subject' => $subject,
            'content' => $content
        );
    }

    public function getValidateMobileTemplate($mobile, $vcode)
    {
        $tpl_info = $this->getInfoByCode('validate_mobile');
        $param = array(
            'mobile' => $mobile,
            'vcode' => $vcode
        );
        $subject = ncReplaceText($tpl_info['title'], $param);
        $content = ncReplaceText($tpl_info['content'], $param);
        
        return array(
            'subject' => $subject,
            'content' => $content
        );
    }

    public function getLotteryOkMobileTemplate()
    {
        $tpl_info = $this->getInfoByCode('lottery_ok_mobile');
        $subject = $tpl_info['title'];
        $content = $tpl_info['content'];
        
        return array(
            'subject' => $subject,
            'content' => $content
        );
    }

    public function getLotteryOkEmailTemplate($buyer_name, $goods_id, $goods_name, $goods_period)
    {
        $tpl_info = $this->getInfoByCode('lottery_ok_email');
        $param = array(
            'buyer_name' => $buyer_name,
            'goods_id' => $goods_id,
            'goods_name' => $goods_name,
            'goods_period' => $goods_period
        );
        $subject = ncReplaceText($tpl_info['title'], $param);
        $content = ncReplaceText($tpl_info['content'], $param);
        
        return array(
            'subject' => $subject,
            'content' => $content
        );
    }
    
    public function getLotteryNoTemplate($buyer_name, $goods_id, $goods_name, $goods_period)
    {
        $tpl_info = $this->getInfoByCode('lottery_no');
        $param = array(
            'buyer_name' => $buyer_name,
            'goods_id' => $goods_id,
            'goods_name' => $goods_name,
            'goods_period' => $goods_period
        );
        $subject = ncReplaceText($tpl_info['title'], $param);
        $content = ncReplaceText($tpl_info['content'], $param);
    
        return array(
            'subject' => $subject,
            'content' => $content
        );
    }
}