<?php

namespace App\Lexiangla\Controllers;

class ControllerBase extends \App\Common\Controllers\ControllerBase
{

    protected function initialize()
    {
        parent::initialize();
    }

    protected function debugVar()
    {
        ob_start();
        print_r(func_get_args());
        $info = ob_get_contents();
        ob_get_clean();
        return $info;
    }

    /**
     * 转化方法 很重要
     *
     * @param object $object            
     */
    protected function object2array($object)
    {
        // return @json_decode(@\App\Common\Utils\Helper::myJsonEncode($object), 1);
        return @json_decode(preg_replace('/{}/', '""', @\App\Common\Utils\Helper::myJsonEncode($object)), 1);
    }
}
