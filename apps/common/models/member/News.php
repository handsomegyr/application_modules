<?php
namespace Webcms\Common\Models\Member;

use Webcms\Common\Models\Base;

class News extends Base
{
    const ACTION1 = 1; // 购买
    const ACTION2 = 2; // 晒单
    
    // 动态操作 1 购买 2 晒单
    const ACTIONDATAS = array(
        '1' => array(
            'name' => '购买',
            'value' => '1'
        ),
        '2' => array(
            'name' => '晒单',
            'value' => '2'
        )
    );
    
    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Member\News());
    }
}