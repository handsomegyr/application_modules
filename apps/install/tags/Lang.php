<?php
namespace App\Install\Tags;

class Lang extends \Phalcon\Tag
{

    /**
     * 获取语言
     *
     * @return string
     */
    static public function get($lang_key)
    {
        $di = \Phalcon\Di\Di::getDefault();
        $lang = $di->get('lang');
        print_r($lang);
        die('xxx');
        return isset($lang[$lang_key]) ? $lang[$lang_key] : ($force ? $lang_key : '');
    }
}