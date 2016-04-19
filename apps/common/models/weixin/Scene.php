<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base\Base;

/**
 * 微信二维码场景管理
 *
 * @author young
 *        
 */
class Scene extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin\Mysql\Scene());
    }
}