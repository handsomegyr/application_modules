<?php
namespace App\Common\Models\Member;

use App\Common\Models\Base\Base;

class Report extends Base
{
    
    // 举报类型 1钓鱼欺诈 2广告骚扰 3色情暴力 4其他
    const TYPEDATAS = array(
        '1' => array(
            'name' => '钓鱼欺诈',
            'value' => '1'
        ),
        '2' => array(
            'name' => '广告骚扰',
            'value' => '2'
        ),
        '3' => array(
            'name' => '色情暴力',
            'value' => '3'
        ),
        '4' => array(
            'name' => '其他',
            'value' => '4'
        )
    );

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Member\Mysql\Report());
    }
}