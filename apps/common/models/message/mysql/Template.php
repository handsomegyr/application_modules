<?php
namespace Webcms\Common\Models\Mysql\Message;

use Webcms\Common\Models\Mysql\Base;

class Template extends Base
{

    /**
     * 消息-模版设置表管理
     * This model is mapped to the table imessage_template
     */
    public function getSource()
    {
        return 'imessage_template';
    }
}