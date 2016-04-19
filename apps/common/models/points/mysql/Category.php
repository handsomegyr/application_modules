<?php
namespace App\Common\Models\Points\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Category extends Base
{

    /**
     * 积分-积分分类表管理
     * This model is mapped to the table ipoints_category
     */
    public function getSource()
    {
        return 'ipoints_category';
    }
}