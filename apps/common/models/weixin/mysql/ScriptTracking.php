<?php
namespace Webcms\Common\Models\Mysql\Weixin;

use Webcms\Common\Models\Mysql\Base;

class ScriptTracking extends Base
{

    /**
     * 微信执行时间跟踪统计
     * This model is mapped to the table iweixin_script_tracking
     */
    public function getSource()
    {
        return 'iweixin_script_tracking';
    }
}