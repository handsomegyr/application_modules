<?php
namespace App\Common\Models\Tencent;

use App\Common\Models\Base\Base;

class OauthInfo extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Tencent\Mysql\OauthInfo());
    }
}
