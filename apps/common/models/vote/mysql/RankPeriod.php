<?php

namespace Webcms\Common\Models\Mysql\Vote;

use Webcms\Common\Models\Mysql\Base;

class RankPeriod extends Base
{

    /**
     * 投票-每期排行表管理
     * This model is mapped to the table ivote_rank_period
     */
    public function getSource()
    {
        return 'ivote_rank_period';
    }
}
