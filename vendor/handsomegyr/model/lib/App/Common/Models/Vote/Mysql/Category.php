<?php
namespace App\Common\Models\Vote\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Category extends Base
{

    /**
     * 投票-类型表管理
     * This model is mapped to the table ivote_category
     */
    public function getSource()
    {
        return 'ivote_category';
    }
}

