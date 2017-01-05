<?php
namespace App\Common\Models\Points\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Rule extends Base
{

    /**
     * 积分-积分规则表管理
     * This model is mapped to the table ipoints_rule
     */
    public function getSource()
    {
        return 'ipoints_rule';
    }
}