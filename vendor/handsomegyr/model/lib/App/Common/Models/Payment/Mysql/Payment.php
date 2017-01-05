<?php
namespace App\Common\Models\Payment\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Payment extends Base
{

    /**
     * 支付-支付方式表管理
     * This model is mapped to the table ipayment_payment
     */
    public function getSource()
    {
        return 'ipayment_payment';
    }
    
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['config'] = $this->changeToArray($data['config']);
        return $data;
    }
}