<?php
namespace Webcms\Common\Models\Payment;

use Webcms\Common\Models\Base;

class Log extends Base
{

    const TYPE0 = 0; // 全部
    const TYPE1 = 1; // 充值
    const TYPE2 = 2; // 消费
    const TYPE3 = 3; // 转账
    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Payment\Log());
    }
}