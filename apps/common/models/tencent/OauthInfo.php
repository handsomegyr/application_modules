<?php
namespace App\Common\Models\Tencent;

use App\Common\Models\Base;

class OauthInfo extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Tencent\OauthInfo());
    }
}
