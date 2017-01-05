<?php
namespace App\Common\Models\Activity\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Category extends Base
{

    /**
     * 活动-活动分类管理
     * This model is mapped to the table iactivity_category
     */
    public function getSource()
    {
        return 'iactivity_category';
    }
}