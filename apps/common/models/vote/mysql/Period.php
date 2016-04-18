<?php

namespace App\Common\Models\Mysql\Vote;

use App\Common\Models\Mysql\Base;

class Period extends Base
{

    /**
     * 投票-排行期表管理
     * This model is mapped to the table ivote_period
     */
    public function getSource()
    {
        return 'ivote_period';
    }
}
