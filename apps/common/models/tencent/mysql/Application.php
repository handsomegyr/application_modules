<?php
namespace App\Common\Models\Mysql\Tencent;

use App\Common\Models\Mysql\Base;

class Application extends Base
{

    /**
     * 腾讯-应用设置表管理
     * This model is mapped to the table itencent_application
     */
    public function getSource()
    {
        return 'itencent_application';
    }
}