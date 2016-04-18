<?php
namespace App\Common\Models\Mysql\Order;

use App\Common\Models\Mysql\Base;

class Statistics extends Base
{

    /**
     * 订单-统计管理
     * This model is mapped to the table iorder_statistics
     */
    public function getSource()
    {
        return 'iorder_statistics';
    }
}