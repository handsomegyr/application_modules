<?php
namespace App\Common\Models\Mysql\Tencent;

use App\Common\Models\Mysql\Base;

class OauthInfo extends Base
{

    /**
     * 腾讯-授权信息表管理
     * This model is mapped to the table itencent_oauthinfo
     */
    public function getSource()
    {
        return 'itencent_oauthinfo';
    }
}