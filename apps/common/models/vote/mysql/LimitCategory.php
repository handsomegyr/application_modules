<?php
namespace App\Common\Models\Mysql\Vote;

use App\Common\Models\Mysql\Base;

class LimitCategory extends Base
{

    /**
     * 投票-限制类别表管理
     * This model is mapped to the table ivote_limit_category
     */
    public function getSource()
    {
        return 'ivote_limit_category';
    }
}

