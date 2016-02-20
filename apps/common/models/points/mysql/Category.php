<?php
namespace Webcms\Common\Models\Mysql\Points;

use Webcms\Common\Models\Mysql\Base;

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