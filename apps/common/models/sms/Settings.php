<?php
namespace Webcms\Common\Models\Sms;

use Webcms\Common\Models\Base;

class Settings extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Sms\Settings());
    }
}