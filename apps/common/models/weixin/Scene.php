<?php
namespace Webcms\Common\Models\Weixin;

use Webcms\Common\Models\Base;

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
        $this->setModel(new \Webcms\Common\Models\Mysql\Weixin\Scene());
    }
}